<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Table;

use Kavalhub\FormGenerator\Html\HtmlElement;

class Th extends HtmlElement
{
    public function __construct(private readonly string $value)
    {
        parent::__construct();
        $this->setTag('th');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<th' . $this->getHtmlTrait() . '>' . $this->value . '</th>';
    }
}
