<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithName;
use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlFor;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;

class Label extends ElementWithValue
{
    use HtmlFor;
    use HtmlValue;

    public function __construct(ElementWithName $element, string $label)
    {
        $this->setFor($element->getId());
        $this->setValue($label);
    }

    public function getHtml(): string
    {
        return '<label' . $this->getHtmlTrait() . '>' . $this->getValue() . '</label>';
    }
}
