<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Template;

final readonly class TemplateResolutionContext
{
    public function __construct(
        public string $className,
        public string $parentClassName,
        public string $extension,
    ) {
    }

    public static function forElement(object $element, string $extension): self
    {
        $className = (new \ReflectionClass($element))->getShortName();
        $parent = get_parent_class($element);
        $parentClassName = $parent ? (new \ReflectionClass($parent))->getShortName() : '';

        return new self($className, $parentClassName, $extension);
    }
}
