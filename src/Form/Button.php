<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;
use Kavalhub\FormGenerator\Element\Trait\Label;

class Button extends ElementWithValue
{
    use HtmlType;
    use Label;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('submit');
    }

    public function getHtml(string $value = ''): string
    {
        return '<button' . $this->getHtmlTrait() . '>' . $this->getLabel() . '</button>';
    }
}
