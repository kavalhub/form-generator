<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Interface;

interface HtmlRenderableInterface
{
    public function render(string $innerHtml = ''): string;
}
