<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;

class CompositeElementWithName extends CompositeElement
{
    use HtmlName;
    public function __construct(string $name, string $tag = '')
    {
        $this->setName($name);
        parent::__construct($tag);
    }

    public function addElement(ElementInterface $element, mixed $info = null): self
    {
        $element->setParent($this);

        return parent::addElement($element, $info);
    }
}