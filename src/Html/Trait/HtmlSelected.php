<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlSelected
{
    protected bool $selected = false;

    public function setSelected(bool $value = true): self
    {
        $this->selected = $value;

        return $this;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    protected function getHtmlSelected(): string
    {
        return $this->selected ? ' selected' : '';
    }
}
