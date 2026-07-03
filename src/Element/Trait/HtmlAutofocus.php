<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlAutofocus
{
    protected bool $autofocus = false;

    public function setAutofocus(bool $value = true): self
    {
        $this->autofocus = $value;

        return $this;
    }

    public function isAutofocus(): bool
    {
        return $this->autofocus;
    }

    protected function getHtmlAutofocus(): string
    {
        return $this->autofocus ? ' autofocus' : '';
    }
}
