<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlMaxlength;
use Kavalhub\FormGenerator\Element\Trait\HtmlPattern;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputText extends ElementWithValue
{
    use HtmlType;
    use HtmlMaxlength;
    use HtmlPattern;
    use HtmlPlaceholder;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('text');
    }

    public function getHtml(): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
