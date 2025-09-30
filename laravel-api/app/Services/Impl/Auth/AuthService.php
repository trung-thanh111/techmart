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
        /**
         * @var \Tymon\JwtAuth\JwtGuard // khai báo php doc cho setTTL()
         */

        $this->auth = auth(Common::API);
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->csrfTokenRepository = $csrfTokenRepository;
        $this->userRepository = $userRepository;
        $this->emailVerificationService = $emailVerificationService;
    }

    public function authenticate(AuthRequest $request)
    {
        $rememberMe = $request->boolean('remember_me', false);
        try {
            return $this->beginTransaction()
                ->generateAccessToken($request, $rememberMe)
                ->generateRefreshToken($rememberMe)
                ->generateCsrfToken()
                ->commit()
                ->authResponse();
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

    // trả về self - dùng chung cho cả login và register
    private function generateAccessToken($request, bool $rememberMe = false): self
    {
        $ttl = $rememberMe ? self::ACCESS_TOKEN_TIME_TO_LIVE_REMEMBER : self::ACCESS_TOKEN_TIME_TO_LIVE;
        $this->auth->setTTL($ttl); // set thời gian sống token theo phút
        $this->auth->claims(['guard' => Common::API]); // phân biệt loại auth api đăng nhập như admin, vv..

        // Nếu là register, user đã được set trong createNewUser
        if ($request instanceof RegisterRequest) {
            $this->accessToken = $this->auth->fromUser($this->auth->user());
        } else {
            // Nếu là login, sử dụng attempt
            $credentials = [
                'email' => $request->string('email'),
                'password' => $request->string('password')
            ];
            
            if (!$accessToken = $this->auth->attempt($credentials)) {
                throw new AuthenticationException(Lang::get('message.auth_failed'));
            }
            $this->accessToken = $accessToken;
        }
        
        return $this; // return chính nó
    }

    private function generateRefreshToken(bool $rememberMe = false): self
    {
        $expiresAt = $rememberMe ? now()->addDays(self::REFRESH_TOKEN_TIME_TO_LIVE_REMEMBER) : now()->addDays(self::REFRESH_TOKEN_TIME_TO_LIVE);
        $payload = [
            'refresh_token' => Str::uuid(),
            'user_id' => $this->auth->user()->id,
            'expires_at' => $expiresAt,
        ];
        if (!$refreshToken = $this->refreshTokenRepository->create($payload)) {
            throw new ModelNotFoundException(Lang::get('message.model_not_found'));
        }

        $this->refreshToken = $refreshToken->refresh_token;
        return $this;
    }

    private function generateCsrfToken(): self
    {
        $payload = [
            'csrf_token' => Str::uuid(),
            'user_id' => $this->auth->user()->id,
        ];

        $this->csrfTokenRepository->deleteCsrfTokenByUserid($payload['user_id']);
        if (!$csrfToken = $this->csrfTokenRepository->create($payload)) {
            throw new ModelNotFoundException(Lang::get('message.model_not_found'));
        }

        $this->csrfToken = $csrfToken->csrf_token;
        return $this;
    }

    private function authResponse(): array
    {
        return [
            'data' => [
                'accessToken' => $this->accessToken,
                'tokenType' => 'Bearer ',
                'expireAt' => $this->auth->factory()->getTTL() * 60,
                'csrfToken' => $this->csrfToken,
                'user' => new UserResource($this->auth->user()),
            ],
            'authCookie' => Cookie::make('refreshToken', $this->refreshToken, self::REFRESH_TOKEN_TIME_TO_LIVE * 24 * 60, '/', null, null, true, false, 'Lax'),
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

    public function getMe()
    {
        if (!$user = $this->auth->user()) {
            throw new UserNotDefinedException(Lang::get('message.user_not_found'));
        }
        return new UserResource($user);
    }

    public function refreshToken(Request $request)
    {
        try {
            return $this->beginTransaction()
                ->checkHeaderToken($request)
                ->checkRefreshToken()
                ->generateNewAccessToken()
                ->generateRefreshToken()
                ->generateCsrfToken()
                ->commit()
                ->authResponse();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    private function checkHeaderToken(Request $request): self
    {
        if (!$request->hasHeader('X-CSRF-TOKEN')) {
            throw new NotFoundHttpException(Lang::get('message.csrf_token_not_found'));
        }
        if (!$this->csrfTokenRepository->findByCsrfToken($request->header('X-CSRF-TOKEN'))) {
            throw new NotFoundHttpException(Lang::get('message.model_not_found'));
        }

        $refreshToken = $request->cookie('refreshToken');
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

    private function generateNewAccessToken(): self
    {
        $this->refreshTokenModel->update(['was_used' => 1]);
        $user = $this->userRepository->findById($this->refreshTokenModel->user_id);
        $this->auth->setTTL((self::ACCESS_TOKEN_TIME_TO_LIVE));
        $this->auth->claims(['guard' => Common::API]);
        $this->accessToken = $this->auth->login($user);

        return $this;
    }

    private function checkRefreshTokenRuese($refreshToken = null): bool
    {
        if ($refreshToken->was_used || $refreshToken->is_revoked) {
            return true;
        }
        return false;
    }
    // phương thức này k trả lại già cả -> void
    public function logout(): void
    {
        // xóa refresh token
        $this->auth->logout();
        $this->auth->invalidate(true); // vô hiệu hóa accessToken
    }
}
