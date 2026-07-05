<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Html\Link;

final class RowDeleteLink
{
    public static function create(string $page, int|string $id): string
    {
        $link = (new Link('del_' . $id, '?page=' . rawurlencode($page) . '&delete=' . $id, '×'))
            ->addClass(['btn', 'btn-sm', 'btn-outline-danger', 'js-delete-row', 'text-decoration-none', 'px-2', 'py-0'])
            ->addData(['entity' => $page, 'id' => (string)$id]);

        return FormRenderer::html($link);
    }
}
