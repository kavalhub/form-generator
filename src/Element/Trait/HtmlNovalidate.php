<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlNovalidate
{
    protected bool $novalidate = false;

    public function setNovalidate(bool $value = true): self
    {
        $this->novalidate = $value;

        return $this;
    }

    protected function getHtmlNovalidate(): string
    {
        return $this->novalidate ? ' novalidate' : '';
    }
}
