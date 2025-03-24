<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element;

use Kavalhub\Form\Element\Trait\HtmlName;
use Kavalhub\Form\Element\Trait\HtmlRequired;
use Kavalhub\Form\Element\Trait\HtmlValue;

class ElementWithValue extends Element
{
    use HtmlName;
    use HtmlRequired;
    use HtmlValue;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function validate(): bool
    {
        return true;
    }
}
