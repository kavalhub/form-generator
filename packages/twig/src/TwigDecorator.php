<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Twig;

use Kavalhub\FormGenerator\Decorator\AbstractDecorator;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

class TwigDecorator extends AbstractDecorator
{
    protected string $path = __DIR__ . '/../templates';
    protected string $errorClass = 'fg-twig-is-invalid';
    protected string $successClass = 'fg-twig-is-valid';

    private TwigViewFactory $viewFactory;

    public function __construct(
        HtmlDecoratableInterface $element,
        ?TwigViewFactory $viewFactory = null,
    ) {
        parent::__construct($element);
        $this->viewFactory = $viewFactory ?? new TwigViewFactory();
    }

    public function getHtml(): string
    {
        $this->element->addClass(['fg-twig-field-item']);

        return parent::getHtml();
    }

    protected function getTemplateExtension(): string
    {
        return '.html.twig';
    }

    protected function renderTemplateFile(string $path): string
    {
        return $this->viewFactory->renderFile($path, [
            'element' => $this->element,
            'decorator' => $this,
        ]);
    }

    public function decorateChild(HtmlDecoratableInterface $element): static
    {
        $child = new static($element, $this->viewFactory);
        if ($this->customPath !== null) {
            $child->customPath = $this->customPath;
        }
        if ($this->resourceBase !== null) {
            $child->resourceBase = $this->resourceBase;
        }
        $child->resolveElementTemplatePath();

        return $child;
    }
}
