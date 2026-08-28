<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

trait PropagatesHtmlAjaxToChildren
{
    use HtmlAttributes {
        setAjax as private applyAjaxToSelf;
    }

    public function setAjax(bool $enabled = true): self
    {
        $this->applyAjaxToSelf($enabled);
        $this->propagateAjaxToChildren($enabled);

        return $this;
    }

    public function supportsAjax(): bool
    {
        return false;
    }

    public function addElement(ElementInterface $element, mixed $info = null): self
    {
        parent::addElement($element, $info);

        if ($this->isAjax() && method_exists($element, 'setAjax')) {
            $element->setAjax(true);
        }

        return $this;
    }

    private function propagateAjaxToChildren(bool $enabled): void
    {
        foreach ($this->getAll() as $child) {
            if (method_exists($child, 'setAjax')) {
                $child->setAjax($enabled);
            }
        }
    }
}
