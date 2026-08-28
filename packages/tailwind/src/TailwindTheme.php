<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Tailwind;

use Kavalhub\FormGenerator\Render\Engine\PhpTemplateEngine;
use Kavalhub\FormGenerator\Render\RenderTheme;

final class TailwindTheme
{
    public static function create(?string $resourceBase = null, ?string $customPath = null): RenderTheme
    {
        return new RenderTheme(
            packagePath: __DIR__ . '/../templates',
            engine: new PhpTemplateEngine(),
            extension: '.php',
            customPath: $customPath ?? $resourceBase,
            resourceBase: $resourceBase,
            errorClass: 'border-red-500 bg-red-50',
            successClass: 'border-emerald-500',
            elementClasses: ['mb-3'],
        );
    }
}
