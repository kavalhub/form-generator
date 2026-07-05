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

    public function render(string $innerHtml = ''): string
    {
        return '<td' . $this->getHtmlTrait() . '>' . HtmlEscaper::escape($this->value) . '</td>';
    }
}
