<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Template;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class ClassNameInBaseTemplateSource implements TemplateSourceInterface
{
    public function __construct(
        private readonly ?string $base,
        private readonly string $extension,
    ) {
    }

    public function candidates(HtmlDecoratableInterface $element, TemplateResolutionContext $context): array
    {
        if ($this->base === null || $this->base === '') {
            return [];
        }

        $paths = [$this->base . '/' . $context->className . $context->extension];
        if ($context->parentClassName !== '') {
            $paths[] = $this->base . '/' . $context->parentClassName . $context->extension;
        }

        return $paths;
    }
}
