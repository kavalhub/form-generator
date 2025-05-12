<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\UseCase;

use Kavalhub\FormGenerator\Env\Storage;
use Generator;

class GetCategoryList
{
    public function __construct(private readonly Storage $storage)
    {
    }

    public function execute(): Generator
    {
        return $this->storage->getCategoryList();
    }
}