<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Fabric;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Fabric\Interface\ElementFabricInterface;
use Kavalhub\FormGenerator\Form\Group;
use Kavalhub\FormGenerator\Form\InputCheckbox;
use Kavalhub\FormGenerator\Form\InputRadio;
use Kavalhub\FormGenerator\Form\Select;
use RuntimeException;

class ElementFabric implements ElementFabricInterface
{
    public const ELEMENT = 'element';
    public const NAME = 'name';
    public const METHOD = 'method';
    public const BLOCK = 'block';
    public const ADD_ELEMENT = 'addElement';
    public const ADD_ELEMENT_BLOCK = 'addElementBlock';
    public const ADD_CALLBACK_VALIDATOR = 'addCallbackValidator';
    public const ATTACH_OBSERVER = 'attachObserver';

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
        if (empty($elementData[self::ELEMENT]) || !class_exists($elementData[self::ELEMENT])) {
            throw new RuntimeException('Element class does not exist.');
        }
        if (empty($elementData[self::NAME])) {
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
        if ($method === self::ADD_ELEMENT) {
            $add = is_array($value) ? self::create($value) : $value;
            $element->addElement($add);
        } elseif ($method === self::ADD_ELEMENT_BLOCK) {
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

    public static function getClassName(string $name): string
    {
        return match ($name) {
            'InputCheckbox' => InputCheckbox::class,
            'InputRadio' => InputRadio::class,
            'Select' => Select::class,
            'Group' => Group::class,
        };
    }
}
