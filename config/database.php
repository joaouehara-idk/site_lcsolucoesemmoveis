<?php

return [
    'host'    => $_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? 'localhost',
    'port'    => $_SERVER['DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
    'db'      => $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? '',
    'user'    => $_SERVER['DB_USER'] ?? $_ENV['DB_USER'] ?? '',
    'pass'    => $_SERVER['DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
    'charset' => $_SERVER['DB_CHARSET'] ?? $_ENV['DB_CHARSET'] ?? 'utf8mb4',
];
