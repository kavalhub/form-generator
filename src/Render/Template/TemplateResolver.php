<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Template;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class TemplateResolver
{
    /**
     * @param list<TemplateSourceInterface> $sources
     */
    public function __construct(
        private readonly array $sources,
    ) {
    }

    public function resolve(HtmlDecoratableInterface $element, TemplateResolutionContext $context): ?string
    {
        foreach ($this->sources as $source) {
            foreach ($source->candidates($element, $context) as $path) {
                if (is_file($path)) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * @param list<TemplateResolver> $resolvers
     */
    public static function chain(array $resolvers): self
    {
        $sources = [];
        foreach ($resolvers as $resolver) {
            $sources = [...$sources, ...$resolver->getSources()];
        }

        return new self($sources);
    }

    /**
     * @return list<TemplateSourceInterface>
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @return list<TemplateSourceInterface>
     */
    public static function defaultSources(
        string $extension,
        ?string $customPath,
        string $packagePath,
    ): array {
        return [
            new ElementPathTemplateSource($extension),
            new ClassNameInBaseTemplateSource($customPath, $extension),
            new ClassNameInBaseTemplateSource($packagePath, $extension),
        ];
    }

    public static function forDecorator(
        string $extension,
        ?string $customPath,
        string $packagePath,
    ): self {
        return new self(self::defaultSources($extension, $customPath, $packagePath));
    }

    /**
     * @param list<TemplateSourceInterface> $additionalSources
     */
    public function withSources(array $additionalSources): self
    {
        return new self([...$this->sources, ...$additionalSources]);
    }
}
