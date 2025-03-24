<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Interface;

use Kavalhub\Form\Element\Element;

interface CompositeElementInterface
{
    public function addElement(ElementInterface $element): self;
    public function getById(string $id): Element;
}
