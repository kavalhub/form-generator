<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\Label;
use Kavalhub\FormGenerator\Html\Trait\HtmlChecked;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputRadio extends HtmlElementWithValue
{
    use HtmlChecked;
    use HtmlType;
    use Label;

    public function __construct(string $name, string $defaultValue = '')
    {
        parent::__construct($name);
        $this->setId($this->getName() . '_' . $defaultValue);
        $this->setType('radio');
        $this->setDefaultValue($defaultValue);
    }

    public function setDefaultValue(string $defaultValue): ElementWithValue
    {
        $this->setId($this->getName() . '_' . $defaultValue);

        return parent::setDefaultValue($defaultValue);
    }

    public function setValue(string $value): self
    {
        $this->setChecked(false);
        if ($value === $this->getValue()) {
            $this->setChecked();
        }

        return $this;
    }

    public function setValid(bool $valid = true): self
    {
        return $this;
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
