<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlChecked
{
    protected bool $checked = false;

    public function setChecked(bool $value = true): self
    {
        $this->checked = $value;

        return $this;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    protected function getHtmlChecked(): string
    {
        return $this->checked ? ' checked' : '';
    }
}
