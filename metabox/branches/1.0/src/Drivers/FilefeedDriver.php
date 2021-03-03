<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\Request;

class FilefeedDriver extends MetaboxDriver implements FilefeedDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'filefeed';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'classes'   => [],
                'filetype'  => '', // video || application/pdf || video/flv, video/mp4,
                'max'       => -1,
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
        return $this->title ?? __('Partage de fichier', 'tify');
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param int|null $value Identifiant de qualification du média.
     *
     * @return array
     */
    public function item($index, ?int $value = null): array
    {
        $name = $this->getName();
        $index = !is_numeric($index) ? $index : uniqid();

        return [
            'name'  => $this->get('max', -1) === 1 ? "{$name}[]" : "{$name}[{$index}]",
            'value' => $value,
            'index' => $index,
            'icon'  => wp_get_attachment_image($value, [48, 64], true),
            'title' => get_the_title($value),
            'mime'  => get_post_mime_type($value),
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultClasses = [
            'addnew' => 'MetaboxFilefeed-addnew ThemeButton--primary ThemeButton--normal',
            'down'   => 'MetaboxFilefeed-itemSortDown ThemeFeed-itemSortDown',
            'input'  => 'MetaboxFilefeed-itemInput',
            'item'   => 'MetaboxFilefeed-item ThemeFeed-item',
            'items'  => 'MetaboxFilefeed-items ThemeFeed-items',
            'order'  => 'MetaboxFilefeed-itemOrder ThemeFeed-itemOrder',
            'remove' => 'MetaboxFilefeed-itemRemove ThemeFeed-itemRemove',
            'sort'   => 'MetaboxFilefeed-itemSortHandle ThemeFeed-itemSortHandle',
            'up'     => 'MetaboxFilefeed-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set(
            [
                'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxFilefeed'),
                'attrs.data-control' => 'metabox-filefeed',
            ]
        );

        if ($sortable = $this->get('sortable')) {
            $this->set(
                [
                    'sortable' => array_merge(
                        [
                            'placeholder' => 'MetaboxFilefeed-itemPlaceholder',
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
                    'ajax'      => array_merge(
                        [
                            'data'     => [
                                'max'    => $this->get('max', -1),
                                'name'   => $this->getName(),
                                'viewer' => $this->getViewer(),
                            ],
                            'dataType' => 'json',
                            'method'   => 'post',
                            'url'      => $this->getXhrUrl(),
                        ]
                    ),
                    'classes'   => $this->get('classes', []),
                    'media'     => [
                        'multiple' => ($this->get('max', -1) === 1 ? false : true),
                        'library'  => [
                            'type' => $this->get('filetype'),
                        ],
                    ],
                    'removable' => $this->get('removable'),
                    'sortable'  => $this->get('sortable'),
                ],
            ]
        );

        if ($values = $this->getValue()) {
            $items = [];
            array_walk(
                $values,
                function ($value, $index) use (&$items) {
                    $items[] = $this->item($index, (int)$value);
                }
            );
            $this->set('items', $items);
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/filefeed');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);
        $value = (int)Request::input('value');

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de fichiers partagés atteint.', 'tify'),
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