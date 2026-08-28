<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class RenderContext implements RenderContextInterface
{
    public HtmlDecoratableInterface $element;

    public function __construct(
        HtmlDecoratableInterface $element,
        private readonly RenderTheme $theme,
        private readonly ElementRenderer $renderer,
    ) {
        $this->element = $this->theme->prepareElement($element);
    }

    public function getElement(): HtmlDecoratableInterface
    {
        return $this->element;
    }

    public function decorateChild(HtmlDecoratableInterface $element): RenderContextInterface
    {
        return new self($element, $this->theme, $this->renderer);
    }

    public function getErrorClass(): string
    {
        return $this->theme->getErrorClass();
    }

    public function getSuccessClass(): string
    {
        return $this->theme->getSuccessClass();
    }

    public function render(): string
    {
        return $this->renderer->renderNode($this->element, $this, $this->theme);
    }
}
