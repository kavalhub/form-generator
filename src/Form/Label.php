<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Element\ElementWithName;
use Kavalhub\FormGenerator\Element\Trait\HtmlFor;

class Label extends Element
{
    use HtmlFor;
    protected string $label = '';

    public function __construct(ElementWithName $element, string $label)
    {
        $this->setFor($element->getId());
        $this->label = $label;
    }

    public function getHtml(): string
    {
        return '<label' . $this->getHtmlTrait() . '><h4>' . $this->label . '</h4></label>';
    }
}
