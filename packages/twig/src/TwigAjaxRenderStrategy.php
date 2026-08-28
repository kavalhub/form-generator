<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Twig;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\ElementRenderer;

final class TwigAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    public function __construct(
        private readonly ?string $templatePath = null,
        private readonly ?TwigViewFactory $viewFactory = null,
        private readonly ElementRenderer $renderer = new ElementRenderer(),
    ) {
    }

    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        return $this->renderer->html(
            $element,
            TwigTheme::create($this->templatePath, $this->templatePath, $this->viewFactory),
        );
    }

    public function fieldClass(ElementInterface $element): string
    {
        return method_exists($element, 'isError') && $element->isError()
            ? 'fg-twig-is-invalid'
            : 'fg-twig-is-valid';
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        if (!method_exists($element, 'isError') || !$element->isError()) {
            return '';
        }

        return '<div class="fg-twig-error">' . $element->getDisplayErrors() . '</div>';
    }
}
