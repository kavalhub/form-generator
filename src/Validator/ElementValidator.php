<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class ElementValidator implements ElementValidatorInterface
{
    public function __construct(private Request $request)
    {
    }
    public function validate(ElementInterface $element): bool
    {
        return true;
    }
}
