<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlValue
{
    protected string $value;
    protected string $defaultValue = '';

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
        return $this->value ?? $this->defaultValue;
    }
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    protected function getHtmlValue(): string
    {
        return !empty($this->getValue()) ? ' value="' . htmlspecialchars($this->getValue()) . '"' : '';
    }
}
