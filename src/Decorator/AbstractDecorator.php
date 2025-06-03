<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator;

use Kavalhub\FormGenerator\Decorator\Interface\DecoratorInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

abstract class AbstractDecorator implements DecoratorInterface
{
    protected string $path = __DIR__;
    protected string $errorClass = '';
    protected string $successClass = '';
    protected ElementInterface $element;

    public function __construct(ElementInterface $element)
    {
        $this->element = clone $element;
    }

    public function getHtml(): string
    {
        if ($this->element->isError()) {
            $this->element->addClass([$this->getErrorClass()]);
        }

        if ($this->element->isValid() && !empty($this->element->getValue())) {
            $this->element->addClass([$this->getSuccessClass()]);
        }

        $className = (new \ReflectionClass($this->element))->getShortName();
        $parentClassName = (new \ReflectionClass(get_parent_class($this->element)))->getShortName();

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

        return $this->element->getHtml();
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