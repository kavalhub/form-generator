<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlFor;
use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;

class Label extends HtmlElementWithName
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
        if ($this->element === null) {
            return false;
        }

        return $this->element->isRequired();
    }

    public function render(string $innerHtml = ''): string
    {
        $content = $this->allowHtml ? $this->label : HtmlEscaper::escape($this->label);

        return '<label' . $this->getHtmlTrait(['HtmlName']) . '>' . $content . '</label>';
    }
}
