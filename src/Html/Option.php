<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Trait\Label;
use Kavalhub\FormGenerator\Html\Trait\HtmlSelected;

class Option extends HtmlElementWithValue
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

    public function render(string $innerHtml = ''): string
    {
        return '<option' . $this->getHtmlTrait(['HtmlId', 'HtmlName']) . '>' . $this->getLabel() . '</option>';
    }
}
