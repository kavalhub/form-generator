<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithName;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputSubmit extends ElementWithName
{
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('submit');
    }

    public function getHtml(): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
