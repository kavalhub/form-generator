<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Request;

use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use Kavalhub\FormGenerator\Request\ElementRequest;
use Kavalhub\FormGenerator\Request\PostOnlyRequest;
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

    public function testElementRequestReadsGetAndPost(): void
    {
        $_GET = ['filter' => 'from-get'];
        $_POST = [];
        $_REQUEST = ['filter' => 'from-get'];
        $request = new ElementRequest();

        $this->assertSame(['from-get'], $request->get('filter'));

        unset($_GET['filter'], $_REQUEST['filter']);
    }

    public function testElementRequestReadsPost(): void
    {
        $_POST = ['field' => 'value'];
        $_REQUEST = ['field' => 'value'];
        $request = new ElementRequest();

        $this->assertSame(['value'], $request->get('field'));

        unset($_POST['field'], $_REQUEST['field']);
    }

    public function testPostOnlyRequestIgnoresGet(): void
    {
        $_GET = ['field' => 'from-get'];
        $_POST = [];
        $request = new PostOnlyRequest();

        $this->assertNull($request->get('field'));

        unset($_GET['field']);
    }

    public function testPostOnlyRequestReadsPost(): void
    {
        $_POST = ['field' => 'posted'];
        $request = new PostOnlyRequest();

        $this->assertSame(['posted'], $request->get('field'));

        unset($_POST['field']);
    }
}
