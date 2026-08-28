<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Template;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class ElementPathTemplateSource implements TemplateSourceInterface
{
    public function __construct(
        private readonly string $extension,
    ) {
    }

    public function candidates(HtmlDecoratableInterface $element, TemplateResolutionContext $context): array
    {
        $elementPath = $element->getPath();
        if ($elementPath === '') {
            return [];
        }

        if ($this->isExplicitTemplateFile($elementPath, $context->extension)) {
            return [$elementPath];
        }

        if ($this->isExtensionlessTemplatePath($elementPath)) {
            return [$elementPath . $context->extension];
        }

        if (is_dir($elementPath)) {
            $paths = [$elementPath . '/' . $context->className . $context->extension];
            if ($context->parentClassName !== '') {
                $paths[] = $elementPath . '/' . $context->parentClassName . $context->extension;
            }

            return $paths;
        }

        return [];
    }

    private function isExplicitTemplateFile(string $path, string $extension): bool
    {
        return str_ends_with($path, $extension);
    }

    private function isExtensionlessTemplatePath(string $path): bool
    {
        return !str_contains(basename($path), '.');
    }
}
