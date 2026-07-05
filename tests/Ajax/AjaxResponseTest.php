<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\AjaxReplaceItem;
use Kavalhub\FormGenerator\Ajax\AjaxResponse;
use PHPUnit\Framework\TestCase;

final class AjaxResponseTest extends TestCase
{
    public function testJsonEncodeUsesReplaceKey(): void
    {
        $response = new AjaxResponse();
        $response->addReplace(new AjaxReplaceItem('f_email', 'is-invalid', '<div class="invalid-feedback">err</div>'));
        $response->setMessage('OK');

        $decoded = json_decode($response->jsonEncode(), true);

        $this->assertSame('f_email', $decoded['REPLACE'][0]['ID']);
        $this->assertSame('is-invalid', $decoded['REPLACE'][0]['CLASS']);
        $this->assertSame('OK', $decoded['MESSAGE']);
    }
}
