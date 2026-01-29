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

    private function getCookieName(): string
    {
        return 'refreshToken';
    }

    public function authenticate(AuthRequest $request, string $guard = 'api')
    {
        $rememberMe = $request->boolean('remember_me', false);
        try {
            // Validate Admin Role if guard is admin
            if ($guard === 'admin') {
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
            'authCookie' => Cookie::make($cookieName, $this->refreshToken, self::REFRESH_TOKEN_TIME_TO_LIVE * 24 * 60, '/', null, true, true, false, 'Lax'),
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

    public function logout(string $guard = 'api'): \Symfony\Component\HttpFoundation\Cookie
    {
        $auth = $this->getAuth($guard);
        $cookieName = $this->getCookieName();
        $auth->logout();
        $auth->invalidate(true);

        return Cookie::make($cookieName, null, -1, '/', null, true, true, false, 'Lax');
    }
}
