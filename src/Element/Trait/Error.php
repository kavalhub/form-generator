<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait Error
{
    protected bool $error = false;
    protected array $errorList = [];

    public function setError(bool $error = true): self
    {
        $this->error = $error;

        return $this;
    }

    public function isError(): bool
    {
        return $this->error;
    }

    public function getError(): array
    {
        return $this->errorList;
    }

    public function addError(array $error): self
    {
        $this->setError();
        $this->errorList = array_merge($this->errorList, $error);

        return $this;
    }

    protected function getHtmlError(): string
    {
        return !empty($this->errorList) ? implode('<br>', $this->errorList) : '';
    }
}
