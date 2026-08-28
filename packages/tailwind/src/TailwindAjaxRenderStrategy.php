<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Tailwind;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\ElementRenderer;

final class TailwindAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    public function __construct(
        private readonly ?string $templatePath = null,
        private readonly ElementRenderer $renderer = new ElementRenderer(),
    ) {
    }

    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        return $this->renderer->html($element, TailwindTheme::create($this->templatePath));
    }

    public function fieldClass(ElementInterface $element): string
    {
        return method_exists($element, 'isError') && $element->isError()
            ? 'border-red-500 bg-red-50'
            : 'border-emerald-500';
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        if (!method_exists($element, 'isError') || !$element->isError()) {
            return '';
        }

        return '<div class="mt-1 text-sm text-red-600">' . $element->getDisplayErrors() . '</div>';
    }
}
