<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Element;
use Kavalhub\FormGenerator\Html\Interface\PaginatorInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;
use RuntimeException;

class Paginator extends Group implements PaginatorInterface
{
    private const NUM_PLACEHOLDER = '(:num)';

    private int $count = 0;

    /** @var array<string, scalar|array<int|string, scalar|null>> */
    private array $queryList = [];

    private InputNumber $limit;

    private InputNumber $page;

    public function __construct(
        string $name = 'p',
        private readonly int $defaultLimit = 20,
        private readonly int $defaultPage = 1,
    ) {
        parent::__construct($name);

        $this->limit = (new InputNumber('limit'))
            ->setRequired(false)
            ->setDefaultValue((string)$this->defaultLimit);

        $this->page = (new InputNumber('page'))
            ->setMin(1)
            ->setRequired(false)
            ->setDefaultValue((string)$this->defaultPage)
            ->addCallbackValidator($this->validatePageRange(...));

        $this->limit->setParent($this);
        $this->page->setParent($this);
    }

    public function getByName(string $name, bool $extract = false): Element
    {
        if ($name === 'limit') {
            return $this->limit;
        }
        if ($name === 'page') {
            return $this->page;
        }

        return parent::getByName($name, $extract);
    }

    public function bind(ElementValidatorInterface $validator): self
    {
        if ($this->getParent() === null) {
            throw new RuntimeException('Paginator parent must be set before bind().');
        }

        $this->limit->setParent($this);
        $this->page->setParent($this);
        $validator->handle($this->limit);
        $validator->handle($this->page);

        return $this;
    }

    public function getClass(): string
    {
        $class = [];
        if ($this->isAjax()) {
            $class[] = 'js-paginator-ajax';
        }

        return implode(' ', $class);
    }

    public function setCount(int $count): self
    {
        $this->count = $count;
        $maxPage = max(1, (int)ceil($count / $this->getLimit()));

        $this->page->setMax((float)$maxPage);

        if (!$this->isPageInRange($maxPage)) {
            $pageValue = (int)$this->page->getValue();
            if ($pageValue < $this->defaultPage) {
                $this->page->setValue((string)$this->defaultPage);
            } else {
                $this->page->setValue((string)$maxPage);
            }
        }

        $this->rebuildNavigation();

        return $this;
    }

    public function setQueryList(array $queryList): self
    {
        $this->queryList = $queryList;
        $this->rebuildNavigation();

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLimit(): int
    {
        $limit = (int)$this->limit->getValue();

        return $limit > 0 ? $limit : $this->defaultLimit;
    }

    public function getPage(): int
    {
        $page = (int)$this->page->getValue();

        return $page > 0 ? $page : $this->defaultPage;
    }

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }

    public function getUrlPattern(): string
    {
        $pageKey = $this->page->getFormName();
        $patternUrl = $pageKey . '=' . self::NUM_PLACEHOLDER;
        $queryList = $this->queryList;
        unset($queryList[$pageKey]);

        if ($queryList !== []) {
            $patternUrl = http_build_query($queryList) . '&' . $patternUrl;
        }

        return '?' . $patternUrl;
    }

    public function getNumPages(): int
    {
        $limit = $this->getLimit();

        return $limit === 0 ? 0 : (int)ceil($this->count / $limit);
    }

    public function getPageUrl(int $pageNum): string
    {
        return str_replace(self::NUM_PLACEHOLDER, (string)$pageNum, $this->getUrlPattern());
    }

    public function getPrevUrl(): ?string
    {
        if ($this->getPage() <= 1) {
            return null;
        }

        return $this->getPageUrl($this->getPage() - 1);
    }

    public function getNextUrl(): ?string
    {
        if ($this->getPage() >= $this->getNumPages()) {
            return null;
        }

        return $this->getPageUrl($this->getPage() + 1);
    }

    /**
     * @return list<array{num: int|string, url: ?string, isCurrent: bool}>
     */
    public function getPages(int $maxPagesToShow = 10): array
    {
        $numPages = $this->getNumPages();
        if ($numPages <= 1) {
            return [];
        }

        $currentPage = $this->getPage();

        if ($numPages <= $maxPagesToShow) {
            $pages = [];
            for ($i = 1; $i <= $numPages; $i++) {
                $pages[] = $this->createPage($i, $i === $currentPage);
            }

            return $pages;
        }

        $numAdjacents = (int)floor(($maxPagesToShow - 3) / 2);

        if ($currentPage + $numAdjacents > $numPages) {
            $slidingStart = $numPages - $maxPagesToShow + 2;
        } else {
            $slidingStart = $currentPage - $numAdjacents;
        }
        if ($slidingStart < 2) {
            $slidingStart = 2;
        }

        $slidingEnd = $slidingStart + $maxPagesToShow - 3;
        if ($slidingEnd >= $numPages) {
            $slidingEnd = $numPages - 1;
        }

        $pages = [$this->createPage(1, $currentPage === 1)];
        if ($slidingStart > 2) {
            $pages[] = $this->createPageEllipsis();
        }
        for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
            $pages[] = $this->createPage($i, $i === $currentPage);
        }
        if ($slidingEnd < $numPages - 1) {
            $pages[] = $this->createPageEllipsis();
        }
        $pages[] = $this->createPage($numPages, $currentPage === $numPages);

        return $pages;
    }

    private function rebuildNavigation(): void
    {
        $this->clearNavigationChildren();

        if ($this->getNumPages() <= 1) {
            return;
        }

        $ellipsisIndex = 0;

        if ($prev = $this->getPrevUrl()) {
            $this->addNavigationLink('prev', $prev, '«', ['page-link', 'fg-paginator-prev']);
        }

        foreach ($this->getPages() as $page) {
            if ($page['num'] === '...') {
                $this->addNavigationLabel(
                    'ellipsis_' . $ellipsisIndex++,
                    '...',
                    ['fg-paginator-ellipsis'],
                );
                continue;
            }
            if ($page['isCurrent']) {
                $this->addNavigationLabel(
                    'page_' . $page['num'],
                    (string)$page['num'],
                    ['page-link', 'fg-paginator-current'],
                );
                continue;
            }
            $this->addNavigationLink(
                'page_' . $page['num'],
                (string)$page['url'],
                (string)$page['num'],
                ['page-link'],
            );
        }

        if ($next = $this->getNextUrl()) {
            $this->addNavigationLink('next', $next, '»', ['page-link', 'fg-paginator-next']);
        }
    }

    private function clearNavigationChildren(): void
    {
        foreach (iterator_to_array($this->elementStorage) as $element) {
            $this->removeElement($element);
        }
    }

    /**
     * @param list<string> $classes
     */
    private function addNavigationLink(string $name, string $href, string $label, array $classes): void
    {
        $link = (new Link($name, $href, $label))->addClass($classes);
        $this->addElement($link);
    }

    /**
     * @param list<string> $classes
     */
    private function addNavigationLabel(string $name, string $label, array $classes): void
    {
        $element = (new Label($name))->setLabel($label)->addClass($classes);
        $this->addElement($element);
    }

    private function validatePageRange(InputNumber $page): bool
    {
        if ($page->getValue() === '') {
            return true;
        }
        $value = (int)$page->getValue();
        $min = (int)$page->getMin();
        $max = (int)$page->getMax();
        if ($max > 0 && ($value < $min || $value > $max)) {
            return false;
        }

        return true;
    }

    private function isPageInRange(int $maxPage): bool
    {
        if ($this->page->getValue() === '') {
            return true;
        }
        $value = (int)$this->page->getValue();

        return $value >= $this->defaultPage && $value <= $maxPage;
    }

    /**
     * @return array{num: int, url: string, isCurrent: bool}
     */
    private function createPage(int $pageNum, bool $isCurrent): array
    {
        return [
            'num' => $pageNum,
            'url' => $this->getPageUrl($pageNum),
            'isCurrent' => $isCurrent,
        ];
    }

    /**
     * @return array{num: string, url: null, isCurrent: false}
     */
    private function createPageEllipsis(): array
    {
        return [
            'num' => '...',
            'url' => null,
            'isCurrent' => false,
        ];
    }
}
