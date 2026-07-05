<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlName;

class Nav extends HtmlCompositeElementWithValue
{
    use HtmlName;

    public function __construct(string $name, string $tag = 'Nav')
    {
        parent::__construct($name);
        $this->setTag($tag);
    }

    public function render(string $innerHtml = ''): string
    {
        foreach ($this->elementStorage as $element) {
            if ($element instanceof HtmlRenderableInterface) {
                $innerHtml .= $element->render();
            }
        }

        return '<' . $this->getTag() . $this->getHtmlTrait() . '>' . $innerHtml . '</' . $this->getTag() . '>';
    }
}
