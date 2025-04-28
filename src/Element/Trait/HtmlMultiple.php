<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlMultiple
{
    protected bool $multiple = false;

    public function setMultiple(bool $value = true): self
    {
        $this->multiple = $value;

        return $this;
    }

    protected function getHtmlMultiple(): string
    {
        return $this->multiple ? ' multiple' : '';
    }
}
