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
        $element->setParent($this);
        $this->elementStorage->attach($element, $info ?? $element->getId());

        return $this;
    }

    public function getByName(string $name): Element
    {
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            if ($element instanceof ElementWithName) {
                if ($element->getId() === $name) {
                    return $element;
                }
            }
        }

        return new NullElement('');
    }

    public function getAll(): SplObjectStorage
    {
        return $this->elementStorage;
    }
}
