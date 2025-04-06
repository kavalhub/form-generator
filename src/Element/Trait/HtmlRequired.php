<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlRequired
{
    protected bool $required = false;

    public function setRequired(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    protected function getHtmlRequired(): string
    {
        return $this->required ? ' required' : '';
    }
}
