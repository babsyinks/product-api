<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     */
    protected $levels = [];

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

public function register(): void
{
    $this->renderable(function (AuthenticationException $e, Request $request) {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated. Please provide a valid Bearer token.',
            ], 401);
        }
    });
}

    /**
     * Render all exceptions as consistent JSON for API routes.
     */
    public function render($request, Throwable $e): mixed
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->renderApiException($e);
        }

        return parent::render($request, $e);
    }

    private function renderApiException(Throwable $e): JsonResponse
    {
        // 422 Validation errors
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }

        // 401 Unauthenticated
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated. Please provide a valid Bearer token.',
            ], 401);
        }

        // 403 Authorisation
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => 'This action is unauthorized.',
            ], 403);
        }

        // 404 Model not found
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            return response()->json([
                'message' => "{$model} not found.",
            ], 404);
        }

        // Generic HTTP exceptions
        if ($e instanceof HttpException) {
            return response()->json([
                'message' => $e->getMessage() ?: 'HTTP error.',
            ], $e->getStatusCode());
        }

        // 500 fallback
        $message = config('app.debug')
            ? $e->getMessage()
            : 'An unexpected error occurred. Please try again later.';

        return response()->json(['message' => $message], 500);
    }
}
