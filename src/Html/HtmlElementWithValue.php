<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlElementCore;
use Kavalhub\FormGenerator\Html\Trait\HtmlInputAttributes;

abstract class HtmlElementWithValue extends ElementWithValue implements HtmlDecoratableInterface
{
    use HtmlElementCore;
    use HtmlInputAttributes;
}
