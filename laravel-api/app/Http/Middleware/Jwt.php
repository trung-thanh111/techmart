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
    public function handle(Request $request, Closure $next, $guard = 'api'): Response
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                $token = $request->cookie('refreshToken');
            }

            if (!$token) {
                return ApiResource::message(Lang::get('message.access_token_not_found'), Response::HTTP_UNAUTHORIZED);
            }
            JWTAuth::setToken($token);

            $payload = JWTAuth::getPayload();
            if ($payload->get('guard') !== $guard) {
                return ApiResource::message(Lang::get('message.token_invalid'), Response::HTTP_UNAUTHORIZED);
            }
            if (!auth($guard)->check()) {
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
