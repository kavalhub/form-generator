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
        if ($this->element instanceof ElementWithValue) {
            $value = $this->request->get($this->element->getName())[0];
            if (isset($value)) {
                $this->element->setValue((string)$value);
            }
            if(empty($value) && $this->element->isRequired()) {
                $this->element->addError(['Required field is required.']);
                return false;
            }
            $this->element->setValid();
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
