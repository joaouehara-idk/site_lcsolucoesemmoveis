<?php

namespace App\Core;

class ExceptionHandler {
    public static function handle($exception) {
        $msg = $exception->getMessage();
        $trace = $exception->getTraceAsString();
        $file = $exception->getFile();
        $line = $exception->getLine();

        // Log sem depender de Logger (Logger pode ser o causador do crash)
        $logDir = ROOT_PATH . '/storage/logs';
        if (is_dir($logDir) || @mkdir($logDir, 0777, true)) {
            $logFile = $logDir . '/' . date('Y-m-d') . '.log';
            $ts = date('Y-m-d H:i:s');
            @file_put_contents($logFile, "[$ts] [FATAL] $msg in $file:$line\n$trace\n\n", FILE_APPEND);
        }

        // Sempre retorna 500 e mostra mensagem amigável
        http_response_code(500);

        $debug = $_SERVER['APP_DEBUG'] ?? 'false';
        if ($debug === 'true') {
            echo "<h1>Exception</h1>";
            echo "<p><strong>" . htmlspecialchars($msg) . "</strong></p>";
            echo "<p>Arquivo: " . htmlspecialchars($file) . ":" . $line . "</p>";
            echo "<pre>" . htmlspecialchars($trace) . "</pre>";
        } else {
            echo "<!DOCTYPE html><html><head><title>Erro</title>";
            echo "<style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#F6F1EB;color:#1A1714;}";
            echo ".box{text-align:center;padding:40px;}.box h1{font-size:18px;margin-bottom:10px;}.box p{color:#8A8580;font-size:14px;}";
            echo ".box a{color:#1A1714;text-decoration:underline;font-size:14px;}</style></head><body>";
            echo "<div class='box'><h1>Algo deu errado</h1>";
            echo "<p>Desculpe, ocorreu um erro interno. Por favor, tente novamente mais tarde.</p>";
            echo "<a href='" . ($_SERVER['BASE_URL'] ?? '/') . "/login'>Voltar ao Login</a></div></body></html>";
        }
        exit;
    }
}
