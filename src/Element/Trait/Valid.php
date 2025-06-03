<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Valid
{
    protected ?bool $valid;

    public function isValid(): ?bool
    {
        return $this->valid ?? null;
    }

    public function setValid(bool $valid = true): self
    {
        $this->valid = $valid;

        return $this;
    }
}
