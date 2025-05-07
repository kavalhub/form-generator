<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator\Bootstrap;

use Kavalhub\FormGenerator\Decorator\Interface\DecoratorInterface;
use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

class BootstrapDecorator implements DecoratorInterface
{
    private string $path = __DIR__;
    private string $errorClass = 'is-invalid';
    private string $successClass = 'is-valid';
    private ElementInterface $element;

    public function __construct(ElementInterface $element)
    {
        $this->element = clone $element;
    }

    public function getHtml(): string
    {
        if ($this->element->isError()) {
            $this->element->addClass([$this->getErrorClass()]);
        }
        if ($this->element->isValid()) {
            $this->element->addClass([$this->getSuccessClass()]);
        }
        $this->element->addClass(['mb-2']);
        $className = explode('\\', get_class($this->element));
        $className = end($className);

        return match (true) {
            file_exists($this->element->getPath() . '/' . $className . '.php') => include $this->element->getPath()
                . '/' . $className . '.php',
            file_exists($this->path . '/' . $className . '.php') => include $this->path . '/' . $className . '.php',
            default => $this->element->getHtml(),
        };
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