<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\Label;

class Button extends ElementWithValue
{
    use Label;

    public function getHtml(string $value = ''): string
    {
        return '<button' . $this->getHtmlTrait() . '>' . $this->getLabel() . '</button>';
    }
}
