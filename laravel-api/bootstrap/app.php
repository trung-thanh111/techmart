<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\ConvertCamelCaseToSnakeCase;
use App\Http\Middleware\Jwt;
use App\Http\Resources\ApiResource;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt' => Jwt::class,
            'convert' => ConvertCamelCaseToSnakeCase::class,
            'checkPermission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // xử lý 2 trường hợp exceptions
        $exceptions->render(function(NotFoundHttpException $e, Request $request){
            return ApiResource::message($e->getMessage(), Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function(\Throwable $th, Request $request){
            Log::error(Lang::get('message.type_exception'). get_class($th));
            Log::error(Lang::get('message.notification_exception'). $th->getMessage());
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
