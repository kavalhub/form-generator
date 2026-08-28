<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Blade\Engine;

use Kavalhub\FormGenerator\Blade\BladeViewFactory;
use Kavalhub\FormGenerator\Render\RenderContextInterface;
use Kavalhub\FormGenerator\Render\Engine\TemplateEngineInterface;

final class BladeTemplateEngine implements TemplateEngineInterface
{
    public function __construct(
        private readonly BladeViewFactory $viewFactory,
    ) {
    }

    public function render(string $path, object $context): string
    {
        if (!$context instanceof RenderContextInterface) {
            throw new \InvalidArgumentException('Blade templates require a RenderContext.');
        }

        return $this->viewFactory->renderFile($path, [
            'element' => $context->getElement(),
            'decorator' => $context,
        ]);
    }
}
