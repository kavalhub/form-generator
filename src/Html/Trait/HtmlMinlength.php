<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlMinlength
{
    protected int $minlength = 0;

    public function getMinlength(): int
    {
        return $this->minlength;
    }

    public function setMinlength(int $minlength): self
    {
        $this->minlength = $minlength;

        return $this;
    }

    protected function getHtmlMinlength(): string
    {
        return $this->minlength > 0 ? ' minlength="' . $this->minlength . '"' : '';
    }
}
