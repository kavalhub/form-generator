<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Element\ElementWithName;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\HtmlFor;

class Label extends ElementWithName
{
    use HtmlFor;

    private string $label = '';

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function isRequired(): bool
    {
        if(empty($this->element)) {
            return false;
        }

        return $this->element->isRequired();
    }

    public function getHtml(string $value = ''): string
    {
        return '<label' . $this->getHtmlTrait(['HtmlName']) . '>' . $this->label . '</label>';
    }
}
