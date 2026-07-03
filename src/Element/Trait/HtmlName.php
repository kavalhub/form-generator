<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Util\HtmlEscaper;

trait HtmlName
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

    protected function getHtmlName(): string
    {
        $suffix = $this->nameAsArray ? '[]' : '';

        return !empty($this->getName())
            ? ' name="' . HtmlEscaper::escapeAttribute($this->getFormName()) . $suffix . '"'
            : '';
    }
}
