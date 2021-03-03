<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;
use tiFy\Support\Img;
use tiFy\Support\Proxy\Request;
use tiFy\Validation\Validator as v;

class ImagefeedDriver extends MetaboxDriver implements ImagefeedDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'imagefeed';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'classes'   => [],
                'max'       => -1,
                'library'   => true,
                'removable' => true,
                'sortable'  => true,
            ]
        );
    }


    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Images', 'tify');
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param string|int $value Identifiant de qualification du média.
     *
     * @return array
     */
    public function item($index, $value): array
    {
        $name = $this->getName();
        $index = !is_numeric($index) ? $index : uniqid();

        $src = '';
        if (is_numeric($value)) {
            if ($img = wp_get_attachment_image_src($value)) {
                $src = $img[0];
            }
        } elseif (is_string($value)) {
            if (v::url()->validate($value)) {
                $src = $value;
            } elseif (file_exists($value)) {
                $src = Img::getBase64Src($value);
            }
        }

        return [
            'index' => $index,
            'name'  => $this->get('max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value' => $value,
            'src'   => $src,
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($values = $this->getValue()) {
            $items = [];
            array_walk(
                $values,
                function ($value, $index) use (&$items) {
                    $items[] = $this->item($index, $value);
                }
            );
            $this->set('items', $items);
        }

        $defaultClasses = [
            'addnew' => 'MetaboxImagefeed-addnew ThemeButton--primary ThemeButton--normal',
            'down'   => 'MetaboxImagefeed-itemSortDown ThemeFeed-itemSortDown',
            'input'  => 'MetaboxImagefeed-itemInput',
            'item'   => 'MetaboxImagefeed-item ThemeFeed-item',
            'items'  => 'MetaboxImagefeed-items ThemeFeed-items',
            'order'  => 'MetaboxImagefeed-itemOrder ThemeFeed-itemOrder',
            'remove' => 'MetaboxImagefeed-itemRemove ThemeFeed-itemRemove',
            'sort'   => 'MetaboxImagefeed-itemSortHandle ThemeFeed-itemSortHandle',
            'up'     => 'MetaboxImagefeed-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set(
            [
                'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxImagefeed'),
                'attrs.data-control' => 'metabox-imagefeed',
            ]
        );

        if ($sortable = $this->get('sortable')) {
            $this->set(
                [
                    'sortable' => array_merge(
                        [
                            'placeholder' => 'MetaboxImagefeed-itemPlaceholder',
                            'axis'        => 'y',
                        ],
                        is_array($sortable) ? $sortable : []
                    ),
                ]
            );
        }

        $this->set(
            [
                'attrs.data-options' => [
                    'ajax'      => [
                        'data'     => [
                            'max'    => $this->get('max', -1),
                            'name'   => $this->getName(),
                            'viewer' => $this->getViewer(),
                        ],
                        'dataType' => 'json',
                        'method'   => 'post',
                        'url'      => $this->getXhrUrl(),
                    ],
                    'classes'   => $this->get('classes', []),
                    'library'   => $this->get('library'),
                    'removable' => $this->get('removable'),
                    'sortable'  => $this->get('sortable'),
                ],
            ]
        );
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/imagefeed');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);
        $value = Request::input('value', '');

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum d\'images atteint.', 'tify'),
            ];
        } else {
            $this->setName(Request::input('name', ''));
            $this->setViewer(Request::input('viewer', []));
            $this->set(
                [
                    'max'    => $max,
                ]
            );
            return [
                'success' => true,
                'data'    => $this->view('item-wrap', $this->item($index, $value)),
            ];
        }
    }
}