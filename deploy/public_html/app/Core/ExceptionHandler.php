<?php

namespace App\Core;

class ExceptionHandler {
    public static function handle($exception) {
        Logger::error($exception->getMessage() . "\n" . $exception->getTraceAsString());

        $debug = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'false';
        if ($debug === 'true') {
            echo "<h1>Exception</h1>";
            echo "<p>" . $exception->getMessage() . "</p>";
            echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        } else {
            http_response_code(500);
            echo "<h1>Algo deu errado</h1>";
            echo "<p>Desculpe, ocorreu um erro interno. Por favor, tente novamente mais tarde.</p>";
        }
        exit;
    }
}
