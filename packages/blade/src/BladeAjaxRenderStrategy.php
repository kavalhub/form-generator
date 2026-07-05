<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Blade;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class BladeAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    public function __construct(
        private readonly ?string $templatePath = null,
        private readonly ?BladeViewFactory $viewFactory = null,
    ) {
    }

    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        $decorator = new BladeDecorator($element, $this->viewFactory);
        if ($this->templatePath !== null) {
            $decorator->setTemplate($this->templatePath);
        }

        return $decorator->getHtml();
    }

    public function fieldClass(ElementInterface $element): string
    {
        return method_exists($element, 'isError') && $element->isError() ? 'fg-blade-is-invalid' : 'fg-blade-is-valid';
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        if (!method_exists($element, 'isError') || !$element->isError()) {
            return '';
        }

        return '<div class="fg-blade-error">' . $element->getDisplayErrors() . '</div>';
    }
}
