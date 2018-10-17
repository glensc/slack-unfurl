<?php

use SlackUnfurl\Application;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

(new Dotenv())->load(__DIR__ . '/.env');

$config = [
    'appDir' => __DIR__,
    'env' => $_SERVER['APP_ENV'] ?? 'dev',
];

return new Application($config);
