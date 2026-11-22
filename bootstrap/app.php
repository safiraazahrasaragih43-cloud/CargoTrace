<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CSRF token mismatch or session expired.',
                    ], 419);
                }

                $referer = $request->headers->get('referer');
                $target = $referer ?: route('login');

                return redirect($target)
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with('error', __('Sesi halaman Anda telah kedaluwarsa karena lama tidak aktif. Token keamanan telah disegarkan otomatis, silakan coba lagi.'));
            }

            return null;
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSRF token mismatch or session expired.',
                ], 419);
            }

            $referer = $request->headers->get('referer');
            $target = $referer ?: route('login');

            return redirect($target)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', __('Sesi halaman Anda telah kedaluwarsa karena lama tidak aktif. Token keamanan telah disegarkan otomatis, silakan coba lagi.'));
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Forbidden: insufficient permissions.',
                ], 403);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP method not allowed.',
                ], 405);
            }

            // Never leak stack traces in REST API error payloads
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred.',
            ], 500);
        });
    })->create();
