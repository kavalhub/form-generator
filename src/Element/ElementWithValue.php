<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\Error;
use Kavalhub\FormGenerator\Element\Trait\HtmlList;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;

class ElementWithValue extends ElementWithName
{
    use Error;
    use HtmlList;
    use HtmlValue;
}
