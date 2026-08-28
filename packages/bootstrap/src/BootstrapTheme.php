<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Bootstrap;

use Kavalhub\FormGenerator\Render\Engine\PhpTemplateEngine;
use Kavalhub\FormGenerator\Render\RenderTheme;

final class BootstrapTheme
{
    public static function create(?string $resourceBase = null, ?string $customPath = null): RenderTheme
    {
        return new RenderTheme(
            packagePath: __DIR__ . '/../templates',
            engine: new PhpTemplateEngine(),
            extension: '.php',
            customPath: $customPath ?? $resourceBase,
            resourceBase: $resourceBase,
            errorClass: 'is-invalid',
            successClass: 'is-valid',
            elementClasses: ['mb-2'],
            fieldErrorWrapper: '<div class="invalid-feedback">%s</div>',
        );
    }
}
