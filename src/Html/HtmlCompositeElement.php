<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlElementCore;
use Kavalhub\FormGenerator\Html\Trait\PropagatesHtmlAjaxToChildren;

abstract class HtmlCompositeElement extends CompositeElement implements HtmlDecoratableInterface
{
    use HtmlElementCore;
    use PropagatesHtmlAjaxToChildren;
}
