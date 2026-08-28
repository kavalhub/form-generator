<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Storage;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Storage\Interface\ElementStorageInterface;

abstract class AbstractElementStorage implements ElementStorageInterface
{

    abstract public function get(string $key): mixed;

    abstract public function has(string $key): bool;

    abstract public function remove(string $key): void;

    public function set(ElementInterface $element): void
    {
        if (!method_exists($element, 'isUsingStorage') || !$element->isUsingStorage())
        {
            return;
        }

        $this->attach($element->getFormName(), $element->getValue());
    }

    public function setDefaultValue(ElementInterface $element): void
    {
        if (!method_exists($element, 'isUsingStorage') || !$element->isUsingStorage())
        {
            return;
        }

        $value = $this->get($element->getFormName());
        if (isset($value)) {
            foreach ($value as $item) {
                if (method_exists($element, 'setDefaultValue')) {
                    $element->setValue((string)$item);
                }
            }
        }
    }

    abstract protected function attach(string $key, $value): void;
}