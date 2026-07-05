<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Interface\CsrfProtectable;
use Kavalhub\FormGenerator\Element\Trait\CsrfProtection;
use Kavalhub\FormGenerator\Element\Trait\Name;
use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlEnctype;
use Kavalhub\FormGenerator\Html\Trait\HtmlMethod;
use Kavalhub\FormGenerator\Html\Trait\HtmlName;
use Kavalhub\FormGenerator\Html\Trait\HtmlNovalidate;

class Form extends HtmlCompositeElement implements CsrfProtectable
{
    use CsrfProtection {
        enableCsrf as private enableCsrfProtection;
    }
    use HtmlEnctype;
    use HtmlMethod;
    use HtmlName;
    use HtmlNovalidate;
    use Name;

    public function __construct(string $name)
    {
        $this->setName($name);
        parent::__construct();
    }

    public function enableCsrf(): self
    {
        if ($this->isCsrfEnabled()) {
            return $this;
        }
        $this->enableCsrfProtection();
        $this->addElement(
            (new InputHidden($this->csrfFieldName))->setValue($this->getOrCreateCsrfToken())
        );

        return $this;
    }

    public function render(string $innerHtml = ''): string
    {
        return $this->renderWithWrapper();
    }

    public function renderWithWrapper(?string $tag = null): string
    {
        $openTag = $tag ? '<' . $tag . '>' : '';
        $closeTag = $tag ? '</' . $tag . '>' : '';
        $html = '';
        foreach ($this->elementStorage as $element) {
            if ($element instanceof HtmlRenderableInterface) {
                $html .= $openTag . $element->render() . $closeTag;
            }
        }

        return '<form' . $this->getHtmlTrait() . '>' . $html . '</form>';
    }
}
