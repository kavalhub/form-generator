<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlAccept;
use Kavalhub\FormGenerator\Element\Trait\HtmlMultiple;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputFile extends ElementWithValue
{
    use HtmlAccept;
    use HtmlMultiple;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('file');
    }

    public function getHtml(string $value = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
