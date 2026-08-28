<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\Template\ClassNameInBaseTemplateSource;
use Kavalhub\FormGenerator\Render\Template\ElementPathTemplateSource;
use Kavalhub\FormGenerator\Render\Template\TemplateResolutionContext;
use Kavalhub\FormGenerator\Render\Template\TemplateResolver;
use PHPUnit\Framework\TestCase;

final class TemplateResolverTest extends TestCase
{
    public function testResolvesClassNameInPackageBase(): void
    {
        $templateDir = sys_get_temp_dir() . '/fg-resolver-test-' . uniqid('', true);
        mkdir($templateDir);
        file_put_contents($templateDir . '/InputText.php', '<?php return "ok";');

        try {
            $resolver = new TemplateResolver([
                new ClassNameInBaseTemplateSource($templateDir, '.php'),
            ]);
            $context = TemplateResolutionContext::forElement(new InputText('x'), '.php');

            $this->assertSame($templateDir . '/InputText.php', $resolver->resolve(new InputText('x'), $context));
        } finally {
            @unlink($templateDir . '/InputText.php');
            @rmdir($templateDir);
        }
    }

    public function testElementPathHasPriorityOverPackageBase(): void
    {
        $packageDir = sys_get_temp_dir() . '/fg-resolver-package-' . uniqid('', true);
        $elementFile = sys_get_temp_dir() . '/fg-resolver-element-' . uniqid('', true) . '.php';
        mkdir($packageDir);
        file_put_contents($packageDir . '/InputText.php', '<?php return "<package>";');
        file_put_contents($elementFile, '<?php return "<element>";');

        try {
            $input = (new InputText('x'))->setPath($elementFile);
            $resolver = TemplateResolver::forDecorator('.php', $packageDir, $packageDir . '/fallback');
            $context = TemplateResolutionContext::forElement($input, '.php');

            $this->assertSame($elementFile, $resolver->resolve($input, $context));
        } finally {
            @unlink($packageDir . '/InputText.php');
            @unlink($elementFile);
            @rmdir($packageDir);
        }
    }

    public function testChainedPackageBasesResolveInOrder(): void
    {
        $firstDir = sys_get_temp_dir() . '/fg-resolver-first-' . uniqid('', true);
        $secondDir = sys_get_temp_dir() . '/fg-resolver-second-' . uniqid('', true);
        mkdir($firstDir);
        mkdir($secondDir);
        file_put_contents($secondDir . '/InputText.php', '<?php return "<second>";');

        try {
            $resolver = new TemplateResolver([
                new ClassNameInBaseTemplateSource($firstDir, '.php'),
                new ClassNameInBaseTemplateSource($secondDir, '.php'),
            ]);
            $input = new InputText('x');
            $context = TemplateResolutionContext::forElement($input, '.php');

            $this->assertSame($secondDir . '/InputText.php', $resolver->resolve($input, $context));
        } finally {
            @unlink($secondDir . '/InputText.php');
            @rmdir($firstDir);
            @rmdir($secondDir);
        }
    }

    public function testExtensionlessElementPath(): void
    {
        $baseDir = sys_get_temp_dir() . '/fg-resolver-extless-' . uniqid('', true);
        mkdir($baseDir . '/CustomElements', 0777, true);
        file_put_contents($baseDir . '/CustomElements/InputText.php', '<?php return "ok";');

        try {
            $input = (new InputText('x'))->setPath($baseDir . '/CustomElements/InputText');
            $resolver = new TemplateResolver([new ElementPathTemplateSource('.php')]);
            $context = TemplateResolutionContext::forElement($input, '.php');

            $this->assertSame(
                $baseDir . '/CustomElements/InputText.php',
                $resolver->resolve($input, $context),
            );
        } finally {
            @unlink($baseDir . '/CustomElements/InputText.php');
            @rmdir($baseDir . '/CustomElements');
            @rmdir($baseDir);
        }
    }

    public function testResourceBaseRelativePath(): void
    {
        $baseDir = sys_get_temp_dir() . '/fg-resolver-base-' . uniqid('', true);
        mkdir($baseDir . '/elements/Brand', 0777, true);
        file_put_contents($baseDir . '/elements/Brand/Group.php', '<?php return "ok";');

        try {
            $group = (new Group('g'))->setPath($baseDir . '/elements/Brand/Group.php');
            $resolver = new TemplateResolver([new ElementPathTemplateSource('.php')]);
            $context = TemplateResolutionContext::forElement($group, '.php');

            $this->assertSame(
                $baseDir . '/elements/Brand/Group.php',
                $resolver->resolve($group, $context),
            );
        } finally {
            @unlink($baseDir . '/elements/Brand/Group.php');
            @rmdir($baseDir . '/elements/Brand');
            @rmdir($baseDir . '/elements');
            @rmdir($baseDir);
        }
    }
}
