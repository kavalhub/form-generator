<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator;

use Kavalhub\FormGenerator\Decorator\Interface\DecoratorInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

class BootstrapDecorator
{
    private string $path = '/var/www/office-planet.ru/web/BootstrapDecorator';
    private string $errorClass = 'is-invalid';
    private string $successClass = 'is-valid';
    private ElementInterface $element;

    public function __construct(ElementInterface $element)
    {
        $this->element = $element;
    }

    public function getHtml(): string
    {
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