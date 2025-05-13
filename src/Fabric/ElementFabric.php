<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Fabric;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Fabric\Interface\ElementFabricInterface;
use RuntimeException;

class ElementFabric implements ElementFabricInterface
{
    public static function create(array $elementData): ElementInterface
    {
        self::validateElementData($elementData);

        /** @var ElementInterface $element */
        $element = new $elementData['element']($elementData['name']);

        if (!empty($elementData['method'])) {
            self::applyMethods($element, $elementData['method']);
        }

        return $element;
    }

    private static function validateElementData(array $elementData): void
    {
        if (!class_exists($elementData['element'])) {
            throw new RuntimeException('Element class does not exist.');
        }
        if (empty($elementData['name'])) {
            throw new RuntimeException('Element name should not be empty.');
        }
    }

    private static function applyMethods(ElementInterface $element, array $methods): void
    {
        foreach ($methods as $methodList) {
            foreach ($methodList as $method => $value) {
                if (method_exists($element, $method)) {
                    self::invokeMethod($element, $method, $value);
                }
            }
        }
    }

    private static function invokeMethod(ElementInterface $element, string $method, $value): void
    {
        if ($method === 'addElement') {
            $element->addElement(self::create($value));
        } elseif ($method === 'addElementBlock') {
            self::addElementBlock($element, $value);
        } else {
            $element->$method($value);
        }
    }

    private static function addElementBlock(ElementInterface $element, array $value): void
    {
        foreach ($value['block'] as $block) {
            $element->addElement(self::create(['element' => $value['element'], ...$block]));
        }
    }
}
