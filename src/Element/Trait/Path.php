<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Path
{
    protected string $path = '';

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path = ''): self
    {
        $this->path = $path;

        return $this;
    }
}
