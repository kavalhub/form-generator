<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

enum DecoratorMode: string
{
    case Html = 'html';
    case Bootstrap = 'bootstrap';
    case Blade = 'blade';
    case Twig = 'twig';
    case Tailwind = 'tailwind';

    public static function fromRequest(): self
    {
        $sessionMode = self::tryFrom((string)($_SESSION['decorator'] ?? self::Blade->value)) ?? self::Blade;
        $settingsForm = new DemoSettingsForm($sessionMode);
        $decoratorKey = $settingsForm->decoratorRequestKey();

        if (isset($_REQUEST[$decoratorKey])) {
            $raw = $_REQUEST[$decoratorKey];
            $value = is_array($raw) ? (string)($raw[0] ?? '') : (string)$raw;
            if ($value !== '') {
                $_SESSION['decorator'] = $value;
            }
        }

        $value = (string)($_SESSION['decorator'] ?? self::Blade->value);

        return self::tryFrom($value) ?? self::Blade;
    }
}
