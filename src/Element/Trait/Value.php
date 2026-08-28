<?php

declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Value
{
    protected string $value = '';
    protected string $defaultValue = '';
    protected bool $useStorage = false;

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value !== '' ? $this->value : $this->defaultValue;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function useStorage(bool $enabled = true): self
    {
        $this->useStorage = $enabled;
        return $this;
    }

    public function isUsingStorage(): bool
    {
        return $this->useStorage;
    }
}