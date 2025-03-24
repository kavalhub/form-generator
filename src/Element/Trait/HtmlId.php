<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Trait;

trait HtmlId
{
    protected string $id;

    public function getId(): string
    {
        return $this->id ?? static::class ?? '';
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    protected function getHtmlId(): string
    {
        return ' id="' . $this->getId() . '"';
    }
}
