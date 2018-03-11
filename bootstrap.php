<?php

use SlackUnfurl\Application;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

(new Dotenv())->load(__DIR__ . '/.env');

$config = [
    'env' => $_SERVER['APP_ENV'] ?? 'dev',
];

$app = new Application($config);

return $app;
