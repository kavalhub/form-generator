<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlFor;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;

class Label extends Element
{
    use HtmlFor;

    public function __construct(protected ElementWithValue $element, protected string $label)
    {
        $this->setFor($element->getId());
        $this->setId('label_' . $element->getId());
    }

    public function isRequired(): bool
    {
        return $this->element->isRequired();
    }

    public function getHtml(): string
    {
        return '<label' . $this->getHtmlTrait(['HtmlName']) . '>' . $this->label . '</label>';
    }
}
