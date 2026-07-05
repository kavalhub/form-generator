<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax\Interface;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

interface AjaxRenderStrategyInterface
{
    public function blockHtml(HtmlDecoratableInterface $element): string;

    public function fieldClass(ElementInterface $element): string;

    public function fieldErrorHtml(ElementInterface $element): string;
}
