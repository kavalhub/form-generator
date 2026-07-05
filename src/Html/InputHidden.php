<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputHidden extends HtmlElementWithValue
{
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('hidden');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
