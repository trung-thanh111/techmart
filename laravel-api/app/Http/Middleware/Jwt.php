<?php

namespace App\Http\Middleware;

use App\Http\Resources\ApiResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Jwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (empty($guards)) {
            $guards = ['api'];
        } else {
            // Hỗ trợ cả cú pháp jwt:admin,api lẫn jwt:admin
            $guards = array_unique(explode(',', implode(',', $guards)));
        }

        try {
            $token = $request->bearerToken();

            if (!$token) {
                return ApiResource::message(Lang::get('message.access_token_not_found'), Response::HTTP_UNAUTHORIZED);
            }

            JWTAuth::setToken($token);

            $payload = JWTAuth::getPayload();
            $tokenGuard = $payload->get('guard');

            if (!in_array($tokenGuard, $guards)) {
                return ApiResource::message(Lang::get('message.token_invalid'), Response::HTTP_UNAUTHORIZED);
            }
            if (!auth($tokenGuard)->check()) {
                JWTAuth::authenticate();
            }
        } catch (TokenInvalidException $e) {
            return ApiResource::message(Lang::get('message.token_invalid'), Response::HTTP_UNAUTHORIZED);
        } catch (TokenExpiredException $e) {
            return ApiResource::message(Lang::get('message.token_expired'), Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $next($request);
    }
}
