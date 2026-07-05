<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Laravel;

use Illuminate\Http\Request;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Laravel\LaravelRequestAdapter;
use PHPUnit\Framework\TestCase;

final class LaravelRequestAdapterTest extends TestCase
{
    public function testReadsFromIlluminateRequest(): void
    {
        $request = Request::create('/', 'GET', ['filter_go' => '1']);
        $adapter = new LaravelRequestAdapter($request);

        $this->assertSame(['1'], $adapter->get('filter_go'));
    }

    public function testSetValueOnElement(): void
    {
        $request = Request::create('/', 'POST', ['login_user' => 'admin']);
        $adapter = new LaravelRequestAdapter($request);

        $form = new \Kavalhub\FormGenerator\Html\Form('login');
        $input = new InputText('user');
        $form->addElement($input);
        $adapter->setValue($input);

        $this->assertSame('admin', $input->getValue());
    }
}
