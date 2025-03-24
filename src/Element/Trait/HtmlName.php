<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Trait;

trait HtmlName
{
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

    protected function getHtmlName(): string
    {
        return !empty($this->name) ? ' name="' . $this->name . '"' : '';
    }
}
