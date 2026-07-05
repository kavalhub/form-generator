<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\Name;

class CompositeElementWithName extends CompositeElement
{
    use Name;

    public function __construct(string $name)
    {
        $this->setName($name);
        parent::__construct();
    }

    public function addElement(\Kavalhub\FormGenerator\Element\Interface\ElementInterface $element, mixed $info = null): self
    {
        $element->setParent($this);

        return parent::addElement($element, $info);
    }
}
