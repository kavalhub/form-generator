<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Blade;

use Kavalhub\FormGenerator\Blade\BladeAjaxRenderStrategy;
use Kavalhub\FormGenerator\Blade\BladeTheme;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use PHPUnit\Framework\TestCase;

final class BladeThemeTest extends TestCase
{
    private ElementRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ElementRenderer();
    }

    public function testRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $html = $this->renderer->html($input, BladeTheme::create());

        $this->assertStringContainsString('fg-blade-input', $html);
        $this->assertStringContainsString('fg-blade-error', $html);
        $this->assertStringContainsString('&lt;bad&gt;', $html);
        $this->assertStringNotContainsString('<bad>', $html);
    }

    public function testAjaxRenderStrategyFieldValidation(): void
    {
        $strategy = new BladeAjaxRenderStrategy();
        $input = (new InputText('email'))->addError(['Required']);

        $this->assertSame('fg-blade-is-invalid', $strategy->fieldClass($input));
        $this->assertStringContainsString('fg-blade-error', $strategy->fieldErrorHtml($input));
    }

    public function testCustomTemplateOverridesDefault(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-blade-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.php',
            '<custom>{!! $element->render() !!}</custom>',
        );

        try {
            $input = new InputText('q');
            $html = $this->renderer->html($input, BladeTheme::create(customPath: $templateDir));

            $this->assertStringStartsWith('<custom>', $html);
            $this->assertStringEndsWith('</custom>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }

    public function testCustomTemplateAppliesToChildrenInForm(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-blade-form-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.php',
            '<custom-input>{!! $element->render() !!}</custom-input>',
        );

        try {
            $form = new \Kavalhub\FormGenerator\Html\Form('f');
            $form->addElement(new InputText('name'));

            $html = $this->renderer->html($form, BladeTheme::create(customPath: $templateDir));

            $this->assertStringContainsString('<custom-input>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }
}
