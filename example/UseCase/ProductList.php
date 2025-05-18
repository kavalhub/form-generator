<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Env\Storage;
use Generator;
use Kavalhub\FormGenerator\Fabric\ElementFabric;
use Kavalhub\FormGenerator\Form\InputCheckbox;

class ProductList
{
    private array $filter = [];

    public function __construct(private Storage $storage)
    {
    }

    public function addFilter(array $filter): self
    {
        $this->filter = array_merge($this->filter, $filter);

        return $this;
    }

    public function get(): Generator
    {
        return $this->storage->getProductList(!empty($this->filter) ? 'WHERE ' . implode(' AND ', $this->filter) : '');
    }

    public function getFacet(): array
    {
        $array = [];
        foreach ($this->get() as $product) {
            $array[$product['facet_name']][ElementFabric::ELEMENT] = $product['view'];
            $array[$product['facet_name']][ElementFabric::NAME] = $product['view'];
            $array[$product['facet_name']]['value'][$product['facet_value']] = $product['facet_value'];
        }

        return $array;
    }

    public function __toArray(): array
    {
        $array = [];
        foreach ($this->get() as $product) {
            $array[$product['id']]['facet'][$product['facet_name']][] = $product['facet_value'];
            $array[$product['id']]['name'] = $product['name'];
            $array[$product['id']]['price'] = $product['price'];
            $array[$product['id']]['currency'] = $product['currency'];
        }

        return $array;
    }
}