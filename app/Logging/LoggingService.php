<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class LoggingService
{
    public static function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    public static function request(string $type, array $payload): void
    {
        self::info("[$type] Request Payload", ['payload' => $payload]);
    }

    public static function response(string $type, array $response): void
    {
        self::info("[$type] API Response", ['response' => $response]);
    }
}
