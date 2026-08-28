<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Http;

enum DecoratorMode: string
{
    case Html = 'html';
    case Bootstrap = 'bootstrap';
    case Blade = 'blade';
    case Twig = 'twig';
    case Tailwind = 'tailwind';

    public static function decoratorLabel(self $mode): string
    {
        return match ($mode) {
            self::Html => 'HTML (без декоратора)',
            self::Bootstrap => 'Bootstrap',
            self::Blade => 'Blade',
            self::Twig => 'Twig',
            self::Tailwind => 'Tailwind',
        };
    }
}
