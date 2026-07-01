<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Util;

use Kavalhub\FormGenerator\Util\HtmlEscaper;
use PHPUnit\Framework\TestCase;

final class HtmlEscaperTest extends TestCase
{
    public function testEscapeSpecialCharacters(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;',
            HtmlEscaper::escape('<script>alert("xss")</script>')
        );
    }

    public function testEscapeListJoinsWithSeparator(): void
    {
        $this->assertSame(
            'a&lt;br&gt;b',
            HtmlEscaper::escapeList(['a<br>', 'b'])
        );
    }

    public function testEscapeAttribute(): void
    {
        $this->assertSame('foo &amp; bar', HtmlEscaper::escapeAttribute('foo & bar'));
    }
}
