<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use ReflectionClass;

trait TraitHtmlCollector
{
    public function getHtmlTrait(): string
    {
        $html = '';
        foreach ((new ReflectionClass($this))->getTraitNames() as $traitName) {
            if (str_contains($traitName, 'Html')) {
                $html .= $this->{'get' . $traitName}();
            }
        }

        return $html;
    }
}
