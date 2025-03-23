<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Constants\ResponseMessage;
use App\Constants\HttpCode;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class Handler extends ExceptionHandler
{
    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception): JsonResponse
    {
        if ($exception instanceof RuntimeException) {
            return response()->json([
                'message' => ResponseMessage::INTERNAL_SERVER_ERROR,
                'error' => $exception->getMessage()
            ], $exception->getCode() ?: HttpCode::INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
