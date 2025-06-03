<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Form\Link;
use Kavalhub\FormGenerator\Form\Nav;

class LinkBar extends Nav
{
    private const LINK_LIST = [
        'addCategory' => [
            'class' => AddCategoryForm::class,
            'label' => 'Добавить категорию',
        ],
        'addFacet' => [
            'class' => AddFacetForm::class,
            'label' => 'Добавить фасет',
        ],
        'addProduct' => [
            'class' => AddProductForm::class,
            'label' => 'Добавить товар',
        ],
    ];

    private const NAME = 'action';

    public function __construct()
    {
        parent::__construct(self::NAME, 'nav');
        $this->addClass(['justify-content-end'])
        ->addElementBlock(Link::class, array_map(static function ($key, $data) {
            return [
                'name' => $key,
                'method' => [
                    'addData' => ['action' => $key],
                    'addClass' => ['popup-open'],
                    'setHref' => '/',
                    'setLabel' => $data['label'],
                    'addCallbackValidator' => function (Link $element) use ($data) {
                        if ($element->getValue()) {
                            $element->getParent()
                                ->setDefaultValue($element->getValue())
                                ->setValue($data['class']);
                        }

                        return true;
                    },
                ],
            ];
        }, array_keys(self::LINK_LIST), self::LINK_LIST));
    }
}
