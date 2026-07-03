<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Domain\Facet;
use Kavalhub\Example\Env\Storage;

readonly class AddFacet
{
    public function __construct(private Storage $storage)
    {
    }

    public function execute(Facet $facet): Facet
    {
        return $this->storage->addFacet($facet);
    }
}
