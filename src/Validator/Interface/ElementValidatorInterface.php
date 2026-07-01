<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator\Interface;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

interface ElementValidatorInterface
{
    public function handle(ElementInterface $element): bool;
}
