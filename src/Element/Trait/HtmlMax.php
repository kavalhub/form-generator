<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlMax
{
    protected float $max = 0;

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): self
    {
        $this->max = $max;

        return $this;
    }

    protected function getHtmlMax(): string
    {
        return !empty($this->max) ? ' max="' . $this->max . '"' : '';
    }
}
