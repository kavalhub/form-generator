<?php
declare(strict_types=1);

namespace Kavalhub\Example\Application\UseCase;

use Kavalhub\Example\Domain\Facet;
use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;

readonly class AddFacet
{
    public function __construct(private CatalogRepositoryInterface $repository)
    {
    }

    public function execute(Facet $facet): Facet
    {
        return $this->repository->addFacet($facet);
    }
}
