<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Decorator;

use Kavalhub\FormGenerator\Decorator\AbstractDecorator;
use Kavalhub\FormGenerator\Html\Group;
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

    public function testExplicitTemplateFileHasPriorityOverGlobal(): void
    {
        $globalDir = sys_get_temp_dir() . '/fg-decorator-global-' . uniqid('', true);
        $elementFile = sys_get_temp_dir() . '/fg-decorator-element-' . uniqid('', true) . '.php';
        mkdir($globalDir);
        file_put_contents($globalDir . '/InputText.php', '<?php return "<global>";');
        file_put_contents($elementFile, '<?php return "<element>";');

        try {
            $input = (new InputText('x'))->setPath($elementFile);
            $decorator = new TestDecorator($input, $globalDir);
            $decorator->setTemplate($globalDir);

            $this->assertSame('<element>', $decorator->getHtml());
        } finally {
            @unlink($globalDir . '/InputText.php');
            @unlink($elementFile);
            @rmdir($globalDir);
        }
    }

    public function testResourceBaseResolvesRelativeTemplateFile(): void
    {
        $baseDir = sys_get_temp_dir() . '/fg-decorator-base-' . uniqid('', true);
        mkdir($baseDir . '/elements/Brand', 0777, true);
        file_put_contents($baseDir . '/elements/Brand/Group.php', '<?php return "<brand-group>";');

        try {
            $group = (new Group('g'))->setPath('elements/Brand/Group.php');
            $decorator = new TestDecorator($group, $baseDir . '/package');
            $decorator->setResourceBase($baseDir);

            $this->assertSame('<brand-group>', $decorator->getHtml());
        } finally {
            @unlink($baseDir . '/elements/Brand/Group.php');
            @rmdir($baseDir . '/elements/Brand');
            @rmdir($baseDir . '/elements');
            @rmdir($baseDir);
        }
    }

    public function testResourceBaseResolvesExtensionlessTemplateFile(): void
    {
        $baseDir = sys_get_temp_dir() . '/fg-decorator-extless-' . uniqid('', true);
        mkdir($baseDir . '/CustomElements', 0777, true);
        file_put_contents($baseDir . '/CustomElements/InputText.php', '<?php return "<custom-input>";');

        try {
            $input = (new InputText('x'))->setPath('CustomElements/InputText');
            $decorator = new TestDecorator($input, $baseDir . '/package');
            $decorator->setResourceBase($baseDir);

            $this->assertSame('<custom-input>', $decorator->getHtml());
        } finally {
            @unlink($baseDir . '/CustomElements/InputText.php');
            @rmdir($baseDir . '/CustomElements');
            @rmdir($baseDir);
        }
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
