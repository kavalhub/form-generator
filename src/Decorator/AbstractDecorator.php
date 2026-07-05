<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator;

use Kavalhub\FormGenerator\Decorator\Interface\DecoratorInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

abstract class AbstractDecorator implements DecoratorInterface
{
    protected string $path = __DIR__;
    protected ?string $customPath = null;
    protected string $errorClass = '';
    protected string $successClass = '';
    protected HtmlDecoratableInterface $element;

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

        $className = (new \ReflectionClass($this->element))->getShortName();
        $parentClass = get_parent_class($this->element);
        $parentClassName = $parentClass ? (new \ReflectionClass($parentClass))->getShortName() : '';

        foreach ($this->resolveTemplatePaths($className, $parentClassName) as $path) {
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
        $this->customPath = rtrim($path, '/');

        return $this;
    }

    /**
     * @return list<string>
     */
    protected function resolveTemplatePaths(string $className, string $parentClassName): array
    {
        $bases = array_values(array_filter([
            $this->element->getPath() !== '' ? $this->element->getPath() : null,
            $this->customPath,
            $this->path,
        ], static fn (?string $base): bool => $base !== null && $base !== ''));

        $paths = [];
        foreach ($bases as $base) {
            $paths[] = $base . '/' . $className . '.php';
        }
        if ($parentClassName !== '') {
            foreach ($bases as $base) {
                $paths[] = $base . '/' . $parentClassName . '.php';
            }
        }

        return $paths;
    }
}
