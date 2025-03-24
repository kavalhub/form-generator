<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Trait;

trait HtmlPlaceholder
{
    protected string $placeholder = '';

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    protected function getHtmlPlaceholder(): string
    {
        return !empty($this->placeholder) ? ' placeholder="' . $this->placeholder . '"' : '';
    }
}
