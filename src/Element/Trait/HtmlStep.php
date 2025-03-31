<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlStep
{
    protected float $step = 0;

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep(float $step): self
    {
        $this->step = $step;

        return $this;
    }

    protected function getHtmlStep(): string
    {
        return !empty($this->step) ? ' step="' . $this->step . '"' : '';
    }
}
