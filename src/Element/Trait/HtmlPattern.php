<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlPattern
{
    protected string $pattern = '';

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    protected function getHtmlPattern(): string
    {
        return !empty($this->pattern) ? ' pattern="' . $this->pattern . '"' : '';
    }
}
