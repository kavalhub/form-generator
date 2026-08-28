<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class ElementRenderer
{
    private RenderTheme $theme;

    public function __construct(?RenderTheme $theme = null)
    {
        $this->theme = $theme ?? RenderTheme::plain();
    }

    public function setTheme(RenderTheme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTheme(): RenderTheme
    {
        return $this->theme;
    }

    public function html(HtmlDecoratableInterface $element, ?RenderTheme $theme = null): string
    {
        $theme ??= $this->theme;

        return (new RenderContext($element, $theme, $this))->render();
    }

    public function renderNode(
        HtmlDecoratableInterface $element,
        RenderContext $context,
        RenderTheme $theme,
    ): string {
        return $theme->pipeline()->render($element, $context);
    }
}
