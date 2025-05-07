<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlSelected;
use Kavalhub\FormGenerator\Element\Trait\Label;

class Option extends ElementWithValue
{
    use HtmlSelected;
    use Label;

    public function __construct(string $value = '', string $label = '')
    {
        parent::__construct('');
        $this->value = $value;
        $this->label = $label;
    }

    public function getFormName(): string
    {
        return $this->parent->getFormName();
    }

    public function setValue(string $value): self
    {
        if ($value === $this->getValue()) {
            $this->setSelected();
        }
        return $this;
    }

    public function getHtml(): string
    {
        return '<option' . $this->getHtmlTrait(['HtmlId','HtmlName']) . '>' . $this->getLabel() . '</option>';
    }
}
