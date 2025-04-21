<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Label
{
    protected string $label = '';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
