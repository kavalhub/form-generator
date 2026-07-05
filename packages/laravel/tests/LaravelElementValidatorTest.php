<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Laravel;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Laravel\LaravelElementValidator;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use PHPUnit\Framework\TestCase;

final class LaravelElementValidatorTest extends TestCase
{
    private function createValidatorFactory(): Factory
    {
        $translator = new Translator(new ArrayLoader(), 'en');

        return new Factory($translator);
    }

    public function testLaravelRulesValidateAfterCoreBinding(): void
    {
        $form = new Form('contact');
        $email = (new InputText('email'))->setRequired();
        $submit = (new InputSubmit('send'))->setDefaultValue('Go');
        $form->addElement($email)->addElement($submit);

        $request = new ArrayRequest([
            'contact_email' => 'not-an-email',
            'contact_send' => 'Go',
        ]);

        $validator = new LaravelElementValidator($request, $this->createValidatorFactory());
        $validator->setRules([
            'contact_email' => 'email',
        ]);

        $this->assertTrue($validator->checkSubmit($submit));
        $this->assertFalse($validator->handle($form));
        $this->assertTrue($email->isError());
    }

    public function testPassesWithValidLaravelRules(): void
    {
        $form = new Form('contact');
        $email = (new InputText('email'))->setRequired();
        $submit = (new InputSubmit('send'))->setDefaultValue('Go');
        $form->addElement($email)->addElement($submit);

        $request = new ArrayRequest([
            'contact_email' => 'user@example.com',
            'contact_send' => 'Go',
        ]);

        $validator = new LaravelElementValidator($request, $this->createValidatorFactory());
        $validator->setRules([
            'contact_email' => 'required|email',
        ]);

        $this->assertTrue($validator->handle($form));
        $this->assertTrue($validator->isValid());
    }

    public function testWorksWithoutLaravelRulesAsCoreOnly(): void
    {
        $form = new Form('contact');
        $form->addElement((new InputText('email'))->setRequired());

        $validator = new LaravelElementValidator(new ArrayRequest([]), $this->createValidatorFactory());

        $this->assertFalse($validator->handle($form));
    }
}
