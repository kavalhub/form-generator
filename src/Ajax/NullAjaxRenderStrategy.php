<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Render\RenderTheme;

/**
 * @deprecated Use ThemeAjaxRenderStrategy with ElementRenderer that already has a theme.
 */
final class NullAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    private readonly ThemeAjaxRenderStrategy $inner;

    public function __construct(?ElementRenderer $renderer = null, ?RenderTheme $theme = null)
    {
        $renderer ??= new ElementRenderer();
        if ($theme !== null) {
            $renderer->setTheme($theme);
        }
        $this->inner = new ThemeAjaxRenderStrategy($renderer);
    }

    public static function fromRenderer(ElementRenderer $renderer, ?RenderTheme $theme = null): self
    {
        return new self($renderer, $theme);
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
