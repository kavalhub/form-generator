<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlValue
{
    protected string $value = '';

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    protected function getHtmlValue(): string
    {
        return !empty($this->value) ? ' value="' . htmlspecialchars($this->value) . '"' : '';
    }
}
