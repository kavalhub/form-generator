<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputColor extends ElementWithValue
{
    use HtmlType;
    use HtmlPlaceholder;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('color');
    }

    public function getHtml(string $value = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
