<?php
declare(strict_types=1);

namespace Kavalhub\Example\Api;

use InvalidArgumentException;

final class FormValueMapper
{
    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    public static function toRequestData(string $form, string $action, array $values): array
    {
        return match ($form) {
            'category' => self::mapCategory($action, $values),
            'facet' => self::mapFacet($action, $values),
            'product' => self::mapProduct($values),
            'filter' => self::mapFilter($action, $values),
            default => throw new InvalidArgumentException('Unknown form: ' . $form),
        };
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private static function mapCategory(string $action, array $values): array
    {
        $data = [];
        if (isset($values['name'])) {
            $data['addCategory_name'] = $values['name'];
        }
        if (isset($values['sort'])) {
            $data['addCategory_sort'] = $values['sort'];
        }
        if ($action === 'submit') {
            $data['addCategory_submit'] = 'Добавить';
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private static function mapFacet(string $action, array $values): array
    {
        $data = [];
        if (isset($values['name'])) {
            $data['add_name'] = $values['name'];
        }
        if ($action === 'submit') {
            $data['add_addFacet'] = 'Добавить';
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private static function mapProduct(array $values): array
    {
        $data = ['addProduct_submit' => 'Добавить'];
        if (isset($values['name'])) {
            $data['addProduct_name'] = $values['name'];
        }
        if (isset($values['price'])) {
            $data['addProduct_price'] = $values['price'];
        }
        if (isset($values['category'])) {
            $data['addProduct_category'] = $values['category'];
        }
        if (isset($values['facets']) && is_array($values['facets'])) {
            foreach ($values['facets'] as $facetId => $facetValue) {
                $data['addProduct_facet_' . $facetId] = $facetValue;
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private static function mapFilter(string $action, array $values): array
    {
        $data = [];
        if (!empty($values['show_empty_categories'])) {
            $data['fl_cn_s'] = ['c'];
        }
        if (isset($values['categories']) && is_array($values['categories'])) {
            $data['fl_gc_cat'] = array_map('strval', $values['categories']);
        }
        if (isset($values['facets']) && is_array($values['facets'])) {
            foreach ($values['facets'] as $facetName => $facetValues) {
                if (!is_array($facetValues)) {
                    $facetValues = [$facetValues];
                }
                $data['fl_g' . $facetName . '_' . $facetName] = array_map('strval', $facetValues);
            }
        }
        if ($action === 'apply') {
            $data['fl_go'] = 'Показать';
        }

        return $data;
    }
}
