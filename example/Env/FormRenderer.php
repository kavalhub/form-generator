<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Ajax\NullAjaxRenderStrategy;
use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator;
use Kavalhub\FormGenerator\Blade\BladeAjaxRenderStrategy;
use Kavalhub\FormGenerator\Blade\BladeDecorator;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Tailwind\TailwindAjaxRenderStrategy;
use Kavalhub\FormGenerator\Tailwind\TailwindDecorator;
use Kavalhub\FormGenerator\Twig\TwigAjaxRenderStrategy;
use Kavalhub\FormGenerator\Twig\TwigDecorator;
use Kavalhub\FormGenerator\Twig\TwigViewFactory;

final class FormRenderer
{
    private const RESOURCES = __DIR__ . '/../../resources';

    public const FACET_INPUT_TEMPLATE_BASE = 'CustomElements/InputText';
    public const BRAND_GROUP_TEMPLATE_BASE = 'elements/Brand/Group';

    public static function html(HtmlDecoratableInterface $element, ?DecoratorMode $mode = null): string
    {
        $mode ??= DecoratorMode::fromRequest();
        if ($mode === DecoratorMode::Html) {
            return $element->render();
        }

        $basePath = self::templatePath($mode);
        $decorator = match ($mode) {
            DecoratorMode::Bootstrap => (new BootstrapDecorator($element))
                ->setResourceBase($basePath)
                ->setTemplate($basePath),
            DecoratorMode::Blade => (new BladeDecorator($element))
                ->setResourceBase($basePath)
                ->setTemplate($basePath),
            DecoratorMode::Twig => (new TwigDecorator($element, self::twigViewFactory()))
                ->setResourceBase($basePath)
                ->setTemplate($basePath),
            DecoratorMode::Tailwind => (new TailwindDecorator($element))
                ->setResourceBase($basePath)
                ->setTemplate($basePath),
        };

        return $decorator->getHtml();
    }

    public static function templatePath(DecoratorMode $mode): string
    {
        return match ($mode) {
            DecoratorMode::Bootstrap => self::RESOURCES . '/Bootstrap',
            DecoratorMode::Blade => self::RESOURCES . '/Blade',
            DecoratorMode::Twig => self::RESOURCES . '/Twig',
            DecoratorMode::Tailwind => self::RESOURCES . '/Tailwind',
            DecoratorMode::Html => self::RESOURCES,
        };
    }

    public static function customTemplatePath(string $basename, ?DecoratorMode $mode = null): string
    {
        return $basename;
    }

    public static function ajaxStrategy(DecoratorMode $mode): AjaxRenderStrategyInterface
    {
        return match ($mode) {
            DecoratorMode::Html => new NullAjaxRenderStrategy(),
            DecoratorMode::Bootstrap => new BootstrapAjaxRenderStrategy(self::templatePath($mode)),
            DecoratorMode::Blade => new BladeAjaxRenderStrategy(self::templatePath($mode)),
            DecoratorMode::Twig => new TwigAjaxRenderStrategy(self::templatePath($mode), self::twigViewFactory()),
            DecoratorMode::Tailwind => new TailwindAjaxRenderStrategy(self::templatePath($mode)),
        };
    }

    private static function twigViewFactory(): TwigViewFactory
    {
        static $factory = null;
        if ($factory === null) {
            $cache = self::RESOURCES . '/../../var/cache/twig';
            if (!is_dir($cache)) {
                @mkdir($cache, 0777, true);
            }
            $factory = new TwigViewFactory($cache);
        }

        return $factory;
    }
}
