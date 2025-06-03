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
    private ?bool $valid = null;
    private array $checkList = [];

    public function __construct(private readonly RequestInterface $request)
    {
    }

    public function checkSubmit(InputSubmit $submit): bool
    {
        return !empty($this->request->get($submit->getFormName()));
    }

    public function handle(ElementInterface $element): bool
    {
        if ($element->getComposite()) {
            foreach (
                $element->getComposite()
                    ->getAll() as $composite
            ) {
                $this->checkList[] = (new self($this->request))->handle($composite);
            }
        }
        if (method_exists($element, 'setValue' )) {
            $this->request->setValue($element);
            $this->required($element);
        }
        foreach ($element->getCallbackValidatorList() as $callbackValidator) {
            $this->checkList[] = $callbackValidator($element);
        }
        if (!in_array(false, $this->checkList, true)) {
            $element->setValid();
            $this->valid = true;
            return true;
        }
        $this->valid = false;

        return false;
    }

    private function required(ElementInterface $element): void
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

    public function isValid(): ?bool
    {
        return $this->valid;
    }
}
