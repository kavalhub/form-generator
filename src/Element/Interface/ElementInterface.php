<?php
declare(strict_types=1);

namespace Kavalhub\Form\Element\Interface;
interface ElementInterface
{
    public function getComposite(): ?self;

    public function getId(): string;
}
