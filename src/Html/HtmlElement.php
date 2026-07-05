<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlAttributes;
use Kavalhub\FormGenerator\Html\Trait\HtmlElementCore;

abstract class HtmlElement extends Element implements HtmlDecoratableInterface
{
    use HtmlAttributes;
    use HtmlElementCore;
}
