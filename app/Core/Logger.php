<?php

namespace App\Core;

class Logger {
    public static function log($message, $level = 'info') {
        try {
            $logDir = ROOT_PATH . '/storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }

            $file = $logDir . '/' . date('Y-m-d') . '.log';
            $timestamp = date('Y-m-d H:i:s');
            $formattedMessage = "[$timestamp] [$level] $message" . PHP_EOL;

            @file_put_contents($file, $formattedMessage, FILE_APPEND);
        } catch (\Throwable $e) {
            // Logger must never crash the app
        }
    }

    public static function info($message) {
        self::log($message, 'info');
    }

    public static function error($message) {
        self::log($message, 'error');
    }

    public static function debug($message) {
        self::log($message, 'debug');
    }

    public static function warning($message) {
        self::log($message, 'warning');
    }
}
