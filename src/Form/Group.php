<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;

class Group extends CompositeElement
{
    use HtmlName;

    public function __construct(string $name)
    {
        $this->setName($name);
        parent::__construct();
    }

    public function getHtml(string $tag = null): string
    {
        $openTag = $tag ? '<' . $tag . '>' : '';
        $closeTag = $tag ? '</' . $tag . '>' : '';
        $html = '';
        foreach ($this->elementStorage as $element) {
            $html .= $openTag . $element->getHtml() . $closeTag;
        }

        return '<div' . $this->getHtmlTrait() . '>' . $html . '</div>';
    }
}
