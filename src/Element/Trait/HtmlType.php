<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Trait;

trait HtmlType
{
    protected string $type = '';

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    protected function getHtmlType(): string
    {
        return !empty($this->type) ? ' type="' . $this->type . '"' : '';
    }
}
