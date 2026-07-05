<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class FormRenderer
{
    private const TEMPLATE_PATH = __DIR__ . '/../../resources/form-templates';

    public static function html(HtmlDecoratableInterface $element): string
    {
        $decorator = new BootstrapDecorator($element);
        if (self::hasCustomTemplates()) {
            $decorator->setTemplate(self::TEMPLATE_PATH);
        }

        return $decorator->getHtml();
    }

    public static function templatePath(): string
    {
        return self::TEMPLATE_PATH;
    }

    private static function hasCustomTemplates(): bool
    {
        if (!is_dir(self::TEMPLATE_PATH)) {
            return false;
        }

        return glob(self::TEMPLATE_PATH . '/*.php') !== [];
    }
}
