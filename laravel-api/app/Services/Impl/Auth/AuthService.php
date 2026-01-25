<?php

namespace App\Services\Impl\Auth;

use App\Enums\Common;
use App\Exceptions\SecurityException;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use App\Repositories\CsrfTokenRepository;
use App\Repositories\RefreshTokenRepository;
use App\Traits\HasTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use App\Repositories\User\UserRepository;
use App\Services\Interfaces\EmailVerification\EmailVerificationServiceInterface;

class AuthService implements AuthServiceInterface
{
    use HasTransaction;

    private const ACCESS_TOKEN_TIME_TO_LIVE = 60 * 24 * 15; // phút (1 giờ mặc định) đang dev nên để 15 ngày
    private const ACCESS_TOKEN_TIME_TO_LIVE_REMEMBER = 60 * 24 * 15; // 15 ngày
    private const REFRESH_TOKEN_TIME_TO_LIVE = 15; // ngày mặc định
    private const REFRESH_TOKEN_TIME_TO_LIVE_REMEMBER = 30; // ngày nếu remember

    private $auth;
    private $accessToken; // token truy cập
    private $refreshToken;
    private $csrfToken;
    private $refreshTokenRepository;
    private $csrfTokenRepository;
    private $userRepository;
    private $refreshTokenModel;
    private $emailVerificationService;


    public function __construct(
        RefreshTokenRepository $refreshTokenRepository,
        CsrfTokenRepository $csrfTokenRepository,
        UserRepository $userRepository,
        EmailVerificationServiceInterface $emailVerificationService
    ) {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->csrfTokenRepository = $csrfTokenRepository;
        $this->userRepository = $userRepository;
        $this->emailVerificationService = $emailVerificationService;
    }

    private function getAuth(string $guard)
    {
        return auth($guard);
    }

    private function getCookieName(string $guard): string
    {
        return $guard === 'admin' ? 'adminRefreshToken' : 'refreshToken';
    }

    public function authenticate(AuthRequest $request, string $guard = 'api')
    {
        $rememberMe = $request->boolean('remember_me', false);
        try {
            // Validate Admin Role if guard is admin
            if ($guard === 'admin') {
                // Pre-check can be done here or after attempt. After attempt is safer for credentials check first.
            }

            return $this->beginTransaction()
                ->generateAccessToken($request, $guard, $rememberMe)
                ->generateRefreshToken($guard, $rememberMe)
                ->generateCsrfToken($guard)
                ->commit()
                ->authResponse($guard);
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            return $this->beginTransaction()
                ->prepareVerification($request)
                ->commit()
                ->registerResponse();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    private function prepareVerification(RegisterRequest $request): self
    {
        $payload = [
            'name' => (string)$request->string('name'),
            'password' => (string)$request->string('password'),
        ];
        $result = $this->emailVerificationService->sendVerificationEmail(
            (string)$request->string('email'),
            (string)$request->string('name'),
            $payload
        );
        if (!$result['success']) {
            throw new \Exception($result['message']);
        }
        return $this;
    }

    private function generateAccessToken($request, string $guard, bool $rememberMe = false): self
    {
        $auth = $this->getAuth($guard);
        $ttl = $rememberMe ? self::ACCESS_TOKEN_TIME_TO_LIVE_REMEMBER : self::ACCESS_TOKEN_TIME_TO_LIVE;
        $auth->setTTL($ttl);
        $auth->claims(['guard' => $guard]);

        if ($request instanceof RegisterRequest) {
            $this->accessToken = $auth->fromUser($auth->user());
        } else {
            $credentials = [
                'email' => $request->string('email'),
                'password' => $request->string('password')
            ];
            
            if (!$accessToken = $auth->attempt($credentials)) {
                throw new AuthenticationException(Lang::get('message.auth_failed'));
            }

            // Check Admin Role
            if ($guard === 'admin') {
                $user = $auth->user();
                if (!$user->is_admin) { // Using the accessor
                    $auth->logout();
                    throw new AuthenticationException(Lang::get('message.permission_denied'));
                }
            }
            
            // Block Admin on Client (User) login
            if ($guard === 'api') {
                $user = $auth->user();
                if ($user->is_admin) {
                     $auth->logout();
                     throw new AuthenticationException(Lang::get('message.permission_denied_admin_on_client'));
                }
            }

            $this->accessToken = $accessToken;
        }
        
        return $this;
    }

    private function generateRefreshToken(string $guard, bool $rememberMe = false): self
    {
        $auth = $this->getAuth($guard);
        $expiresAt = $rememberMe ? now()->addDays(self::REFRESH_TOKEN_TIME_TO_LIVE_REMEMBER) : now()->addDays(self::REFRESH_TOKEN_TIME_TO_LIVE);
        $payload = [
            'refresh_token' => Str::uuid(),
            'user_id' => $auth->user()->id,
            'expires_at' => $expiresAt,
            // We might want to store which guard this token is for if we want STRICT database separation,
            // but for now, we just rely on the cookie holding it.
        ];
        if (!$refreshToken = $this->refreshTokenRepository->create($payload)) {
            throw new ModelNotFoundException(Lang::get('message.model_not_found'));
        }

        $this->refreshToken = $refreshToken->refresh_token;
        return $this;
    }

    private function generateCsrfToken(string $guard): self
    {
        $auth = $this->getAuth($guard);
        $payload = [
            'csrf_token' => Str::uuid(),
            'user_id' => $auth->user()->id,
        ];

        // Ensure we don't wipe out tokens for other sessions if possible, but here it deletes by user_id
        // which might be an issue if User and Admin are same ID (same table). 
        // Ideally we should distinguish or just allow it (logging in as one invalidates the other's CSRF?).
        // For now, let's keep it simple. If I log in as Admin, I might invalidate my User session's CSRF if they share strict checking.
        // User asked for "Separate".
        // If we delete by UserID, we kill both sessions if they are the same user.
        // Let's NOT delete all tokens, or maybe filter?
        // Repository pattern might need update to delete only for this context if we stored context.
        // However, CSRF token is usually per session/request. 
        // For clean separation, maybe we shouldn't delete *all* tokens but strictly the one we are replacing?
        // But we are creating a new one. The previous implementation deleted *all* tokens for that user.
        // This implies single session per user.
        // If we want Admin and User concurrent login for same account, we shouldn't delete ALL.
        // But maybe we SHOULD delete all to prevent stale tokens.
        // Let's stick to current logic (delete all) -> effectively creates single session enforcement PER USER ID.
        // To allow both, we need to NOT delete all.
        // I will Temporarily COMMENT OUT deleteCsrfTokenByUserid or ensure it's correct.
        // Actually, if I am Admin and I login, I get a CSRF token. If I am User, I get another. 
        // If both stored in DB under user_id... retrieving one by token string is fine (`findByCsrfToken`).
        // But `deleteCsrfTokenByUserid` wipes them.
        // FIX: Only delete EXPIRED tokens or old tokens?
        // Since I can't easily change the repo right now without more files, I will leave the delete.
        // NOTE: This means logging in as Admin *might* log you out of Client if logic checks DB existence of other tokens.
        // But `checkHeaderToken` checks if `X-CSRF` exists in DB.
        // So yes, logging in as Admin will invalidate User session if they share the same User ID.
        // The user said: "k lẻ admin mỗi lần vào phải đăng nhập lại ... còn riêng user thì có remember".
        // They WANT simultaneous login.
        // So I should STOP deleting all tokens. Just create a new one.
        
        // $this->csrfTokenRepository->deleteCsrfTokenByUserid($auth->user()->id); // CHANGED: Don't delete all.
        
        if (!$csrfToken = $this->csrfTokenRepository->create($payload)) {
            throw new ModelNotFoundException(Lang::get('message.model_not_found'));
        }

        $this->csrfToken = $csrfToken->csrf_token;
        return $this;
    }

    private function authResponse(string $guard): array
    {
        $auth = $this->getAuth($guard);
        $cookieName = $this->getCookieName($guard);
        
        return [
            'data' => [
                'accessToken' => $this->accessToken,
                'tokenType' => 'Bearer ',
                'expireAt' => $auth->factory()->getTTL() * 60,
                'csrfToken' => $this->csrfToken,
                'user' => new UserResource($auth->user()),
            ],
            'authCookie' => Cookie::make($cookieName, $this->refreshToken, self::REFRESH_TOKEN_TIME_TO_LIVE * 24 * 60, '/', null, null, true, false, 'Lax'),
        ];
    }

    /**
     * Response cho đăng ký (chưa xác thực email)
     */
    private function registerResponse(): array
    {
        return [
            'data' => [
                'message' => 'Xác thực thông tin. Vui lòng kiểm tra hộp thư mail!',
                'email_verification_required' => true
            ]
        ];
    }

    public function getMe(string $guard = 'api')
    {
        $auth = $this->getAuth($guard);
        if (!$user = $auth->user()) {
            throw new UserNotDefinedException(Lang::get('message.user_not_found'));
        }
        return new UserResource($user);
    }

    public function refreshToken(Request $request, string $guard = 'api')
    {
        try {
            return $this->beginTransaction()
                ->checkHeaderToken($request, $guard)
                ->checkRefreshToken()
                ->generateNewAccessToken($guard)
                ->generateRefreshToken($guard) // Generates new, but what about old?
                // Logic flow: checkRefreshToken checks if old was reused.
                // We generate new refresh token.
                ->generateCsrfToken($guard)
                ->commit()
                ->authResponse($guard);
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    private function checkHeaderToken(Request $request, string $guard): self
    {
        if (!$request->hasHeader('X-CSRF-TOKEN')) {
            throw new NotFoundHttpException(Lang::get('message.csrf_token_not_found'));
        }
        if (!$this->csrfTokenRepository->findByCsrfToken($request->header('X-CSRF-TOKEN'))) {
            throw new NotFoundHttpException(Lang::get('message.model_not_found'));
        }

        $cookieName = $this->getCookieName($guard);
        $refreshToken = $request->cookie($cookieName);
        
        if (!$refreshToken) {
             throw new ModelNotFoundException(Lang::get('message.token_not_found') . ' (Cookie missing)');
        }

        if (!$result = $this->refreshTokenRepository->findValidRefreshToken($refreshToken)) {
            throw new ModelNotFoundException(Lang::get('message.model_not_found'));
        }
        $this->refreshTokenModel = $result;
        return $this;
    }

    private function checkRefreshToken(): self
    {
        if ($this->checkRefreshTokenRuese($this->refreshTokenModel)) {
            $this->refreshTokenRepository->revokeAllUserRefreshToken($this->refreshTokenModel->user_id);
            $this->commit();
            Log::warning(Lang::get('message.refresh_token_reused'), [
                'refreshToken' => $this->refreshTokenModel->refresh_token,
                'user_id' => $this->refreshTokenModel->user_id,
                'ip' => request()->ip(),
                'timestamp' => now(),
            ]);
            throw new SecurityException(Lang::get('message.permission_denied'));
        }
        return $this;
    }

    private function generateNewAccessToken(string $guard): self
    {
        $auth = $this->getAuth($guard);
        $this->refreshTokenModel->update(['was_used' => 1]);
        $user = $this->userRepository->findById($this->refreshTokenModel->user_id);
        $auth->setTTL((self::ACCESS_TOKEN_TIME_TO_LIVE));
        $auth->claims(['guard' => $guard]);
        $this->accessToken = $auth->login($user);

        return $this;
    }

    private function checkRefreshTokenRuese($refreshToken = null): bool
    {
        if ($refreshToken->was_used || $refreshToken->is_revoked) {
            return true;
        }
        return false;
    }
    
    public function logout(string $guard = 'api'): void
    {
        $auth = $this->getAuth($guard);
        // xóa refresh token logic if needed?
        $auth->logout();
        $auth->invalidate(true); 
    }
}
