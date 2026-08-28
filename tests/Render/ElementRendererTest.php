<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Render\Engine\PhpTemplateEngine;
use Kavalhub\FormGenerator\Render\RenderTheme;
use Kavalhub\FormGenerator\Render\Template\ClassNameInBaseTemplateSource;
use PHPUnit\Framework\TestCase;

final class ElementRendererTest extends TestCase
{
    private ElementRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new ElementRenderer();
    }

    public function testResolvesTemplateFromPackagePath(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-renderer-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents(
            $templateDir . '/InputText.php',
            '<?php return "<wrapped>" . $this->element->render() . "</wrapped>";',
        );

        try {
            $theme = new RenderTheme($templateDir, new PhpTemplateEngine());
            $html = $this->renderer->html(new InputText('x'), $theme);

            $this->assertStringStartsWith('<wrapped>', $html);
            $this->assertStringEndsWith('</wrapped>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }

    public function testFallsBackToElementRenderWithoutTemplate(): void
    {
        $theme = new RenderTheme(sys_get_temp_dir(), new PhpTemplateEngine());
        $html = $this->renderer->html(new InputText('x'), $theme);

        $this->assertStringContainsString('name="x"', $html);
    }

    public function testExplicitTemplateFileHasPriorityOverPackage(): void
    {
        $packageDir = sys_get_temp_dir() . '/fg-renderer-package-' . uniqid('', true);
        $elementFile = sys_get_temp_dir() . '/fg-renderer-element-' . uniqid('', true) . '.php';
        mkdir($packageDir);
        file_put_contents($packageDir . '/InputText.php', '<?php return "<package>";');
        file_put_contents($elementFile, '<?php return "<element>";');

        try {
            $input = (new InputText('x'))->setPath($elementFile);
            $theme = new RenderTheme($packageDir, new PhpTemplateEngine(), customPath: $packageDir);
            $html = $this->renderer->html($input, $theme);

            $this->assertSame('<element>', $html);
        } finally {
            @unlink($packageDir . '/InputText.php');
            @unlink($elementFile);
            @rmdir($packageDir);
        }
    }

    public function testResourceBaseResolvesRelativeTemplateFile(): void
    {
        $baseDir = sys_get_temp_dir() . '/fg-renderer-base-' . uniqid('', true);
        mkdir($baseDir . '/elements/Brand', 0777, true);
        file_put_contents($baseDir . '/elements/Brand/Group.php', '<?php return "<brand-group>";');

        try {
            $group = (new Group('g'))->setPath('elements/Brand/Group.php');
            $theme = new RenderTheme($baseDir . '/package', new PhpTemplateEngine(), resourceBase: $baseDir);
            $html = $this->renderer->html($group, $theme);

            $this->assertSame('<brand-group>', $html);
        } finally {
            @unlink($baseDir . '/elements/Brand/Group.php');
            @rmdir($baseDir . '/elements/Brand');
            @rmdir($baseDir . '/elements');
            @rmdir($baseDir);
        }
    }

    public function testResourceBaseResolvesExtensionlessTemplateFile(): void
    {
        $baseDir = sys_get_temp_dir() . '/fg-renderer-extless-' . uniqid('', true);
        mkdir($baseDir . '/CustomElements', 0777, true);
        file_put_contents($baseDir . '/CustomElements/InputText.php', '<?php return "<custom-input>";');

        try {
            $input = (new InputText('x'))->setPath('CustomElements/InputText');
            $theme = new RenderTheme($baseDir . '/package', new PhpTemplateEngine(), resourceBase: $baseDir);
            $html = $this->renderer->html($input, $theme);

            $this->assertSame('<custom-input>', $html);
        } finally {
            @unlink($baseDir . '/CustomElements/InputText.php');
            @rmdir($baseDir . '/CustomElements');
            @rmdir($baseDir);
        }
    }

    public function testChainedPackageBasesResolveInOrder(): void
    {
        $firstDir = sys_get_temp_dir() . '/fg-renderer-first-' . uniqid('', true);
        $secondDir = sys_get_temp_dir() . '/fg-renderer-second-' . uniqid('', true);
        mkdir($firstDir);
        mkdir($secondDir);
        file_put_contents($secondDir . '/InputText.php', '<?php return "<second>";');

        try {
            $theme = new RenderTheme(
                $firstDir,
                new PhpTemplateEngine(),
                additionalSources: [new ClassNameInBaseTemplateSource($secondDir, '.php')],
            );
            $html = $this->renderer->html(new InputText('x'), $theme);

            $this->assertSame('<second>', $html);
        } finally {
            @unlink($secondDir . '/InputText.php');
            @rmdir($firstDir);
            @rmdir($secondDir);
        }
    }

    public function testHtmlUsesPlainThemeByDefault(): void
    {
        $html = $this->renderer->html(new InputText('x'));

        $this->assertStringContainsString('name="x"', $html);
    }

    public function testSetThemeIsUsedForRender(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-renderer-theme-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents($templateDir . '/InputText.php', '<?php return "<themed>";');

        try {
            $html = (new ElementRenderer())
                ->setTheme(new RenderTheme($templateDir, new PhpTemplateEngine()))
                ->html(new InputText('x'));

            $this->assertSame('<themed>', $html);
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }
}
