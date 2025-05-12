<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Domain;

use SplObjectStorage;

class FacetCollection extends SplObjectStorage
{
    public function add(Facet $object): self
    {
        $this->attach($object);

        return $this;
    }
}