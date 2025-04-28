<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\HtmlFor;

class Label extends Element
{
    use HtmlFor;

    public function __construct(protected ElementInterface $element, protected string $label)
    {
        $this->setFor($element->getId());
        $this->setId($element->getId() . '_label');
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
