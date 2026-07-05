<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\CallbackValidator;
use Kavalhub\FormGenerator\Element\Trait\Error;
use Kavalhub\FormGenerator\Element\Trait\Identifiable;
use Kavalhub\FormGenerator\Element\Trait\Observable;
use Kavalhub\FormGenerator\Element\Trait\Required;
use Kavalhub\FormGenerator\Element\Trait\Valid;
use SplObjectStorage;

class Element implements ElementInterface
{
    use CallbackValidator;
    use Error;
    use Identifiable;
    use Observable;
    use Required;
    use Valid;

    protected ?ElementInterface $parent = null;

    public function __construct()
    {
        $this->observer = new SplObjectStorage();
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getValueArray(): array
    {
        return [];
    }

    public function setParent(ElementInterface $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): ?ElementInterface
    {
        return $this->parent;
    }

    public function getComposite(): ?self
    {
        return null;
    }

    public function getAll(): SplObjectStorage
    {
        return new SplObjectStorage();
    }
}
