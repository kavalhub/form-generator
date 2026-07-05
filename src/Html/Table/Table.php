<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Table;

use Kavalhub\FormGenerator\Html\HtmlCompositeElement;
use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;

class Table extends HtmlCompositeElement
{
    public function __construct()
    {
        parent::__construct();
        $this->setTag('table');
    }

    public function render(string $innerHtml = ''): string
    {
        foreach ($this->elementStorage as $element) {
            if ($element instanceof HtmlRenderableInterface) {
                $innerHtml .= $element->render();
            }
        }

        return '<table' . $this->getHtmlTrait() . '>' . $innerHtml . '</table>';
    }
}
