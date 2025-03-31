<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\HtmlName;

class ElementWithName extends ElementWithValue
{
    use HtmlName;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function validate(): bool
    {
        return true;
    }
}
