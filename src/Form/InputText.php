<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlList;
use Kavalhub\FormGenerator\Element\Trait\HtmlMaxlength;
use Kavalhub\FormGenerator\Element\Trait\HtmlPattern;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputText extends ElementWithValue
{
    use HtmlMaxlength;
    use HtmlPattern;
    use HtmlPlaceholder;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('text');
    }

    public function getHtml(string $value = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
