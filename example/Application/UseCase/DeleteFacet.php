<?php
declare(strict_types=1);

namespace Kavalhub\Example\Application\UseCase;

use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;

readonly class DeleteFacet
{
    public function __construct(private CatalogRepositoryInterface $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->deleteFacet($id);
    }
}
