<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Request\Interface\RequestInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class ElementValidator implements ElementValidatorInterface
{
    private array $checkList = [];

    public function __construct(private readonly RequestInterface $request)
    {
    }

    public function submit(InputSubmit $submit): bool
    {
        return !empty($this->request->get($submit->getFormName()));
    }

    public function exec(ElementInterface $element): bool
    {
        if ($element->getComposite()) {
            foreach (
                $element->getComposite()
                    ->getAll() as $composite
            ) {
                $this->checkList[] = (new self($this->request))->exec($composite);
            }
        }
        if ($element instanceof ElementWithValue) {
            $this->request($element);
            $this->required($element);
        }
        foreach ($element->getCallbackValidatorList() as $callbackValidator) {
            $this->checkList[] = $callbackValidator($element);
        }
        if (!in_array(false, $this->checkList, true)) {
            $element->setValid();
            return true;
        }

        return false;
    }

    private function request(ElementWithValue $element): void
    {
        $value = $this->request->get($element->getFormName());
        if (isset($value)) {
            foreach ($value as $item) {
                $element->setValue((string)$item);
            }
        }
    }

    private function required(ElementWithValue $element): void
    {
        if ($element->isRequired()) {
            $element->addCallbackValidator(function (ElementWithValue $element) {
                if (empty($element->getValue())) {
                    $element->addError(['Поле должно быть заполнено']);
                    return false;
                }

                return true;
            });
        }
    }
}
