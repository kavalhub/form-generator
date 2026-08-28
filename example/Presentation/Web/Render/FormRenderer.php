<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Render;

use Kavalhub\Example\Presentation\Http\DecoratorMode;
use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Ajax\NullAjaxRenderStrategy;
use Kavalhub\FormGenerator\Blade\BladeAjaxRenderStrategy;
use Kavalhub\FormGenerator\Blade\BladeTheme;
use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Bootstrap\BootstrapTheme;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Tailwind\TailwindAjaxRenderStrategy;
use Kavalhub\FormGenerator\Tailwind\TailwindTheme;
use Kavalhub\FormGenerator\Twig\TwigAjaxRenderStrategy;
use Kavalhub\FormGenerator\Twig\TwigTheme;
use Kavalhub\FormGenerator\Twig\TwigViewFactory;

final class FormRenderer
{
    public const FACET_INPUT_TEMPLATE_BASE = 'CustomElements/InputText';
    public const BRAND_GROUP_TEMPLATE_BASE = 'elements/Brand/Group';

    public function __construct(
        private readonly string $resourcesPath = __DIR__ . '/../../../resources',
        private readonly ElementRenderer $elementRenderer = new ElementRenderer(),
    ) {
    }

    public function html(HtmlDecoratableInterface $element, DecoratorMode $mode): string
    {
        if ($mode === DecoratorMode::Html) {
            return $element->render();
        }

        $basePath = $this->templatePath($mode);

        return $this->elementRenderer->html($element, $this->theme($mode, $basePath));
    }

    public function templatePath(DecoratorMode $mode): string
    {
        return match ($mode) {
            DecoratorMode::Bootstrap => $this->resourcesPath . '/Bootstrap',
            DecoratorMode::Blade => $this->resourcesPath . '/Blade',
            DecoratorMode::Twig => $this->resourcesPath . '/Twig',
            DecoratorMode::Tailwind => $this->resourcesPath . '/Tailwind',
            DecoratorMode::Html => $this->resourcesPath,
        };
    }

    public function customTemplatePath(string $basename): string
    {
        return $basename;
    }

    public function ajaxStrategy(DecoratorMode $mode): AjaxRenderStrategyInterface
    {
        return match ($mode) {
            DecoratorMode::Html => new NullAjaxRenderStrategy(),
            DecoratorMode::Bootstrap => new BootstrapAjaxRenderStrategy($this->templatePath($mode)),
            DecoratorMode::Blade => new BladeAjaxRenderStrategy($this->templatePath($mode)),
            DecoratorMode::Twig => new TwigAjaxRenderStrategy($this->templatePath($mode), $this->twigViewFactory()),
            DecoratorMode::Tailwind => new TailwindAjaxRenderStrategy($this->templatePath($mode)),
        };
    }

    /**
     * @param array<string, mixed> $vars
     */
    public function renderView(string $relativePath, DecoratorMode $mode, array $vars): string
    {
        $shellMode = $mode === DecoratorMode::Html ? DecoratorMode::Bootstrap : $mode;
        $basePath = $this->templatePath($shellMode);
        $viewPath = $basePath . '/' . $relativePath;

        if ($shellMode === DecoratorMode::Twig) {
            return $this->twigViewFactory()->renderFile($viewPath . '.html.twig', $vars);
        }

        ob_start();
        extract($vars, EXTR_SKIP);
        include $viewPath . '.php';

        return (string)ob_get_clean();
    }

    private function theme(DecoratorMode $mode, string $basePath): \Kavalhub\FormGenerator\Render\RenderTheme
    {
        return match ($mode) {
            DecoratorMode::Bootstrap => BootstrapTheme::create($basePath, $basePath),
            DecoratorMode::Blade => BladeTheme::create($basePath, $basePath),
            DecoratorMode::Twig => TwigTheme::create($basePath, $basePath, $this->twigViewFactory()),
            DecoratorMode::Tailwind => TailwindTheme::create($basePath, $basePath),
            DecoratorMode::Html => throw new \InvalidArgumentException('Html mode has no theme.'),
        };
    }

    private function twigViewFactory(): TwigViewFactory
    {
        static $factory = null;
        if ($factory === null) {
            $cache = $this->resourcesPath . '/../../var/cache/twig';
            if (!is_dir($cache)) {
                @mkdir($cache, 0777, true);
            }
            $factory = new TwigViewFactory($cache);
        }

        return $factory;
    }
}
