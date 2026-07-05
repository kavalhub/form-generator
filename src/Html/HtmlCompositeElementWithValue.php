<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\CompositeElementWithValue;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlCompositeAttributes;
use Kavalhub\FormGenerator\Html\Trait\HtmlElementCore;
use Kavalhub\FormGenerator\Html\Trait\HtmlValue;

abstract class HtmlCompositeElementWithValue extends CompositeElementWithValue implements HtmlDecoratableInterface
{
    use HtmlCompositeAttributes;
    use HtmlElementCore;
    use HtmlValue;
}
