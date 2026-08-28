<?php

declare(strict_types=1);

if (filter_var(getenv('APP_DEBUG') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

$dir = __DIR__;
while ($dir !== dirname($dir)) {
    $autoload = $dir . '/vendor/autoload.php';
    if (is_file($autoload)) {
        require $autoload;

        return;
    }
    $dir = dirname($dir);
}

throw new RuntimeException('vendor/autoload.php not found. Run composer require in the project root.');
