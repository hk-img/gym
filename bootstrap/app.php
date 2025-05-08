<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\EvaluateType;
use App\Http\Middleware\RevalidateBackHistory;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verify_admin' => AuthMiddleware::class,
            'revalidate' => RevalidateBackHistory::class,
            'check_status' => CheckStatus::class,
        ]);

        // $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));
        $middleware->redirectGuestsTo(function (Request $request) {
            // Check if the request path starts with 'admin'
            if (str_starts_with($request->path(), 'admin')) {
                return route('admin.login');
            } else {
                return route('home');
            }
        });

        $middleware->redirectUsersTo(function (Request $request) {
            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // dd($exceptions);
    })->create();
