<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;
use PHPUnit\Framework\TestCase;

final class HtmlAjaxBehaviorTest extends TestCase
{
    public function testSetAjaxAddsDataAttributeOnForm(): void
    {
        $form = (new Form('contact'))->setAjax();

        $this->assertTrue($form->isAjax());
        $this->assertStringContainsString('<form data-fg-ajax="true"', $form->renderWithWrapper());
    }

    public function testSetUrlStateAddsDataAttributeOnForm(): void
    {
        $form = (new Form('contact'))->setUrlState('pushState');

        $this->assertSame('pushState', $form->getUrlState());
        $this->assertStringContainsString('data-fg-url-state="pushState"', $form->renderWithWrapper());
    }

    public function testSetUrlStateFalseRemovesAttribute(): void
    {
        $form = (new Form('contact'))->setUrlState('replaceState')->setUrlState(false);

        $this->assertNull($form->getUrlState());
        $this->assertStringNotContainsString('data-fg-url-state', $form->renderWithWrapper());
    }

    public function testSetAjaxOnFieldAddsDataAttribute(): void
    {
        $input = (new InputText('email'))->setAjax();
        $html = $input->render();

        $this->assertTrue($input->isAjax());
        $this->assertStringContainsString('<input data-fg-ajax="true"', $html);
    }

    public function testDisabledAjaxDoesNotRenderAttribute(): void
    {
        $input = (new InputText('email'))->setAjax(true)->setAjax(false);

        $this->assertFalse($input->isAjax());
        $this->assertStringNotContainsString('data-fg-ajax', $input->render());
    }
}
