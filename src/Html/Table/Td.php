<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Table;

use Kavalhub\FormGenerator\Html\HtmlElement;
use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;

class Td extends HtmlElement
{
    public function __construct(private readonly string $value)
    {
        parent::__construct();
        $this->setTag('td');
    }

    private bool $allowHtml = false;

    public function setAllowHtml(bool $allow = true): self
    {
        $this->allowHtml = $allow;

        return $this;
    }

    public function render(string $innerHtml = ''): string
    {
        $content = $this->allowHtml ? $this->value : HtmlEscaper::escape($this->value);

        return '<td' . $this->getHtmlTrait() . '>' . $content . '</td>';
    }
}
