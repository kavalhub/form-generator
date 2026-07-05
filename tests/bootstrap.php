<?php
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

$packageNamespaces = [
    'Kavalhub\\FormGenerator\\Twig\\' => $root . '/packages/twig/src/',
    'Kavalhub\\Tests\\FormGenerator\\Twig\\' => $root . '/packages/twig/tests/',
    'Kavalhub\\FormGenerator\\Tailwind\\' => $root . '/packages/tailwind/src/',
    'Kavalhub\\Tests\\FormGenerator\\Tailwind\\' => $root . '/packages/tailwind/tests/',
];

spl_autoload_register(static function (string $class) use ($packageNamespaces): void {
    foreach ($packageNamespaces as $prefix => $base) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $file = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }

        return;
    }
});

if (!class_exists(\Twig\Environment::class)) {
    foreach ([
        dirname($root) . '/form.ru/vendor/autoload.php',
        dirname($root) . '/form/vendor/autoload.php',
    ] as $fallbackAutoload) {
        if (!is_file($fallbackAutoload)) {
            continue;
        }

        require $fallbackAutoload;

        if (class_exists(\Twig\Environment::class)) {
            break;
        }
    }
}
