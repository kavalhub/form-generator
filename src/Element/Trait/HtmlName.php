<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlName
{
    protected string $name = '';

    public function getName(): string
    {
        return !empty($this->parent) ? $this->parent->getId() . '_' . $this->name : $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    protected function getHtmlName(): string
    {
        return !empty($this->getName()) ? ' name="' . $this->getName() . '"' : '';
    }
}
