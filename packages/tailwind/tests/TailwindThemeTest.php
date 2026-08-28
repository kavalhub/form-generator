<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Tailwind;

use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Tailwind\TailwindAjaxRenderStrategy;
use Kavalhub\FormGenerator\Tailwind\TailwindTheme;
use PHPUnit\Framework\TestCase;

final class TailwindThemeTest extends TestCase
{
    private ElementRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ElementRenderer();
    }

    public function testRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $html = $this->renderer->html($input, TailwindTheme::create());

        $this->assertStringContainsString('rounded-lg', $html);
        $this->assertStringContainsString('text-red-600', $html);
        $this->assertStringContainsString('&lt;bad&gt;', $html);
    }

    public function testAjaxRenderStrategyFieldValidation(): void
    {
        $strategy = new TailwindAjaxRenderStrategy();
        $input = (new InputText('email'))->addError(['Required']);

        $this->assertStringContainsString('border-red-500', $strategy->fieldClass($input));
        $this->assertStringContainsString('text-red-600', $strategy->fieldErrorHtml($input));
    }

    public function testCustomTemplateOverridesDefault(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-tailwind-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.php',
            '<?php return \'<custom>\' . $this->element->render() . \'</custom>\';',
        );

        try {
            $html = $this->renderer->html(
                new InputText('q'),
                TailwindTheme::create(customPath: $templateDir),
            );
            $this->assertStringStartsWith('<custom>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }

    public function testCheckboxRendersLabelWithSpacing(): void
    {
        $checkbox = (new \Kavalhub\FormGenerator\Html\InputCheckbox('cat', '1'))->setLabel('Books - 3');
        $html = $this->renderer->html($checkbox, TailwindTheme::create());

        $this->assertStringContainsString('gap-2', $html);
        $this->assertStringContainsString('Books - 3', $html);
        $this->assertStringContainsString('<span', $html);
    }
}
