<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Api;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use Kavalhub\FormGenerator\Html\InputNumber;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Option;
use Kavalhub\FormGenerator\Html\Select;

final class FormJsonSchemaExporter
{
    /**
     * @return array<string, mixed>
     */
    public static function export(ElementInterface $root): array
    {
        $properties = [];
        $required = [];
        self::walk($root, $properties, $required);

        $schema = ['type' => 'object', 'properties' => $properties];
        if ($required !== []) {
            $schema['required'] = array_values(array_unique($required));
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $properties
     * @param list<string> $required
     */
    private static function walk(ElementInterface $element, array &$properties, array &$required): void
    {
        if ($element instanceof InputSubmit) {
            return;
        }

        if ($element instanceof Select) {
            $name = $element->getName();
            if ($name === '') {
                return;
            }
            $enum = [];
            if ($element->getComposite()) {
                foreach ($element->getComposite()->getAll() as $child) {
                    if ($child instanceof Option && $child->getValue() !== '') {
                        $enum[] = $child->getValue();
                    }
                }
            }
            $schema = ['type' => 'string', 'title' => $name];
            if ($enum !== []) {
                $schema['enum'] = $enum;
            }
            $properties[$name] = $schema;
            if ($element->isRequired()) {
                $required[] = $name;
            }

            return;
        }

        if ($element instanceof InputCheckbox || $element instanceof InputRadio) {
            $name = $element->getName();
            if ($name === '') {
                return;
            }
            if (!isset($properties[$name])) {
                $properties[$name] = [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'title' => $name,
                ];
            }
            if ($element->isRequired() && !in_array($name, $required, true)) {
                $required[] = $name;
            }

            return;
        }

        if ($element instanceof InputText) {
            $name = $element->getName();
            if ($name === '') {
                return;
            }
            $schema = ['type' => 'string', 'title' => $name];
            if ($element->getPlaceholder() !== '') {
                $schema['description'] = $element->getPlaceholder();
            }
            if ($element->getMinlength() > 0) {
                $schema['minLength'] = $element->getMinlength();
            }
            if ($element->getMaxlength() > 0) {
                $schema['maxLength'] = $element->getMaxlength();
            }
            $properties[$name] = $schema;
            if ($element->isRequired()) {
                $required[] = $name;
            }

            return;
        }

        if ($element instanceof InputNumber) {
            $name = $element->getName();
            if ($name === '') {
                return;
            }
            $schema = ['type' => 'number', 'title' => $name];
            if ($element->getPlaceholder() !== '') {
                $schema['description'] = $element->getPlaceholder();
            }
            if ($element->getMin() !== 0.0) {
                $schema['minimum'] = $element->getMin();
            }
            if ($element->getMax() !== 0.0) {
                $schema['maximum'] = $element->getMax();
            }
            $properties[$name] = $schema;
            if ($element->isRequired()) {
                $required[] = $name;
            }

            return;
        }

        if ($element instanceof ElementWithValue && $element->getName() !== '') {
            $name = $element->getName();
            $properties[$name] = ['type' => 'string', 'title' => $name];
            if ($element->isRequired()) {
                $required[] = $name;
            }

            return;
        }

        if ($element->getComposite()) {
            foreach ($element->getComposite()->getAll() as $child) {
                self::walk($child, $properties, $required);
            }
        }
    }
}
