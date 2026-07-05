<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Util;

use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Util\ElementDataCollector;
use PHPUnit\Framework\TestCase;

final class ElementDataCollectorTest extends TestCase
{
    public function testCollectByFormNameUsesPrefixedNames(): void
    {
        $form = new Form('contact');
        $form->addElement((new InputText('email'))->setValue('a@b.c'));

        $this->assertSame(['contact_email' => 'a@b.c'], ElementDataCollector::collectByFormName($form));
    }

    public function testCollectByFormNameMergesNestedGroups(): void
    {
        $form = new Form('f');
        $group = new Group('g');
        $group->addElement((new InputText('city'))->setValue('SPB'));
        $form->addElement($group);

        $this->assertSame(['f_g_city' => 'SPB'], ElementDataCollector::collectByFormName($form));
    }

    public function testFindByFormNameLocatesNestedField(): void
    {
        $form = new Form('login');
        $form->addElement(new InputText('user'));

        $found = ElementDataCollector::findByFormName($form, 'login_user');

        $this->assertInstanceOf(InputText::class, $found);
    }

    public function testApplyErrorsAddsMessagesToElement(): void
    {
        $input = new InputText('email');
        $form = new Form('contact');
        $form->addElement($input);

        ElementDataCollector::applyErrors($form, [
            'contact_email' => ['Некорректный email'],
        ]);

        $this->assertTrue($input->isError());
        $this->assertSame(['Некорректный email'], $input->getError());
    }
}
