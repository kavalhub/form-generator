<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Interface;

use Kavalhub\FormGenerator\Element\Element;

interface CompositeElementInterface
{
    public function addElement(ElementInterface $element): self;

    public function removeElement(ElementInterface $element): self;

    public function getByName(string $name, bool $extract = false): Element;
}
