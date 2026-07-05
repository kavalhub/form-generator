<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Util;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Interface\SkipsValueCollection;

final class ElementDataCollector
{
    /**
     * @return array<string, mixed>
     */
    public static function collectByFormName(ElementInterface $root): array
    {
        $data = [];
        self::walk($root, $data);

        return $data;
    }

    public static function findByFormName(ElementInterface $root, string $formName): ?ElementInterface
    {
        if (
            $root instanceof ElementWithValue
            && method_exists($root, 'getFormName')
            && $root->getFormName() === $formName
        ) {
            return $root;
        }

        if ($root->getComposite()) {
            foreach ($root->getComposite()->getAll() as $child) {
                $found = self::findByFormName($child, $formName);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * @param array<string, string[]> $errorsByField
     */
    public static function applyErrors(ElementInterface $root, array $errorsByField): void
    {
        foreach ($errorsByField as $field => $messages) {
            $element = self::findByFormName($root, $field);
            if ($element !== null && method_exists($element, 'addError')) {
                $element->addError($messages);
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function walk(ElementInterface $element, array &$data): void
    {
        if ($element->getComposite()) {
            foreach ($element->getComposite()->getAll() as $child) {
                self::walk($child, $data);
            }
        }

        if ($element instanceof SkipsValueCollection) {
            return;
        }

        if (!$element instanceof ElementWithValue || !method_exists($element, 'getFormName')) {
            return;
        }

        $formName = $element->getFormName();
        if ($formName === '') {
            return;
        }

        if (method_exists($element, 'isChecked')) {
            if ($element->isChecked()) {
                $data[$formName] ??= [];
                $data[$formName][] = $element->getValue();
            }

            return;
        }

        if (method_exists($element, 'isSelected')) {
            if ($element->isSelected()) {
                $data[$formName] ??= [];
                $data[$formName][] = $element->getValue();
            }

            return;
        }

        $data[$formName] = $element->getValue();
    }
}
