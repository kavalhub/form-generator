<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Blade;

use Kavalhub\FormGenerator\Decorator\AbstractDecorator;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

class BladeDecorator extends AbstractDecorator
{
    protected string $path = __DIR__ . '/../templates';
    protected string $errorClass = 'fg-blade-is-invalid';
    protected string $successClass = 'fg-blade-is-valid';

    private BladeViewFactory $viewFactory;

    public function __construct(
        HtmlDecoratableInterface $element,
        ?BladeViewFactory $viewFactory = null,
    ) {
        parent::__construct($element);
        $this->viewFactory = $viewFactory ?? new BladeViewFactory();
    }

    public function getHtml(): string
    {
        $this->element->addClass(['fg-blade-field-item']);

        return parent::getHtml();
    }

    protected function getTemplateExtension(): string
    {
        return '.php';
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
