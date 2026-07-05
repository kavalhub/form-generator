<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator\Interface;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\InputSubmit;

interface ElementValidatorInterface
{
    public function checkSubmit(InputSubmit $submit): bool;

    public function handle(ElementInterface $element): bool;

    public function isValid(): ?bool;
}
