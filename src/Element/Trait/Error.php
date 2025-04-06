<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Error
{
    protected array $error = [];

    public function getError(): array
    {
        return $this->error;
    }

    public function addError(array $error): self
    {
        $this->error = array_merge($this->error, $error);

        return $this;
    }

    protected function getHtmlError(): string
    {
        return !empty($this->error) ? implode('<br>', $this->error) : '';
    }
}
