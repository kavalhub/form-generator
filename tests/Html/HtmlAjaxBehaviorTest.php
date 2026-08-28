<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Label;
use PHPUnit\Framework\TestCase;

final class HtmlAjaxBehaviorTest extends TestCase
{
    public function testSetAjaxOnFormKeepsStateWithoutHtmlMarker(): void
    {
        $form = (new Form('contact'))->setAjax();

        $this->assertTrue($form->isAjax());
        $this->assertFalse($form->supportsAjax());
        $this->assertStringNotContainsString('data-fg-ajax', $form->renderWithWrapper());
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

    public function testLabelDoesNotRenderAjaxMarker(): void
    {
        $label = (new Label('title'))->setLabel('Title')->setAjax();

        $this->assertFalse($label->supportsAjax());
        $this->assertStringNotContainsString('data-fg-ajax', $label->render());
    }

    public function testDisabledAjaxDoesNotRenderAttribute(): void
    {
        $input = (new InputText('email'))->setAjax(true)->setAjax(false);

        $this->assertFalse($input->isAjax());
        $this->assertStringNotContainsString('data-fg-ajax', $input->render());
    }

    public function testSetAjaxOnFormPropagatesToExistingChildren(): void
    {
        $form = new Form('contact');
        $email = new InputText('email');
        $form->addElement($email);
        $form->setAjax();

        $this->assertTrue($email->isAjax());
        $this->assertStringContainsString('data-fg-ajax="true"', $email->render());
        $this->assertDoesNotMatchRegularExpression('/<form[^>]*data-fg-ajax/', $form->renderWithWrapper());
    }

    public function testSetAjaxOnFormPropagatesToChildrenAddedLater(): void
    {
        $form = (new Form('contact'))->setAjax();
        $email = new InputText('email');
        $form->addElement($email);

        $this->assertTrue($email->isAjax());
        $this->assertStringContainsString('data-fg-ajax="true"', $email->render());
    }

    public function testSetAjaxOnFormPropagatesToNestedComposite(): void
    {
        $form = new Form('contact');
        $group = new Group('g');
        $email = new InputText('email');
        $group->addElement($email);
        $form->addElement($group);
        $form->setAjax();

        $this->assertTrue($group->isAjax());
        $this->assertFalse($group->supportsAjax());
        $this->assertDoesNotMatchRegularExpression('/<div[^>]*data-fg-ajax/', $group->render());
        $this->assertTrue($email->isAjax());
        $this->assertStringContainsString('data-fg-ajax="true"', $email->render());
    }

    public function testSetAjaxFalseOnFormClearsChildren(): void
    {
        $form = new Form('contact');
        $email = (new InputText('email'))->setAjax();
        $form->addElement($email);
        $form->setAjax(false);

        $this->assertFalse($form->isAjax());
        $this->assertFalse($email->isAjax());
    }
}
