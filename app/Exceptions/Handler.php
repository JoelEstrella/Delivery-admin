<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if (
            $exception instanceof UnauthorizedException ||
            $exception instanceof AuthorizationException ||
            $exception instanceof AccessDeniedHttpException ||
            ($exception instanceof HttpException && $exception->getStatusCode() === 403)
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage() ?: 'No tienes permisos para realizar esta acción.'
                ], 403);
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'No tienes permiso para acceder a este módulo.');
        }

        return parent::render($request, $exception);
    }
}
