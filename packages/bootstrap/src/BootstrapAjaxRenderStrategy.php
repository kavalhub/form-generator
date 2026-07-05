<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Bootstrap;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class BootstrapAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    public function __construct(
        private readonly ?string $templatePath = null,
    ) {
    }

    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        $decorator = new BootstrapDecorator($element);
        if ($this->templatePath !== null) {
            $decorator->setTemplate($this->templatePath);
        }

        return $decorator->getHtml();
    }

    public function fieldClass(ElementInterface $element): string
    {
        return method_exists($element, 'isError') && $element->isError() ? 'is-invalid' : 'is-valid';
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        if (!method_exists($element, 'isError') || !$element->isError()) {
            return '';
        }

        return '<div class="invalid-feedback">' . $element->getDisplayErrors() . '</div>';
    }
}
