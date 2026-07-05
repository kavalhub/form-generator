<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\ElementWithName;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlAttributes;
use Kavalhub\FormGenerator\Html\Trait\HtmlElementCore;

abstract class HtmlElementWithName extends ElementWithName implements HtmlDecoratableInterface
{
    use HtmlAttributes;
    use HtmlElementCore;
}
