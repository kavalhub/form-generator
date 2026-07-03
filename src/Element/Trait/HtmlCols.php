<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlCols
{
    protected int $cols = 0;

    public function getCols(): int
    {
        return $this->cols;
    }

    public function setCols(int $cols): self
    {
        $this->cols = $cols;

        return $this;
    }

    protected function getHtmlCols(): string
    {
        return $this->cols > 0 ? ' cols="' . $this->cols . '"' : '';
    }
}
