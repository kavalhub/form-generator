<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\Engine\PhpTemplateEngine;
use Kavalhub\FormGenerator\Render\Engine\TemplateEngineInterface;
use Kavalhub\FormGenerator\Render\Template\ClassNameInBaseTemplateSource;
use Kavalhub\FormGenerator\Render\Template\ElementPathTemplateSource;
use Kavalhub\FormGenerator\Render\Template\TemplateResolver;

final class RenderTheme
{
    private ?ElementRenderPipeline $pipeline = null;

    /**
     * @param list<string> $elementClasses
     * @param list<Template\TemplateSourceInterface> $additionalSources
     */
    public function __construct(
        private readonly string $packagePath,
        private readonly TemplateEngineInterface $engine,
        private readonly string $extension = '.php',
        private readonly ?string $customPath = null,
        private readonly ?string $resourceBase = null,
        private readonly string $errorClass = '',
        private readonly string $successClass = '',
        private readonly array $elementClasses = [],
        private readonly array $additionalSources = [],
        private readonly string $fieldErrorWrapper = '%s',
    ) {
    }

    public function withResourceBase(?string $resourceBase): self
    {
        return new self(
            $this->packagePath,
            $this->engine,
            $this->extension,
            $this->customPath,
            $resourceBase,
            $this->errorClass,
            $this->successClass,
            $this->elementClasses,
            $this->additionalSources,
            $this->fieldErrorWrapper,
        );
    }

    public function withCustomPath(?string $customPath): self
    {
        return new self(
            $this->packagePath,
            $this->engine,
            $this->extension,
            $customPath,
            $this->resourceBase,
            $this->errorClass,
            $this->successClass,
            $this->elementClasses,
            $this->additionalSources,
            $this->fieldErrorWrapper,
        );
    }

    /**
     * @param list<Template\TemplateSourceInterface> $additionalSources
     */
    public function withAdditionalSources(array $additionalSources): self
    {
        return new self(
            $this->packagePath,
            $this->engine,
            $this->extension,
            $this->customPath,
            $this->resourceBase,
            $this->errorClass,
            $this->successClass,
            $this->elementClasses,
            [...$this->additionalSources, ...$additionalSources],
            $this->fieldErrorWrapper,
        );
    }

    public static function plain(?string $resourceBase = null): self
    {
        return self::plainPhp($resourceBase);
    }

    public static function plainPhp(?string $resourceBase = null): self
    {
        return new self(
            packagePath: '',
            engine: new PhpTemplateEngine(),
            resourceBase: $resourceBase,
        );
    }

    public static function bootstrap(?string $resourceBase = null, ?string $customPath = null): self
    {
        if (!class_exists(\Kavalhub\FormGenerator\Bootstrap\BootstrapTheme::class)) {
            throw new \RuntimeException(
                'Install kavalhub/form-generator-bootstrap to use RenderTheme::bootstrap().',
            );
        }

        return \Kavalhub\FormGenerator\Bootstrap\BootstrapTheme::create($resourceBase, $customPath);
    }

    public function prepareElement(HtmlDecoratableInterface $element): HtmlDecoratableInterface
    {
        $element = clone $element;

        if ($this->elementClasses !== []) {
            $element->addClass($this->elementClasses);
        }

        if ($element->isError() && $this->errorClass !== '') {
            $element->addClass([$this->errorClass]);
        }

        if (
            $element->isValid()
            && $this->successClass !== ''
            && method_exists($element, 'getValue')
            && !empty($element->getValue())
        ) {
            $element->addClass([$this->successClass]);
        }

        (new ElementResourcePathResolver())->applyResourceBase($element, $this->resourceBase);

        return $element;
    }

    public function createResolver(): TemplateResolver
    {
        return new TemplateResolver([
            new ElementPathTemplateSource($this->extension),
            new ClassNameInBaseTemplateSource($this->customPath, $this->extension),
            new ClassNameInBaseTemplateSource($this->packagePath !== '' ? $this->packagePath : null, $this->extension),
            ...$this->additionalSources,
        ]);
    }

    public function pipeline(): ElementRenderPipeline
    {
        return $this->pipeline ??= new ElementRenderPipeline(
            $this->createResolver(),
            $this->engine,
            $this->extension,
        );
    }

    public function getErrorClass(): string
    {
        return $this->errorClass;
    }

    public function getSuccessClass(): string
    {
        return $this->successClass;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function formatFieldError(string $errors): string
    {
        if ($errors === '') {
            return '';
        }

        return sprintf($this->fieldErrorWrapper, $errors);
    }
}
