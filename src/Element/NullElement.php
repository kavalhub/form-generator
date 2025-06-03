<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

class NullElement extends ElementWithValue
{
    public function __construct(string $name = '')
    {
        parent::__construct($name);
    }
}
