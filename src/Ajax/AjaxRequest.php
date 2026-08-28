<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

final class AjaxRequest
{
    public static function isXmlHttpRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * @deprecated Do not bypass the form request pipeline (element → request → validator).
     *             Prefer checkSubmit / handle on form fields to identify the action.
     */
    public static function readTargetId(): ?string
    {
        foreach (['action', 'target_id'] as $param) {
            $value = $_REQUEST[$param] ?? null;
            if (is_array($value)) {
                $value = $value[0] ?? null;
            }
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
