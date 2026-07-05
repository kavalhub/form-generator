<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Interface;

interface HtmlDecoratableInterface extends HtmlRenderableInterface
{
    public function addClass(array $class): self;

    public function getTag(): string;

    public function getPath(): string;

    public function getHtmlTrait(array $without = []): string;
}
