<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlType;

class InputText extends ElementWithValue
{
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('text');
    }
}
