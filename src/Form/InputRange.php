<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlMax;
use Kavalhub\FormGenerator\Element\Trait\HtmlMin;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Element\Trait\HtmlStep;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputRange extends ElementWithValue
{
    use HtmlMin;
    use HtmlMax;
    use HtmlStep;
    use HtmlType;
    use HtmlPlaceholder;


    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('range');
    }

    public function getHtml(): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
