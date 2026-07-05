<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlElementCore
{
    use ClassList;
    use Path;

    protected string $tag = 'div';

    public function getTag(): string
    {
        return $this->tag;
    }

    protected function setTag(string $tag): void
    {
        $this->tag = $tag;
    }
}
