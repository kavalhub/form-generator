<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Api;

use Kavalhub\FormGenerator\Api\FormApiHandler;
use Kavalhub\FormGenerator\Api\FormJsonSchemaExporter;
use Kavalhub\FormGenerator\Api\OpenApiDocumentBuilder;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputNumber;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use Kavalhub\FormGenerator\Request\JsonElementRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;
use PHPUnit\Framework\TestCase;

final class FormApiLayerTest extends TestCase
{
    public function testJsonElementRequestFromArray(): void
    {
        $request = JsonElementRequest::fromArray(['f_name' => 'test']);
        $this->assertSame(['test'], $request->get('f_name'));
    }

    public function testJsonElementRequestFromJson(): void
    {
        $request = JsonElementRequest::fromJson('{"f_name":"abc"}');
        $this->assertSame(['abc'], $request->get('f_name'));
    }

    public function testFormApiHandlerReturnsFieldErrors(): void
    {
        $form = new Form('f');
        $form->addElement((new InputText('email'))->setRequired());

        $handler = new FormApiHandler(new ElementValidator(new ArrayRequest(['f_email' => ''])));
        $response = $handler->handleField($form, 'f_email');
        $data = $response->toArray();

        $this->assertFalse($data['valid']);
        $this->assertArrayHasKey('f_email', $data['fields']);
    }

    public function testFormJsonSchemaExporterBuildsProperties(): void
    {
        $form = new Form('c');
        $form->addElement((new InputText('name'))->setRequired());
        $form->addElement((new InputNumber('sort'))->setMin(1));

        $schema = FormJsonSchemaExporter::export($form);

        $this->assertArrayHasKey('name', $schema['properties']);
        $this->assertContains('name', $schema['required']);
        $this->assertSame('number', $schema['properties']['sort']['type']);
    }

    public function testOpenApiDocumentBuilderCreatesSpec(): void
    {
        $form = new Form('demo');
        $form->addElement((new InputText('name'))->setRequired());
        $form->addElement((new InputSubmit('go'))->setDefaultValue('Go'));

        $builder = new OpenApiDocumentBuilder('Demo', '/api.php', [[
            'key' => 'demo',
            'actions' => ['submit'],
            'form' => $form,
        ]]);
        $spec = $builder->build();

        $this->assertSame('3.0.3', $spec['openapi']);
        $this->assertArrayHasKey('/api.php', $spec['paths']);
    }
}
