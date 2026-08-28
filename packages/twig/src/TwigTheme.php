<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Twig;

use Kavalhub\FormGenerator\Render\RenderTheme;
use Kavalhub\FormGenerator\Twig\Engine\TwigTemplateEngine;

final class TwigTheme
{
    public static function create(
        ?string $resourceBase = null,
        ?string $customPath = null,
        ?TwigViewFactory $viewFactory = null,
    ): RenderTheme {
        $viewFactory ??= new TwigViewFactory();

        return new RenderTheme(
            packagePath: __DIR__ . '/../templates',
            engine: new TwigTemplateEngine($viewFactory),
            extension: '.html.twig',
            customPath: $customPath ?? $resourceBase,
            resourceBase: $resourceBase,
            errorClass: 'fg-twig-is-invalid',
            successClass: 'fg-twig-is-valid',
            elementClasses: ['fg-twig-field-item'],
        );
    }
}
