<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Paginator;
use Kavalhub\FormGenerator\Request\ElementRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;
use PHPUnit\Framework\TestCase;

final class PaginatorTest extends TestCase
{
    private array $savedRequest = [];

    protected function setUp(): void
    {
        $this->savedRequest = $_REQUEST;
    }

    protected function tearDown(): void
    {
        $_REQUEST = $this->savedRequest;
    }

    public function testDefaultLimitAndPage(): void
    {
        $paginator = new Paginator('p', 20, 1);

        $this->assertSame(20, $paginator->getLimit());
        $this->assertSame(1, $paginator->getPage());
        $this->assertSame(0, $paginator->getOffset());
    }

    public function testReadsLimitAndPageFromInputNumber(): void
    {
        $paginator = new Paginator('p', 20, 1);
        $paginator->getByName('limit')->setValue('10');
        $paginator->getByName('page')->setValue('3');

        $this->assertSame(10, $paginator->getLimit());
        $this->assertSame(3, $paginator->getPage());
        $this->assertSame(20, $paginator->getOffset());
    }

    public function testBindReadsPageFromRequestWithParent(): void
    {
        $form = new Form('fl');
        $paginator = new Paginator('pn', 5, 1);
        $paginator->setParent($form);

        $_REQUEST['fl_pn_page'] = '2';

        $paginator->bind(new ElementValidator(new ElementRequest()));

        $this->assertSame(2, $paginator->getPage());
    }

    public function testSetCountClampsPageToMax(): void
    {
        $paginator = new Paginator('p', 10, 1);
        $paginator->getByName('page')->setValue('99');

        $paginator->setCount(25);

        $this->assertSame(3, $paginator->getPage());
        $this->assertSame(3, $paginator->getNumPages());
    }

    public function testSetCountClampsPageToDefaultWhenBelowMin(): void
    {
        $paginator = new Paginator('p', 10, 1);
        $paginator->getByName('page')->setValue('0');

        $paginator->setCount(100);

        $this->assertSame(1, $paginator->getPage());
    }

    public function testGetUrlPatternUsesFormNameWithParent(): void
    {
        $form = new Form('fl');
        $paginator = new Paginator('pn', 5, 1);
        $paginator->setParent($form);
        $paginator->getByName('page')->setValue('2');
        $paginator->setQueryList([
            'page' => 'filter',
            'fl_gc_cat' => ['1'],
        ]);

        $pattern = $paginator->getUrlPattern();

        $this->assertStringStartsWith('?', $pattern);
        $this->assertStringContainsString('page=filter', $pattern);
        $this->assertStringContainsString('fl_pn_page=(:num)', $pattern);
        $this->assertStringNotContainsString('fl_pn_page=2', $pattern);
    }

    public function testGetPageUrlReplacesPlaceholder(): void
    {
        $form = new Form('fl');
        $paginator = new Paginator('pn', 5, 1);
        $paginator->setParent($form);
        $paginator->setQueryList(['page' => 'filter']);

        $this->assertStringContainsString('fl_pn_page=4', $paginator->getPageUrl(4));
    }

    public function testGetPagesBuildsSlidingWindow(): void
    {
        $paginator = new Paginator('p', 1, 1);
        $paginator->getByName('page')->setValue('5');
        $paginator->setCount(20);

        $pages = $paginator->getPages(7);
        $nums = array_column($pages, 'num');

        $this->assertContains(1, $nums);
        $this->assertContains('...', $nums);
        $this->assertContains(20, $nums);
        $this->assertTrue(
            (bool)array_filter($pages, static fn (array $page): bool => $page['num'] === 5 && $page['isCurrent']),
        );
    }

    public function testAjaxClass(): void
    {
        $paginator = (new Paginator())->setAjax();

        $this->assertSame('js-paginator-ajax', $paginator->getClass());
    }

    public function testRenderOutputsNavigationLinksWithoutInputs(): void
    {
        $form = new Form('fl');
        $paginator = new Paginator('pn', 5, 1);
        $paginator->setParent($form);
        $paginator->setCount(12);

        $html = $paginator->render();

        $this->assertStringContainsString('fl_pn_page=2', $html);
        $this->assertStringContainsString('<a', $html);
        $this->assertStringNotContainsString('type="hidden"', $html);
        $this->assertStringNotContainsString('type="number"', $html);
    }

    public function testRebuildNavigationCreatesChildElements(): void
    {
        $form = new Form('fl');
        $paginator = new Paginator('pn', 5, 1);
        $paginator->setParent($form);
        $paginator->setCount(12);

        $this->assertGreaterThan(0, iterator_count($paginator->getAll()));
        $this->assertNotNull($paginator->getByName('page'));
        $this->assertNotNull($paginator->getByName('limit'));
    }

    public function testSetQueryListRebuildsNavigationUrls(): void
    {
        $form = new Form('fl');
        $paginator = new Paginator('pn', 5, 1);
        $paginator->setParent($form);
        $paginator->setQueryList(['page' => 'filter']);
        $paginator->setCount(12);

        $html = $paginator->render();

        $this->assertStringContainsString('page=filter', $html);
        $this->assertStringContainsString('fl_pn_page=2', $html);
    }
}
