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
        if ($this->element instanceof ElementWithValue) {
            if ($this->element->getError()) {
                $this->element->addClass([$this->getErrorClass()]);
            }
            if ($this->element->isValid()) {
                $this->element->addClass([$this->getSuccessClass()]);
            }
        }
        $className = explode('\\', get_class($this->element));
        $className = end($className);
        $template = $this->path . '/' . $className . '.php';
        if (file_exists($template)) {
            return include $template;
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
}