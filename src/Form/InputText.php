<?php
declare(strict_types=1);

namespace Kavalhub\Form\Form;

use Kavalhub\Form\Element\ElementWithValue;
use Kavalhub\Form\Element\Trait\HtmlType;

class InputText extends ElementWithValue
{
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('text');
    }
}
