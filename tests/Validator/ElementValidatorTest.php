<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Validator;

use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;
use PHPUnit\Framework\TestCase;

final class ElementValidatorTest extends TestCase
{
    public function testCheckSubmitDetectsPostedButton(): void
    {
        $submit = new InputSubmit('save');
        $validator = new ElementValidator(new ArrayRequest(['save' => 'Save']));

        $this->assertTrue($validator->checkSubmit($submit));
    }

    public function testCheckSubmitReturnsFalseWhenButtonMissing(): void
    {
        $submit = new InputSubmit('save');
        $validator = new ElementValidator(new ArrayRequest([]));

        $this->assertFalse($validator->checkSubmit($submit));
    }

    public function testHandleValidatesRequiredField(): void
    {
        $input = (new InputText('name'))->setRequired();
        $validator = new ElementValidator(new ArrayRequest([]));

        $this->assertFalse($validator->handle($input));
        $this->assertTrue($input->isError());
        $this->assertSame(['Поле должно быть заполнено'], $input->getError());
    }

    public function testHandlePassesWhenRequiredFieldHasValue(): void
    {
        $input = (new InputText('name'))->setRequired();
        $validator = new ElementValidator(new ArrayRequest(['name' => 'test']));

        $this->assertTrue($validator->handle($input));
        $this->assertFalse($input->isError());
    }

    public function testHandleDoesNotDuplicateRequiredValidatorsOnRevalidation(): void
    {
        $input = (new InputText('name'))->setRequired();
        $validator = new ElementValidator(new ArrayRequest([]));

        $validator->handle($input);
        $errorCountAfterFirst = count($input->getError());

        $validator->handle($input);
        $errorCountAfterSecond = count($input->getError());

        $this->assertSame($errorCountAfterFirst, $errorCountAfterSecond);
    }

    public function testHandleRunsCallbackValidators(): void
    {
        $input = (new InputText('email'))->addCallbackValidator(static function (InputText $element): bool {
            if (!str_contains($element->getValue(), '@')) {
                $element->addError(['Некорректный email']);

                return false;
            }

            return true;
        });
        $validator = new ElementValidator(new ArrayRequest(['email' => 'invalid']));

        $this->assertFalse($validator->handle($input));
        $this->assertSame(['Некорректный email'], $input->getError());
    }

    public function testHandleValidatesCompositeChildren(): void
    {
        $form = new Form('test');
        $form->addElement((new InputText('a'))->setRequired());
        $validator = new ElementValidator(new ArrayRequest(['test_a' => 'value']));

        $this->assertTrue($validator->handle($form));
    }

    public function testCsrfValidationFailsWithInvalidToken(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $_SESSION = [];
        session_id('test-csrf-invalid');
        session_start();

        $form = (new Form('secure'))->enableCsrf();
        $token = $_SESSION['_form_csrf_secure'] ?? '';
        $this->assertNotEmpty($token);

        $validator = new ElementValidator(new ArrayRequest(['secure_csrf' => 'wrong-token']));
        $this->assertFalse($validator->handle($form));
        $this->assertSame(['Неверный CSRF-токен'], $form->getError());

        session_write_close();
        $_SESSION = [];
    }

    public function testCsrfValidationPassesWithValidToken(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $_SESSION = [];
        session_id('test-csrf-valid');
        session_start();

        $form = (new Form('secure2'))->enableCsrf();
        $token = $_SESSION['_form_csrf_secure2'] ?? '';

        $validator = new ElementValidator(new ArrayRequest(['secure2_csrf' => $token]));
        $this->assertTrue($validator->handle($form));

        session_write_close();
        $_SESSION = [];
    }
}
