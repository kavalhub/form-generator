<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Request\Interface\RequestInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

readonly class ElementValidator implements ElementValidatorInterface
{
    public function __construct(private RequestInterface $request, private ElementInterface $element)
    {
    }

    public function validate(): bool
    {
        $validate = $this->getValidate();
        //if ($this->element instanceof ElementWithValue) {
            $value = $this->request->get($this->element->getName());
            if (isset($value)) {
                foreach ($value as $item) {
                    $this->element->setValue((string)$item);
                    if ($item === '' && $this->element->isRequired()) {
                        $this->element->addError(['Required field is required.']);
                        $validate[] = false;
                    }
                }
            }
            if (!in_array(false, $validate, true)) {
                $this->element->setValid();
            }
        //}
        foreach ($this->element->getCallbackValidatorList() as $callbackValidator) {
            $validate[] = $callbackValidator($this->element);
        }

        return !in_array(false, $validate, true);
    }

    public function getValidate(): array
    {
        $validate = ['true'];
        if ($this->element->getComposite()) {
            foreach (
                $this->element->getComposite()
                    ->getAll() as $child
            ) {
                if (method_exists($child, 'getName')) {
                    $validate[] = (new self($this->request, $child))->validate();
                }
            }
        }

        return $validate;
    }
}
