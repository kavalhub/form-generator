<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Element\Trait\HtmlSelected;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;
use Kavalhub\FormGenerator\Element\Trait\Label;

class Option extends Element
{
    use HtmlSelected;
    use HtmlValue;
    use Label;

    public function __construct(string $value, string $label)
    {
        $this->value = $value;
        $this->label = $label;
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
        return '<option' . $this->getHtmlTrait() . '>' . $this->getLabel() . '</option>';
    }
}
