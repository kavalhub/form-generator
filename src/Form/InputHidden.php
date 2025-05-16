<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlRequired;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputHidden extends ElementWithValue
{
    use HtmlType;
    use HtmlRequired;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('hidden');
    }

    public function getHtml(string $value = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
