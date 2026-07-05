<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Generator;
use Kavalhub\Example\Env\Storage;
use Kavalhub\FormGenerator\Factory\ElementFactory;

class ProductList
{
    private array $where = [];
    private array $params = [];

    public function __construct(private readonly Storage $storage)
    {
    }

    public function addRawFilter(string $condition): self
    {
        $this->where[] = $condition;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addCategoryIdsFilter(array $ids): self
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($ids === []) {
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->where[] = 'tpc.category_id IN (' . $placeholders . ')';
        $this->params = array_merge($this->params, $ids);

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function addProductIdsFilter(array $ids): self
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($ids === []) {
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->where[] = 'tp.id IN (' . $placeholders . ')';
        $this->params = array_merge($this->params, $ids);

        return $this;
    }

    public function get(): Generator
    {
        return $this->storage->getProductList($this->where, $this->params);
    }

    public function getFacet(): array
    {
        $array = [];
        foreach ($this->get() as $product) {
            $array[$product['facet_name']][ElementFactory::ELEMENT] = $product[ElementFactory::ELEMENT];
            if (empty($array[$product['facet_name']]['value'][$product['facet_value']])) {
                $array[$product['facet_name']]['value'][$product['facet_value']] = 1;
                continue;
            }
            $array[$product['facet_name']]['value'][$product['facet_value']] += 1;
        }

        return $array;
    }

    public function __toArray(): array
    {
        $array = [];
        foreach ($this->get() as $product) {
            $array[$product['id']]['facet'][$product['facet_name']][] = $product['facet_value'];
            $array[$product['id']]['name'] = $product['name'];
            $array[$product['id']]['category'] = $product['category'] ?? '';
            $array[$product['id']]['price'] = $product['price'];
            $array[$product['id']]['currency'] = $product['currency'];
        }

        return $array;
    }
}
