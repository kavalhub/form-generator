<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

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
            if (str_starts_with($className, 'Html') && method_exists($this, 'get' . $className)) {
                $html .= $this->{'get' . $className}();
            }
        }

        return $html;
    }

    private function getTraitName(): array
    {
        $traits = [];
        $refClass = new ReflectionClass($this);
        while ($refClass) {
            $this->collectTraits($refClass->getTraitNames(), $traits);
            $refClass = $refClass->getParentClass();
        }
        sort($traits);

        return $traits;
    }

    /**
     * @param string[] $traitNames
     * @param string[] $traits
     */
    private function collectTraits(array $traitNames, array &$traits): void
    {
        foreach ($traitNames as $traitName) {
            if (in_array($traitName, $traits, true)) {
                continue;
            }
            $traits[] = $traitName;
            $this->collectTraits((new ReflectionClass($traitName))->getTraitNames(), $traits);
        }
    }
}
