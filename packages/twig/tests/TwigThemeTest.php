<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Twig;

use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Twig\TwigAjaxRenderStrategy;
use Kavalhub\FormGenerator\Twig\TwigTheme;
use Kavalhub\FormGenerator\Twig\TwigViewFactory;
use PHPUnit\Framework\TestCase;

final class TwigThemeTest extends TestCase
{
    private ElementRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ElementRenderer();
    }

    public function testRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $html = $this->renderer->html($input, TwigTheme::create());

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
            $html = $this->renderer->html(
                new InputText('q'),
                TwigTheme::create(customPath: $templateDir),
            );
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
            $html = $this->renderer->html(
                new InputText('name'),
                TwigTheme::create(viewFactory: new TwigViewFactory($cacheDir)),
            );
            $this->assertStringContainsString('fg-twig-input', $html);
        } finally {
            @chmod($cacheDir, 0777);
            @rmdir($cacheDir);
        }
    }
}
