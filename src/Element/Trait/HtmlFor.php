<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlFor
{
    protected string $for = '';

    public function getFor(): string
    {
        return !empty($this->parent) ? $this->parent->getId() . '_' . $this->for : $this->for;
    }

    public function setFor(string $for): self
    {
        $this->for = $for;

        return $this;
    }

    protected function getHtmlFor(): string
    {
        return !empty($this->getFor()) ? ' for="' . $this->getFor() . '"' : '';
    }
}
