<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class NullAjaxRenderStrategy implements AjaxRenderStrategyInterface
{
    public function blockHtml(HtmlDecoratableInterface $element): string
    {
        return $element->render();
    }

    public function fieldClass(ElementInterface $element): string
    {
        return '';
    }

    public function fieldErrorHtml(ElementInterface $element): string
    {
        if (!method_exists($element, 'isError') || !$element->isError()) {
            return '';
        }

        return $element->getDisplayErrors();
    }
}
