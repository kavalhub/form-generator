<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Bootstrap;

use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Bootstrap\BootstrapTheme;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use PHPUnit\Framework\TestCase;

final class BootstrapThemeTest extends TestCase
{
    private ElementRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ElementRenderer();
    }

    public function testRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $html = $this->renderer->html($input, BootstrapTheme::create());

        $this->assertStringContainsString('form-control', $html);
        $this->assertStringContainsString('invalid-feedback', $html);
        $this->assertStringContainsString('&lt;bad&gt;', $html);
        $this->assertStringNotContainsString('<bad>', $html);
    }

    public function testAjaxRenderStrategyFieldValidation(): void
    {
        $strategy = new BootstrapAjaxRenderStrategy();
        $input = (new InputText('email'))->addError(['Required']);

        $this->assertSame('is-invalid', $strategy->fieldClass($input));
        $this->assertStringContainsString('invalid-feedback', $strategy->fieldErrorHtml($input));
    }

    public function testCustomTemplateOverridesDefault(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-bootstrap-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.php',
            '<?php return "<custom>" . $this->element->render() . "</custom>";',
        );

        try {
            $input = new InputText('q');
            $html = $this->renderer->html($input, BootstrapTheme::create(customPath: $templateDir));

            $this->assertStringStartsWith('<custom>', $html);
            $this->assertStringEndsWith('</custom>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }

    public function testRendersPaginatorWithLinks(): void
    {
        $form = new \Kavalhub\FormGenerator\Html\Form('fl');
        $paginator = new \Kavalhub\FormGenerator\Html\Paginator('pn', 5, 1);
        $paginator->setParent($form);
        $paginator->getByName('page')->setValue('2');
        $paginator->setCount(20);

        $html = $this->renderer->html($paginator, BootstrapTheme::create());

        $this->assertStringContainsString('pagination', $html);
        $this->assertStringContainsString('fl_pn_page=1', $html);
        $this->assertStringContainsString('fl_pn_page=3', $html);
        $this->assertStringNotContainsString('type="hidden"', $html);
    }
}
