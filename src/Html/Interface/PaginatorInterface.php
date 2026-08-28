<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Interface;

use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

interface PaginatorInterface
{
    public function setCount(int $count): self;

    public function setQueryList(array $queryList): self;

    public function getCount(): int;

    public function getLimit(): int;

    public function getPage(): int;

    public function getOffset(): int;

    public function getUrlPattern(): string;

    public function bind(ElementValidatorInterface $validator): self;

    public function getNumPages(): int;

    public function getPageUrl(int $pageNum): string;

    public function getPrevUrl(): ?string;

    public function getNextUrl(): ?string;

    /**
     * @return list<array{num: int|string, url: ?string, isCurrent: bool}>
     */
    public function getPages(int $maxPagesToShow = 10): array;

    public function getClass(): string;
}
