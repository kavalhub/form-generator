<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlHref
{
    protected string $href = '';

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    protected function getHtmlHref(): string
    {
        return !empty($this->href) ? ' href="' . $this->href . '"' : '';
    }
}
