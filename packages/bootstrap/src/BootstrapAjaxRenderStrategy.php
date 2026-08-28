<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Bootstrap;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Ajax\ThemeAjaxRenderStrategy;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Render\RenderTheme;

/**
 * BC facade: ensures bootstrap theme on the renderer, then delegates to ThemeAjaxRenderStrategy.
 *
 * Prefer: $renderer->setTheme(RenderTheme::bootstrap()); new ThemeAjaxRenderStrategy($renderer);
 */
final class BootstrapAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    private readonly ThemeAjaxRenderStrategy $inner;

    /**
     * @param ElementRenderer|string|null $rendererOrTemplatePath ElementRenderer, or legacy custom/template path string
     */
    public function __construct(
        ElementRenderer|string|null $rendererOrTemplatePath = null,
        ?RenderTheme $theme = null,
    ) {
        if (is_string($rendererOrTemplatePath)) {
            $renderer = new ElementRenderer();
            $renderer->setTheme($theme ?? BootstrapTheme::create($rendererOrTemplatePath));
        } else {
            $renderer = $rendererOrTemplatePath ?? new ElementRenderer();
            $renderer->setTheme($theme ?? BootstrapTheme::create());
        }

        $this->inner = new ThemeAjaxRenderStrategy($renderer);
    }

    public static function fromRenderer(ElementRenderer $renderer, ?RenderTheme $theme = null): self
    {
        return new self($renderer, $theme ?? RenderTheme::bootstrap());
    }

    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        return $this->inner->blockHtml($element);
    }

    public function fieldClass(ElementInterface $element): string
    {
        return $this->inner->fieldClass($element);
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        return $this->inner->fieldErrorHtml($element);
    }
}
