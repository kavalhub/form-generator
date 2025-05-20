<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Request\Interface\RequestInterface;

abstract class AbstractElementRequest implements RequestInterface
{
    abstract public function get(string $name): ?array;

    public function setValue(ElementInterface $element): void
    {
        $value = $this->get($element->getFormName());
        if (isset($value)) {
            foreach ($value as $item) {
                $element->setValue((string)$item);
            }
        }
    }
}