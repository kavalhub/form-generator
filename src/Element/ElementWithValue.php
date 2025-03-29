<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Element\Trait\HtmlRequired;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;

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
