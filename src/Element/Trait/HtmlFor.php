<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

trait HtmlFor
{
    protected string $for = '';
    protected ElementInterface $element;

    public function getFor(): string
    {
        return !empty($this->parent) && !empty($this->for) ? $this->parent->getId() . '_' . $this->for : $this->for;
    }

    public function setFor(string $for): self
    {
        $this->for = $for;

        return $this;
    }

    public function setElement(ElementInterface $element): self
    {
        $this->element = $element;
        $this->setFor($element->getId());
        $this->setId($element->getId() . '_label');

        return $this;
    }

    public function setElementByName(string $name): self
    {
        if ($this->parent->getComposite()) {
            $this->setElement($this->parent->getByName($name));
        }
        return $this;
    }

    protected function getHtmlFor(): string
    {
        return !empty($this->getFor()) ? ' for="' . $this->getFor() . '"' : '';
    }
}
