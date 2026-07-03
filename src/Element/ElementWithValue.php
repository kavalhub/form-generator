<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\HtmlAutocomplete;
use Kavalhub\FormGenerator\Element\Trait\HtmlAutofocus;
use Kavalhub\FormGenerator\Element\Trait\HtmlDisabled;
use Kavalhub\FormGenerator\Element\Trait\HtmlList;
use Kavalhub\FormGenerator\Element\Trait\HtmlReadonly;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;

class ElementWithValue extends ElementWithName
{
    use HtmlAutocomplete;
    use HtmlAutofocus;
    use HtmlDisabled;
    use HtmlList;
    use HtmlReadonly;
    use HtmlValue;
}
