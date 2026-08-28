<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\ElementRenderer;

/**
 * Ajax presentation layer on top of the renderer's current RenderTheme.
 */
final class ThemeAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    public function __construct(
        private readonly ElementRenderer $renderer = new ElementRenderer(),
    ) {
    }

    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        return $this->renderer->html($element);
    }

    public function fieldClass(ElementInterface $element): string
    {
        $theme = $this->renderer->getTheme();

        if (method_exists($element, 'isError') && $element->isError()) {
            return $theme->getErrorClass();
        }

        if (
            method_exists($element, 'isValid')
            && $element->isValid()
            && method_exists($element, 'getValue')
            && $element->getValue() !== ''
            && $element->getValue() !== null
        ) {
            return $theme->getSuccessClass();
        }

        return '';
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        if (!method_exists($element, 'isError') || !$element->isError()) {
            return '';
        }

        return $this->renderer->getTheme()->formatFieldError($element->getDisplayErrors());
    }
}
