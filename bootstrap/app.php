<?php

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handel Validation Exceptions
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error($e->errors(), 'Validation Error', 422);
            }
        });


        // Handel all other exceptions
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {

                $statusCode = $e instanceof HttpException ? $e->getStatusCode() : 500;

                $messages = [
                    404 => 'The resource not found.',
                    403 => 'Access denied. You don\'t have permission.',
                    401 => 'You must be logged in to access this resource.',
                    500 => 'Something went wrong. Please try again later.'
                ];


                $message = app()->environment('local')
                    ? $e->getMessage()
                    : ($messages[$statusCode] ?? 'Something went wrong. Please try again.');

                return ApiResponse::error(
                    [],
                    $message,
                    $statusCode
                );
            }
        });
    })->create();
