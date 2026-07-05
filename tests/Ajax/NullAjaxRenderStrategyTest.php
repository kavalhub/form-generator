<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\AjaxMode;
use Kavalhub\FormGenerator\Ajax\AjaxReplaceItem;
use Kavalhub\FormGenerator\Ajax\NullAjaxRenderStrategy;
use Kavalhub\FormGenerator\Html\InputText;
use PHPUnit\Framework\TestCase;

final class NullAjaxRenderStrategyTest extends TestCase
{
    public function testBlockHtmlUsesElementRender(): void
    {
        $input = new InputText('email');
        $strategy = new NullAjaxRenderStrategy();

        $this->assertStringContainsString('name="email"', $strategy->blockHtml($input));
    }

    public function testFieldModeOmitsBootstrapClasses(): void
    {
        $input = (new InputText('email'))->addError(['err']);
        $item = AjaxReplaceItem::fromElement($input, AjaxMode::Field, new NullAjaxRenderStrategy());
        $data = $item->toArray();

        $this->assertArrayNotHasKey('CLASS', $data);
        $this->assertSame('err', $data['ERROR']);
    }
}
