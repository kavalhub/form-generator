<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Trait;

trait HtmlRequired
{
    protected bool $required = false;

    public function setRequired(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    protected function getHtmlRequired(): string
    {
        return $this->required ? ' required' : '';
    }
}
