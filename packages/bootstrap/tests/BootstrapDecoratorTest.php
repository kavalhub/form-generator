<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Bootstrap;

use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator;
use Kavalhub\FormGenerator\Html\InputText;
use PHPUnit\Framework\TestCase;

final class BootstrapDecoratorTest extends TestCase
{
    public function testRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $decorator = new BootstrapDecorator($input);
        $html = $decorator->getHtml();

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
            $html = (new BootstrapDecorator($input))
                ->setTemplate($templateDir)
                ->getHtml();

            $this->assertStringStartsWith('<custom>', $html);
            $this->assertStringEndsWith('</custom>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }
}
