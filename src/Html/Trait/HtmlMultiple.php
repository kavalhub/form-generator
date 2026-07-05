<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlMultiple
{
    protected bool $multiple = false;

    public function setMultiple(bool $value = true): self
    {
        $this->multiple = $value;
        if (method_exists($this, 'setNameAsArray')) {
            $this->setNameAsArray($value);
        }

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    protected function getHtmlMultiple(): string
    {
        return $this->multiple ? ' multiple' : '';
    }
}
