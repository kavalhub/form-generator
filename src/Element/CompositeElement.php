<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\CompositeElementInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Interface\SkipsValueCollection;
use Kavalhub\FormGenerator\Util\ElementDataCollector;
use SplObjectStorage;

class CompositeElement extends Element implements CompositeElementInterface
{
    protected SplObjectStorage $elementStorage;

    public function __construct()
    {
        parent::__construct();
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

    public function removeElement(ElementInterface $element): self
    {
        $this->elementStorage->detach($element);

        return $this;
    }

    public function useStorage(bool $enabled = true): self
    {
        $this->elementStorage->rewind();
        foreach ($this->elementStorage as $element) {
            if (method_exists($element, 'useStorage')) {
                $element->useStorage($enabled);
            }
            // Рекурсивно для вложенных композитов
            if ($element->getComposite()) {
                $element->getComposite()->useStorage($enabled);
            }
        }

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
            if ($element instanceof SkipsValueCollection) {
                continue;
            }
            if ($element instanceof ElementWithValue) {
                if (method_exists($element, 'isChecked')) {
                    if ($element->isChecked()) {
                        $values[$element->getName()][] = $element->getValue();
                    }
                    continue;
                }
                if (method_exists($element, 'isSelected')) {
                    if ($element->isSelected()) {
                        $name = $element->getName() !== ''
                            ? $element->getName()
                            : $element->getParent()->getName();
                        $values[$name][] = $element->getValue();
                    }
                    continue;
                }
                if ($element->getName() !== '') {
                    $values[$element->getName()] = $element->getValue();
                }
            }
        }

        return $values;
    }

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

    public function getById(string $id, bool $extract = false): Element
    {
        $found = ElementDataCollector::findById($this, $id);
        if ($found === null) {
            return new NullElement('');
        }
        if ($extract && $found->getParent()?->getComposite() !== null) {
            $found->getParent()->getComposite()->removeElement($found);
        }

        return $found;
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
                $childResults = $element->getByType($type);
                foreach ($childResults->getAll() as $childElement) {
                    $new->elementStorage->attach($childElement);
                }
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
}
