<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Twig\Engine;

use Kavalhub\FormGenerator\Render\RenderContext;
use Kavalhub\FormGenerator\Render\RenderContextInterface;
use Kavalhub\FormGenerator\Render\Engine\TemplateEngineInterface;
use Kavalhub\FormGenerator\Twig\TwigViewFactory;

final class TwigTemplateEngine implements TemplateEngineInterface
{
    public function __construct(
        private readonly TwigViewFactory $viewFactory,
    ) {
    }

    public function render(string $path, object $context): string
    {
        if (!$context instanceof RenderContextInterface) {
            throw new \InvalidArgumentException('Twig templates require a RenderContext.');
        }

        return $this->viewFactory->renderFile($path, [
            'element' => $context->getElement(),
            'decorator' => $context,
        ]);
    }
}
