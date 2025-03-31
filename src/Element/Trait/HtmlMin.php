<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlMin
{
    protected float $min = 0;

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    protected function getHtmlMin(): string
    {
        return !empty($this->min) ? ' min="' . $this->min . '"' : '';
    }
}
