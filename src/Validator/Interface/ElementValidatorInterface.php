<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator\Interface;
use Kavalhub\FormGenerator\Element\Element;

interface ElementValidatorInterface
{
    public function handle(Element $element): bool;
}
