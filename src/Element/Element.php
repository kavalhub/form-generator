<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element;

use Kavalhub\Form\Element\Interface\ElementInterface;
use Kavalhub\Form\Element\Trait\HtmlClass;
use Kavalhub\Form\Element\Trait\HtmlHidden;
use Kavalhub\Form\Element\Trait\HtmlId;
use Kavalhub\Form\Element\Trait\TraitHtmlCollector;

class Element implements ElementInterface
{
    use TraitHtmlCollector;
    use HtmlClass;
    use HtmlHidden;
    use HtmlId;

    public function getComposite(): ?self
    {
        return null;
    }
}
