<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlRows
{
    protected int $rows = 0;

    public function getRows(): int
    {
        return $this->rows;
    }

    public function setRows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    protected function getHtmlRows(): string
    {
        return $this->rows > 0 ? ' rows="' . $this->rows . '"' : '';
    }
}
