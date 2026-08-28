<?php
declare(strict_types=1);

namespace Kavalhub\Example\Application\UseCase;

use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;

readonly class AddCategory
{
    public function __construct(private CatalogRepositoryInterface $repository)
    {
    }

    public function execute(Category $category): Category
    {
        return $this->repository->addCategory($category);
    }
}
