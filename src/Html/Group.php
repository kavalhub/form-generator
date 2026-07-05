<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Trait\Name;
use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlName;

class Group extends HtmlCompositeElement
{
    use HtmlName;
    use Name;

    public function __construct(string $name, string $tag = 'div')
    {
        $this->setName($name);
        $this->setTag($tag);
        parent::__construct();
    }

    public function render(string $innerHtml = ''): string
    {
        foreach ($this->elementStorage as $element) {
            if ($element instanceof HtmlRenderableInterface) {
                $innerHtml .= $element->render();
            }
        }

        return '<' . $this->getTag() . $this->getHtmlTrait(['HtmlName']) . '>' . $innerHtml . '</' . $this->getTag() . '>';
    }
}
