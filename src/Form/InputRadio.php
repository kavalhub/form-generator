<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlChecked;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;
use Kavalhub\FormGenerator\Element\Trait\Label;

class InputRadio extends ElementWithValue
{
    use HtmlChecked;
    use HtmlType;
    use Label;

    public function __construct(string $name, string $value)
    {
        parent::__construct($name);
        $this->setId($this->getId() . '_'. $value);
        $this->setType('radio');
        $this->value = $value;
    }

    public function setValue(string $value): self
    {
        if ($value === $this->getValue()) {
            $this->setChecked();
        }
        return $this;
    }

    public function setValid(bool $valid = true): self
    {
        return $this;
    }

    public function getHtml(): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
