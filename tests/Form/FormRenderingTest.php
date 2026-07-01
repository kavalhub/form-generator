<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Form;

use Kavalhub\FormGenerator\Decorator\Bootstrap\BootstrapDecorator;
use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\Group;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Form\Label;
use Kavalhub\FormGenerator\Form\Textarea;
use Kavalhub\FormGenerator\Table\Td;
use Kavalhub\FormGenerator\Table\Tr;
use PHPUnit\Framework\TestCase;

final class FormRenderingTest extends TestCase
{
    public function testFormRendersWrapperTag(): void
    {
        $form = new Form('login');
        $form->addElement(new InputText('user'));

        $html = $form->getHtml();
        $this->assertStringStartsWith('<form', $html);
        $this->assertStringEndsWith('</form>', $html);
        $this->assertStringContainsString('name="login_user"', $html);
    }

    public function testInputTextEscapesValueAttribute(): void
    {
        $input = (new InputText('q'))->setValue('"><script>');
        $html = $input->getHtml();

        $this->assertStringContainsString('value="&quot;&gt;&lt;script&gt;"', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function testTextareaEscapesBodyContent(): void
    {
        $textarea = (new Textarea('desc'))->setValue('<b>bold</b>');
        $html = $textarea->getHtml();

        $this->assertStringContainsString('&lt;b&gt;bold&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>bold</b>', $html);
    }

    public function testLabelEscapesByDefault(): void
    {
        $label = (new Label('l'))->setLabel('<script>x</script>');
        $html = $label->getHtml();

        $this->assertStringContainsString('&lt;script&gt;x&lt;/script&gt;', $html);
    }

    public function testLabelAllowHtmlWhenEnabled(): void
    {
        $label = (new Label('l'))->setLabel('<h3>Title</h3>')->setAllowHtml();
        $html = $label->getHtml();

        $this->assertStringContainsString('<h3>Title</h3>', $html);
    }

    public function testTdEscapesCellContent(): void
    {
        $td = new Td('<img onerror=alert(1)>');
        $html = $td->getHtml();

        $this->assertStringContainsString('&lt;img onerror=alert(1)&gt;', $html);
    }

    public function testGroupRendersChildrenWithNamePrefix(): void
    {
        $group = new Group('address');
        $group->addElement(new InputText('street'));
        $html = $group->getHtml();

        $this->assertStringContainsString('name="address_street"', $html);
    }

    public function testBootstrapDecoratorRendersInputWithError(): void
    {
        $input = (new InputText('name'))->addError(['<bad>']);
        $decorator = new BootstrapDecorator($input);
        $html = $decorator->getHtml();

        $this->assertStringContainsString('form-control', $html);
        $this->assertStringContainsString('invalid-feedback', $html);
        $this->assertStringContainsString('&lt;bad&gt;', $html);
        $this->assertStringNotContainsString('<bad>', $html);
    }

    public function testFormEnableCsrfAddsHiddenField(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $_SESSION = [];
        session_id('test-csrf-render');
        session_start();

        $form = (new Form('f'))->enableCsrf();
        $html = $form->getHtml();

        $this->assertStringContainsString('type="hidden"', $html);
        $this->assertStringContainsString('name="f_csrf"', $html);

        session_write_close();
        $_SESSION = [];
    }

    public function testTrRendersRow(): void
    {
        $tr = (new Tr())->addElement(new Td('1'))->addElement(new Td('2'));
        $html = $tr->getHtml();

        $this->assertStringContainsString('<tr>', $html);
        $this->assertStringContainsString('</tr>', $html);
        $this->assertSame(2, substr_count($html, '<td'));
    }
}
