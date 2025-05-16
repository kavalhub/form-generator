<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlDisabled
{
    protected bool $disabled = false;

    public function setDisabled(bool $value = true): self
    {
        $this->disabled = $value;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function getHtmlDisabled(): string
    {
        return $this->disabled ? ' disabled' : '';
    }
}
