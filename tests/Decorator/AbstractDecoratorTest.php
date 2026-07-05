<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Decorator;

use Kavalhub\FormGenerator\Decorator\AbstractDecorator;
use Kavalhub\FormGenerator\Html\InputText;
use PHPUnit\Framework\TestCase;

final class AbstractDecoratorTest extends TestCase
{
    public function testResolvesTemplateFromDecoratorPath(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-decorator-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.php',
            '<?php return "<wrapped>" . $this->element->render() . "</wrapped>";',
        );

        try {
            $decorator = new TestDecorator(new InputText('x'), $templateDir);
            $html = $decorator->getHtml();

            $this->assertStringStartsWith('<wrapped>', $html);
            $this->assertStringEndsWith('</wrapped>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }

    public function testFallsBackToElementRenderWithoutTemplate(): void
    {
        $decorator = new TestDecorator(new InputText('x'), sys_get_temp_dir());
        $html = $decorator->getHtml();

        $this->assertStringContainsString('name="x"', $html);
    }
}

final class TestDecorator extends AbstractDecorator
{
    public function __construct(\Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface $element, string $path)
    {
        parent::__construct($element);
        $this->path = $path;
    }
}
