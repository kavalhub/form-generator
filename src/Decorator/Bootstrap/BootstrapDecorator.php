<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator\Bootstrap;

use Kavalhub\FormGenerator\Decorator\Interface\DecoratorInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

class BootstrapDecorator implements DecoratorInterface
{
    private string $path = __DIR__;
    private string $errorClass = 'is-invalid';
    private string $successClass = 'is-valid';
    private HtmlDecoratableInterface $element;

    public function __construct(HtmlDecoratableInterface $element)
    {
        $this->element = clone $element;
    }

    public function getHtml(): string
    {
        if ($this->element->isError()) {
            $this->element->addClass([$this->getErrorClass()]);
        }

        if ($this->element->isValid() && method_exists($this->element, 'getValue') && !empty($this->element->getValue())) {
            $this->element->addClass([$this->getSuccessClass()]);
        }

        $this->element->addClass(['mb-2']);

        $className = (new \ReflectionClass($this->element))->getShortName();
        $parentClass = get_parent_class($this->element);
        $parentClassName = $parentClass ? (new \ReflectionClass($parentClass))->getShortName() : '';

        $paths = [
            $this->element->getPath() . '/' . $className . '.php',
            $this->path . '/' . $className . '.php',
            $this->path . '/' . $parentClassName . '.php',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return include $path;
            }
        }

        return $this->element->render();
    }

    public function getErrorClass(): string
    {
        return $this->errorClass;
    }

    public function getSuccessClass(): string
    {
        return $this->successClass;
    }

    public function setTemplate(string $path): DecoratorInterface
    {
        $this->path = $path;

        return $this;
    }
}
