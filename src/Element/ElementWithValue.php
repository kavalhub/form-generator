<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\HtmlClass;
use Kavalhub\FormGenerator\Element\Trait\HtmlId;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Element\Trait\HtmlRequired;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;
use Kavalhub\FormGenerator\Element\Trait\TraitCollector;

class ElementWithValue extends Element
{
    use TraitCollector;
    use HtmlClass;
    use HtmlId;
    use HtmlName;
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
