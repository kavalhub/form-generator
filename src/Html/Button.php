<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Trait\Label;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class Button extends HtmlElementWithValue
{
    use HtmlType;
    use Label;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('submit');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<button' . $this->getHtmlTrait() . '>' . $this->getLabel() . '</button>';
    }
}
