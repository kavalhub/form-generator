<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlSize
{
    protected int $size = 0;

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    protected function getHtmlSize(): string
    {
        return $this->size > 0 ? ' size="' . $this->size . '"' : '';
    }
}
