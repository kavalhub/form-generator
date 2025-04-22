<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\CallbackValidator;
use Kavalhub\FormGenerator\Element\Trait\HtmlClass;
use Kavalhub\FormGenerator\Element\Trait\HtmlHidden;
use Kavalhub\FormGenerator\Element\Trait\HtmlId;
use Kavalhub\FormGenerator\Element\Trait\HtmlRequired;
use Kavalhub\FormGenerator\Element\Trait\TraitCollector;
use Kavalhub\FormGenerator\Element\Trait\Valid;
use SplObjectStorage;

class Element implements ElementInterface
{
    use CallbackValidator;
    use TraitCollector;
    use HtmlClass;
    use HtmlHidden;
    use HtmlId;
    use HtmlRequired;
    use Valid;

    protected ElementInterface $parent;

    public function setParent(ElementInterface $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getComposite(): ?self
    {
        return null;
    }

    public function getAll(): SplObjectStorage
    {
        return new SplObjectStorage();
    }
}
