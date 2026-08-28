<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\Engine\TemplateEngineInterface;
use Kavalhub\FormGenerator\Render\Template\TemplateResolutionContext;
use Kavalhub\FormGenerator\Render\Template\TemplateResolver;

final class ElementRenderPipeline
{
    public function __construct(
        private readonly TemplateResolver $resolver,
        private readonly TemplateEngineInterface $engine,
        private readonly string $extension = '.php',
    ) {
    }

    public function render(HtmlDecoratableInterface $element, object $templateContext): string
    {
        $context = TemplateResolutionContext::forElement($element, $this->extension);
        $template = $this->resolver->resolve($element, $context);
        if ($template !== null) {
            return $this->engine->render($template, $templateContext);
        }

        return $element->render();
    }
}
