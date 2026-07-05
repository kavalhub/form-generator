<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Twig;

use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Twig\TwigAjaxRenderStrategy;
use Kavalhub\FormGenerator\Twig\TwigDecorator;
use Kavalhub\FormGenerator\Twig\TwigViewFactory;
use PHPUnit\Framework\TestCase;

final class TwigDecoratorTest extends TestCase
{
    public function testRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $html = (new TwigDecorator($input))->getHtml();

        $this->assertStringContainsString('fg-twig-input', $html);
        $this->assertStringContainsString('fg-twig-error', $html);
        $this->assertStringContainsString('&lt;bad&gt;', $html);
    }

    public function testAjaxRenderStrategyFieldValidation(): void
    {
        $strategy = new TwigAjaxRenderStrategy();
        $input = (new InputText('email'))->addError(['Required']);

        $this->assertSame('fg-twig-is-invalid', $strategy->fieldClass($input));
        $this->assertStringContainsString('fg-twig-error', $strategy->fieldErrorHtml($input));
    }

    public function testCustomTemplateOverridesDefault(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-twig-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.html.twig',
            '<custom>{{ element.render()|raw }}</custom>',
        );

        try {
            $html = (new TwigDecorator(new InputText('q')))
                ->setTemplate($templateDir)
                ->getHtml();
            $this->assertStringStartsWith('<custom>', $html);
        } finally {
            @unlink($templateDir . '/InputText.html.twig');
            @rmdir($templateDir);
        }
    }

    public function testRendersWhenCacheDirectoryIsNotWritable(): void
    {
        $cacheDir = sys_get_temp_dir() . '/fg-twig-readonly-' . uniqid('', true);
        mkdir($cacheDir, 0555);

        try {
            $html = (new TwigDecorator(new InputText('name'), new TwigViewFactory($cacheDir)))->getHtml();
            $this->assertStringContainsString('fg-twig-input', $html);
        } finally {
            @chmod($cacheDir, 0777);
            @rmdir($cacheDir);
        }
    }
}
