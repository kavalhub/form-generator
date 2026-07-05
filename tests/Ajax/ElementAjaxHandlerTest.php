<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;
use PHPUnit\Framework\TestCase;

final class ElementAjaxHandlerTest extends TestCase
{
    public function testHandleFieldReturnsValidationState(): void
    {
        $form = new Form('f');
        $email = (new InputText('email'))->setRequired();
        $form->addElement($email);

        $request = new ArrayRequest(['f_email' => '']);
        $handler = new ElementAjaxHandler(new ElementValidator($request), new BootstrapAjaxRenderStrategy());

        $response = $handler->handleField($form, 'f_email');
        $decoded = json_decode($response->jsonEncode(), true);

        $this->assertSame('is-invalid', $decoded['REPLACE'][0]['CLASS']);
        $this->assertStringContainsString('invalid-feedback', $decoded['REPLACE'][0]['ERROR']);
    }

    public function testHandleFieldReturnsValidClassForFilledField(): void
    {
        $form = new Form('f');
        $email = (new InputText('email'))->setRequired();
        $form->addElement($email);

        $request = new ArrayRequest(['f_email' => 'user@example.com']);
        $handler = new ElementAjaxHandler(new ElementValidator($request), new BootstrapAjaxRenderStrategy());

        $response = $handler->handleField($form, 'f_email');
        $decoded = json_decode($response->jsonEncode(), true);

        $this->assertSame('is-valid', $decoded['REPLACE'][0]['CLASS']);
    }

    public function testHandleFormReturnsBlockHtml(): void
    {
        $form = new Form('contact');
        $form->addElement((new InputText('email'))->setRequired());
        $submit = (new InputSubmit('send'))->setDefaultValue('Go');
        $form->addElement($submit);

        $request = new ArrayRequest([
            'contact_email' => 'user@example.com',
            'contact_send' => 'Go',
        ]);
        $handler = new ElementAjaxHandler(new ElementValidator($request), new BootstrapAjaxRenderStrategy());

        $response = $handler->handleForm($form);
        $decoded = json_decode($response->jsonEncode(), true);

        $this->assertStringContainsString('<form', $decoded['REPLACE'][0]['HTML']);
    }
}
