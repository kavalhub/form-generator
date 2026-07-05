<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator;

use Kavalhub\FormGenerator\Decorator\Interface\DecoratorInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

abstract class AbstractDecorator implements DecoratorInterface
{
    protected string $path = __DIR__;
    protected ?string $customPath = null;
    protected ?string $resourceBase = null;
    protected string $errorClass = '';
    protected string $successClass = '';
    protected HtmlDecoratableInterface $element;

    public function __construct(HtmlDecoratableInterface $element)
    {
        $this->element = clone $element;
        $this->resolveElementTemplatePath();
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
                return $this->renderTemplateFile($path);
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

    public function setResourceBase(string $path): self
    {
        $this->resourceBase = rtrim($path, '/');
        $this->resolveElementTemplatePath();

        return $this;
    }

    public function decorateChild(HtmlDecoratableInterface $element): static
    {
        $child = new static($element);
        if ($this->customPath !== null) {
            $child->customPath = $this->customPath;
        }
        if ($this->resourceBase !== null) {
            $child->resourceBase = $this->resourceBase;
        }
        $child->resolveElementTemplatePath();

        return $child;
    }

    protected function resolveElementTemplatePath(): void
    {
        $path = $this->element->getPath();
        if ($path === '' || $this->resourceBase === null) {
            return;
        }
        if (str_starts_with($path, '/') || file_exists($path)) {
            return;
        }

        $this->element->setPath($this->resourceBase . '/' . $path);
    }

    /**
     * @return list<string>
     */
    protected function resolveTemplatePaths(string $className, string $parentClassName): array
    {
        $extension = $this->getTemplateExtension();
        $paths = [];
        $elementPath = $this->element->getPath();

        if ($elementPath !== '') {
            if ($this->isExplicitTemplateFile($elementPath)) {
                $paths[] = $elementPath;
            } elseif ($this->isExtensionlessTemplatePath($elementPath)) {
                $paths[] = $elementPath . $extension;
            } elseif (is_dir($elementPath)) {
                $paths[] = $elementPath . '/' . $className . $extension;
                if ($parentClassName !== '') {
                    $paths[] = $elementPath . '/' . $parentClassName . $extension;
                }
            }
        }

        foreach ([$this->customPath, $this->path] as $base) {
            if ($base === null || $base === '') {
                continue;
            }
            $paths[] = $base . '/' . $className . $extension;
            if ($parentClassName !== '') {
                $paths[] = $base . '/' . $parentClassName . $extension;
            }
        }

        return $paths;
    }

    protected function isExplicitTemplateFile(string $path): bool
    {
        return str_ends_with($path, $this->getTemplateExtension());
    }

    protected function isExtensionlessTemplatePath(string $path): bool
    {
        return !str_contains(basename($path), '.');
    }

    protected function getTemplateExtension(): string
    {
        return '.php';
    }

    protected function renderTemplateFile(string $path): string
    {
        return include $path;
    }
}
