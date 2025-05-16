<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\CallbackValidator;
use Kavalhub\FormGenerator\Element\Trait\Error;
use Kavalhub\FormGenerator\Element\Trait\HtmlClass;
use Kavalhub\FormGenerator\Element\Trait\HtmlHidden;
use Kavalhub\FormGenerator\Element\Trait\HtmlId;
use Kavalhub\FormGenerator\Element\Trait\HtmlRequired;
use Kavalhub\FormGenerator\Element\Trait\Observable;
use Kavalhub\FormGenerator\Element\Trait\Path;
use Kavalhub\FormGenerator\Element\Trait\TraitCollector;
use Kavalhub\FormGenerator\Element\Trait\Valid;
use Kavalhub\FormGenerator\Form\Label;
use SplObjectStorage;

class Element implements ElementInterface
{
    use CallbackValidator;
    use Error;
    use HtmlClass;
    use HtmlHidden;
    use HtmlId;
    use HtmlRequired;
    use Observable;
    use Path;
    use TraitCollector;
    use Valid;

    protected ElementInterface $parent;
    protected SplObjectStorage $elementStorage;

    public function __construct(protected string $tag = 'div')
    {
        $this->observer = new SplObjectStorage();
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getValueArray(): array
    {
        return [];
    }

    public function setParent(ElementInterface $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): ElementInterface
    {
        return $this->parent;
    }

    public function getComposite(): ?self
    {
        return null;
    }

    public function getAll(): SplObjectStorage
    {
        return new SplObjectStorage();
    }

    public function snapLabel(string $label = ''): self
    {
        if (!empty($this->parent)) {
            $test = $this->parent->getByType(Label::class)
                ->getByName($this->getName());
        }

        return $this;
    }

    public function getHtml(string $value = ''): string
    {
        return '<' . $this->tag . $this->getHtmlTrait() . '>' . $value . '</' . $this->tag .'>';
    }
}
