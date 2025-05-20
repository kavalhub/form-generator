<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\CompositeElementInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\InputText;
use SplObjectStorage;
use SplSubject;

class CompositeElement extends Element implements CompositeElementInterface
{
    protected SplObjectStorage $elementStorage;

    public function __construct(string $tag = '')
    {
        parent::__construct($tag);
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

    public function removeElement(ElementInterface $element): self
    {
        $this->elementStorage->detach($element);

        return $this;
    }

    public function getValueArray(): array
    {
        $values = [];
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            if ($element->getComposite()) {
                $values += $element->getValueArray();
            }
            if ($element instanceof InputSubmit) {
                continue;
            }
            if ($element instanceof InputText) {
                $values[$element->getName()] = $element->getValue();
                continue;
            }
            if (method_exists($element, 'isChecked') && $element->isChecked()) {
                $values[$element->getName()][] = $element->getValue();
            }
            if (method_exists($element, 'isSelected') && $element->isSelected()) {
                $values[$element->getName()][] = $element->getValue();
            }
        }

        return $values;
    }

    // TODO переписать с массива ValueObject
    public function addElementBlock(string $elementName, array $elementData = []): self
    {
        if (!class_exists($elementName)) {
            return $this;
        }
        foreach ($elementData as $data) {
            $element = new $elementName($data['name']);
            foreach ($data['method'] as $method => $value) {
                if (method_exists($element, $method)) {
                    $element->$method($value);
                }
            }
            $this->addElement($element);
        }

        return $this;
    }

    public function getByName(string $name, bool $extract = false): Element
    {
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            if (method_exists($element, 'getName')) {
                if ($element->getName() === $name) {
                    if ($extract) {
                        $this->removeElement($element);
                    }
                    return $element;
                }
            }
            if ($element->getComposite()) {
                $childElement = $element->getByName($name);
                if (method_exists($childElement, 'getName')) {
                    if ($childElement->getName() === $name) {
                        if ($extract) {
                            $this->removeElement($childElement);
                        }
                        return $childElement;
                    }
                }
            }
        }

        return new NullElement('');
    }

    public function getByType(string $type): self
    {
        $new = new self();
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            if ($element instanceof $type) {
                $new->elementStorage->attach($element);
                continue;
            }
            if ($element->getComposite()) {
                $element->getByType($type);
            }
        }

        return $new;
    }

    public function notify(): self
    {
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            $element->notify();
        }

        parent::notify();

        return $this;
    }

    public function getAll(): SplObjectStorage
    {
        return $this->elementStorage;
    }

    public function getHtml(string $value = ''): string
    {
        foreach ($this->elementStorage as $element) {
            $value .= $element->getHtml();
        }

        return parent::getHtml($value);
    }
}
