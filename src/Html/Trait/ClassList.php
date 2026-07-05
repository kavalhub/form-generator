<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait ClassList
{
    protected array $classList = [];

    public function addClass(array $class): self
    {
        $this->classList = array_merge($this->classList, $class);

        return $this;
    }

    public function removeClass(array $class): self
    {
        $this->classList = array_diff($this->classList, $class);

        return $this;
    }

    public function getClassList(): array
    {
        return $this->classList;
    }
}
