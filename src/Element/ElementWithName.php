<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\Name;

class ElementWithName extends Element
{
    use Name;

    public function __construct(string $name)
    {
        parent::__construct();
        $this->setName($name);
    }
}
