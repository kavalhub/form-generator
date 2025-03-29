<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Interface;
interface ElementInterface
{
    public function getComposite(): ?self;

    public function getId(): string;
}
