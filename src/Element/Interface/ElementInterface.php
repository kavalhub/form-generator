<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Interface;

use SplObjectStorage;

interface ElementInterface
{
    public function getComposite(): ?self;

    public function getAll(): SplObjectStorage;

    public function getId(): string;
}
