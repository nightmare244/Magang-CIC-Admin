<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Laravel 11 secara otomatis memuat HandleCors jika config/cors.php ada.
        // Kita tidak perlu menambahkannya secara manual di 'use' atau 'prependToGroup'
        // karena hal tersebut sering memicu error "Multiple CORS header".

        $middleware->alias([
            'is.admin' => IsAdmin::class,
        ]);

        // Memastikan request dari SPA (Vue/React) diperlakukan sebagai stateful
        // Ini penting agar middleware Sanctum bisa membaca session/cookie jika diperlukan
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        /**
         * [PENYEMPURNAAN] Mencegah Error CSP (Content Security Policy)
         * Jika terjadi error 401 atau 500 pada request API, Laravel akan 
         * otomatis mengirim JSON, bukan halaman HTML debug.
         */
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })
    ->create();