<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\CompositeElementInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use SplObjectStorage;

class CompositeElement extends Element implements CompositeElementInterface
{
    protected SplObjectStorage $elementStorage;

    public function __construct()
    {
        $this->elementStorage = new SplObjectStorage();
    }

    public function getComposite(): ?self
    {
        return $this;
    }

    public function addElement(ElementInterface $element, mixed $info = null): self
    {
        $this->elementStorage->attach($element, $info ?? $element->getId());

        return $this;
    }

    public function getById(string $id): Element
    {
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            if ($this->elementStorage->getInfo() === $id) {
                return $element;
            }
        }

        return new NullElement();
    }
}
