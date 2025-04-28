<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlMethod
{
    protected string $method = '';

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    protected function getHtmlMethod(): string
    {
        return !empty($this->getMethod()) ? ' method="' . $this->getMethod() . '"' : '';
    }
}
