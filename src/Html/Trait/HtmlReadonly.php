<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlReadonly
{
    protected bool $readonly = false;

    public function setReadonly(bool $value = true): self
    {
        $this->readonly = $value;

        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    protected function getHtmlReadonly(): string
    {
        return $this->readonly ? ' readonly' : '';
    }
}
