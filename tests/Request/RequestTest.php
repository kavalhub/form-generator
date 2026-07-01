<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Request;

use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use Kavalhub\FormGenerator\Request\ElementRequest;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testArrayRequestReturnsScalarAsArray(): void
    {
        $request = new ArrayRequest(['name' => 'Alice']);

        $this->assertSame(['Alice'], $request->get('name'));
    }

    public function testArrayRequestReturnsNullForMissingKey(): void
    {
        $request = new ArrayRequest([]);

        $this->assertNull($request->get('missing'));
    }

    public function testArrayRequestSetValueOnElement(): void
    {
        $input = new InputText('email');
        $request = new ArrayRequest(['email' => 'a@b.c']);
        $request->setValue($input);

        $this->assertSame('a@b.c', $input->getValue());
    }

    public function testElementRequestUsesInjectedSource(): void
    {
        $request = new ElementRequest(['city' => 'SPB']);

        $this->assertSame(['SPB'], $request->get('city'));
    }

    public function testElementRequestUsesPostByDefault(): void
    {
        $_POST = ['field' => 'value'];
        $request = new ElementRequest();

        $this->assertSame(['value'], $request->get('field'));

        unset($_POST['field']);
    }

    public function testElementRequestIgnoresGetParameters(): void
    {
        $_GET = ['field' => 'from-get'];
        $_POST = [];
        $request = new ElementRequest();

        $this->assertNull($request->get('field'));

        unset($_GET['field']);
    }
}
