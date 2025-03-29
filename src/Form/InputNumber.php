<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Element\Trait\HtmlRequired;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputNumber extends ElementWithValue
{
    use HtmlType;
    use HtmlRequired;
    use HtmlPlaceholder;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('number');
    }

    public function getHtml(): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
