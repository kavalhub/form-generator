<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\Engine\PhpTemplateEngine;
use PHPUnit\Framework\TestCase;

final class PhpTemplateEngineTest extends TestCase
{
    public function testIncludeReceivesDecoratorAsThis(): void
    {
        $template = sys_get_temp_dir() . '/fg-engine-this-' . uniqid('', true) . '.php';
        file_put_contents($template, '<?php return "<wrap>" . $this->element->render() . "</wrap>";');

        try {
            $context = new class(new InputText('x')) {
                public function __construct(public InputText $element)
                {
                }
            };

            $html = (new PhpTemplateEngine())->render($template, $context);

            $this->assertStringStartsWith('<wrap>', $html);
            $this->assertStringContainsString('name="x"', $html);
        } finally {
            @unlink($template);
        }
    }

    public function testIncludeCapturesEchoedOutputWhenTemplateDoesNotReturnString(): void
    {
        $template = sys_get_temp_dir() . '/fg-engine-echo-' . uniqid('', true) . '.php';
        file_put_contents($template, '<?php echo "<layout>ok</layout>";');

        try {
            $context = new class() {
            };

            $html = (new PhpTemplateEngine())->render($template, $context);

            $this->assertSame('<layout>ok</layout>', $html);
        } finally {
            @unlink($template);
        }
    }
}
