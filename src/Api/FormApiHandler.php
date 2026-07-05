<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Api;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Util\ElementDataCollector;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

final class FormApiHandler
{
    public function __construct(
        private readonly ElementValidatorInterface $validator,
    ) {
    }

    public function handleField(ElementInterface $root, string $targetId): FormApiResponse
    {
        $element = ElementDataCollector::findById($root, $targetId);
        if ($element === null) {
            return new FormApiResponse(valid: true, data: ElementDataCollector::collectByFormName($root));
        }

        $this->validator->handle($element);

        return $this->buildResponse($root, [$element]);
    }

    public function handleForm(ElementInterface $root): FormApiResponse
    {
        $this->validator->handle($root);

        return $this->buildResponse($root);
    }

    /**
     * @param list<ElementInterface>|null $scope
     */
    private function buildResponse(ElementInterface $root, ?array $scope = null): FormApiResponse
    {
        $fields = [];
        $targets = $scope ?? $this->collectInputElements($root);
        $valid = true;

        foreach ($targets as $element) {
            if ($element instanceof InputSubmit) {
                continue;
            }
            if (!$element instanceof ElementWithValue || $element->getName() === '') {
                continue;
            }
            $fieldValid = !method_exists($element, 'isError') || !$element->isError();
            if (!$fieldValid) {
                $valid = false;
            }
            $errors = method_exists($element, 'getErrors') ? $element->getErrors() : [];
            $fields[$element->getId()] = new FormFieldState(
                $element->getId(),
                $element->getName(),
                $fieldValid,
                $errors,
                $this->fieldValue($element),
            );
        }

        if ($scope === null) {
            if ($this->validator->isValid() === false) {
                $valid = false;
            }
            if (method_exists($root, 'isError') && $root->isError()) {
                $valid = false;
            }
        }

        return new FormApiResponse(
            $valid,
            $fields,
            ElementDataCollector::collectByFormName($root),
        );
    }

    /**
     * @return list<ElementInterface>
     */
    private function collectInputElements(ElementInterface $root): array
    {
        $elements = [];
        $this->walk($root, $elements);

        return $elements;
    }

    /**
     * @param list<ElementInterface> $elements
     */
    private function walk(ElementInterface $element, array &$elements): void
    {
        if ($element instanceof ElementWithValue && $element->getName() !== '' && !$element instanceof InputSubmit) {
            $elements[] = $element;
        }
        if ($element->getComposite()) {
            foreach ($element->getComposite()->getAll() as $child) {
                $this->walk($child, $elements);
            }
        }
    }

    private function fieldValue(ElementWithValue $element): mixed
    {
        if (method_exists($element, 'isChecked')) {
            return $element->isChecked() ? $element->getValue() : null;
        }

        $value = $element->getValue();

        return $value !== '' ? $value : null;
    }
}
