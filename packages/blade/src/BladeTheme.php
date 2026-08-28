<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Blade;

use Kavalhub\FormGenerator\Blade\Engine\BladeTemplateEngine;
use Kavalhub\FormGenerator\Render\RenderTheme;

final class BladeTheme
{
    public static function create(
        ?string $resourceBase = null,
        ?string $customPath = null,
        ?BladeViewFactory $viewFactory = null,
    ): RenderTheme {
        $viewFactory ??= new BladeViewFactory();

        return new RenderTheme(
            packagePath: __DIR__ . '/../templates',
            engine: new BladeTemplateEngine($viewFactory),
            extension: '.php',
            customPath: $customPath ?? $resourceBase,
            resourceBase: $resourceBase,
            errorClass: 'fg-blade-is-invalid',
            successClass: 'fg-blade-is-valid',
            elementClasses: ['fg-blade-field-item'],
        );
    }
}
