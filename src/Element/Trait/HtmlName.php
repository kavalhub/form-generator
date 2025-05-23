<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlName
{
    protected bool $multiple = false;
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

    public function getFormName(): string
    {
        return !empty($this->parent) ? $this->parent->getId() . '_' . $this->name : $this->name;
    }

    protected function getHtmlName(): string
    {
        $multiple = $this->multiple ? '[]' : '';

        return !empty($this->getName()) ? ' name="' . $this->getFormName() . $multiple . '"' : '';
    }
}
