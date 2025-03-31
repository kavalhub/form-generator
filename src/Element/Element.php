<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\HtmlClass;
use Kavalhub\FormGenerator\Element\Trait\HtmlHidden;
use Kavalhub\FormGenerator\Element\Trait\HtmlId;
use Kavalhub\FormGenerator\Element\Trait\TraitCollector;
use ReflectionClass;

class Element implements ElementInterface
{
    use TraitCollector;
    use HtmlClass;
    use HtmlHidden;
    use HtmlId;

    public function getComposite(): ?self
    {
        return null;
    }
}
