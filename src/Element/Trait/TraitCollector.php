<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use ReflectionClass;

trait TraitCollector
{
    public function getHtmlTrait(): string
    {

        $reflection = new ReflectionClass($this);
        $traitName = $reflection->getTraitNames();
        if ($parent = $reflection->getParentClass())
        {
            $traitName = array_merge($traitName, $parent->getTraitNames());
        }
        $html = '';
        foreach ($traitName as $name) {
            $className = explode('\\', $name);
            $className = end($className);
            if (str_contains($className, 'Html')) {
                $html .= $this->{'get' . $className}();
            }
        }

        return $html;
    }
}
