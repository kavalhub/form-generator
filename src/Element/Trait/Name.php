<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Name
{
    protected bool $nameAsArray = false;
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setNameAsArray(bool $value = true): self
    {
        $this->nameAsArray = $value;

        return $this;
    }

    public function isNameAsArray(): bool
    {
        return $this->nameAsArray;
    }

    public function getFormName(): string
    {
        return !empty($this->parent) ? $this->parent->getId() . '_' . $this->name : $this->name;
    }
}
