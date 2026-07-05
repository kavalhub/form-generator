<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Util;

final class HtmlEscaper
{
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param string[] $values
     */
    public static function escapeList(array $values, string $separator = '<br>'): string
    {
        return implode($separator, array_map([self::class, 'escape'], $values));
    }

    public static function escapeAttribute(string $value): string
    {
        return self::escape($value);
    }
}
