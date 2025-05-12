<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Env\Storage;
use Generator;

readonly class GetCategoryList
{
    public function __construct(private Storage $storage)
    {
    }

    public function execute(): Generator
    {
        return $this->storage->getCategoryList();
    }
}