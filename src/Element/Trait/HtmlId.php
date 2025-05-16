<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlId
{
    protected string $id;

    public function getId(): string
    {
        return !empty($this->parent) ? $this->parent->getId() . '_' . $this->getThisId() : $this->getThisId();
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    protected function getThisId(): string
    {
        return $this->id ?? $this->name ?? '';
    }

    protected function getHtmlId(): string
    {
        return !empty($this->getThisId()) ?' id="' . $this->getId() . '"' : '';
    }
}
