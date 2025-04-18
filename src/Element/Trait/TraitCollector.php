<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use ReflectionClass;

trait TraitCollector
{
    public function getHtmlTrait(array $without = []): string
    {
        $html = '';
        foreach ($this->getTraitName() as $name) {
            $className = explode('\\', $name);
            $className = end($className);
            if (in_array($className, $without, true)) {
                continue;
            }
            if (str_contains($className, 'Html')) {
                $html .= $this->{'get' . $className}();
            }
        }

        return $html;
    }

    private function getTraitName(): array
    {
        $refClass = new ReflectionClass($this);
        $traitName = $refClass->getTraitNames();
        $parentTraitName = [];
        while ($parent = $refClass->getParentClass()) {
            $parentTraitName[] = $parent->getTraitNames();
            $refClass = $parent;
        }
        $traitName = array_unique(array_merge($traitName, ...$parentTraitName));
        sort($traitName);

        return $traitName;
    }
}
