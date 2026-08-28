<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

interface RenderContextInterface
{
    public function getElement(): HtmlDecoratableInterface;

    public function decorateChild(HtmlDecoratableInterface $element): RenderContextInterface;

    public function getErrorClass(): string;

    public function getSuccessClass(): string;

    public function render(): string;
}
