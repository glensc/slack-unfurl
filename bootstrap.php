<?php

use Eventum\SlackUnfurl\Application;

require_once __DIR__ . '/vendor/autoload.php';

$config = [
    'env' => getenv('APP_ENV') ?: 'prod',
];

return new Application($config);