<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Form\Label;

class ElementWithName extends Element
{
    use HtmlName;

    public function __construct(string $name)
    {
        parent::__construct();
        $this->setName($name);
    }

    public function snapLabel(): self
    {
        if (!empty($this->parent)) {
            $test = $this->parent->getByType(Label::class)
                ->getByName($this->getName());
        }

        return $this;
    }

    /*public function validate(): bool
    {
        return true;
    }*/
}
