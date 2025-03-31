<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlMaxlength
{
    protected int $maxlength = 0;

    public function getMaxlength(): int
    {
        return $this->maxlength;
    }

    public function setMaxlength(int $maxlength): self
    {
        $this->maxlength = $maxlength;

        return $this;
    }

    protected function getHtmlMaxlength(): string
    {
        return !empty($this->maxlength) ? ' maxlength="' . $this->maxlength . '"' : '';
    }
}
