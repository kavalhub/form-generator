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
    private bool $allowHtml = false;

    public function setAllowHtml(bool $allowHtml = true): self
    {
        $this->allowHtml = $allowHtml;

        return $this;
    }

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
        $content = $this->allowHtml ? $this->label : \Kavalhub\FormGenerator\Util\HtmlEscaper::escape($this->label);

        return '<label' . $this->getHtmlTrait(['HtmlName']) . '>' . $content . '</label>';
    }
}
