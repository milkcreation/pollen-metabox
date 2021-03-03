<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\Request;
use tiFy\Wordpress\Query\QueryPost;

class PostfeedDriver extends MetaboxDriver implements PostfeedDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'postfeed';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'classes'     => [],
                'max'         => -1,
                'suggest'     => [],
                'placeholder' => __('Recherche de contenu associé ...', 'tify'),
                'post_type'   => 'any',
                'post_status' => 'publish',
                'query_args'  => [],
                'sortable'    => true,
                'removable'   => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Éléments en relation', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $items = [];

        if (is_array($this->getValue('items'))) {
            foreach ($this->getValue('items') as $id) {
                if ($item = QueryPost::createFromId((int)$id)) {
                    $items[] = $item;
                }
            }
        }
        $this->set(['items' => $items]);

        $defaultClasses = [
            'down'    => 'MetaboxPostfeed-itemSortDown ThemeFeed-itemSortDown',
            'info'    => 'MetaboxPostfeed-itemInfo',
            'input'   => 'MetaboxPostfeed-itemInput',
            'item'    => 'MetaboxPostfeed-item ThemeFeed-item',
            'items'   => 'MetaboxPostfeed-items ThemeFeed-items',
            'order'   => 'MetaboxPostfeed-itemOrder ThemeFeed-itemOrder',
            'remove'  => 'MetaboxPostfeed-itemRemove ThemeFeed-itemRemove',
            'sort'    => 'MetaboxPostfeed-itemSortHandle ThemeFeed-itemSortHandle',
            'suggest' => 'MetaboxPostfeed-suggest',
            'tooltip' => 'MetaboxPostfeed-itemTooltip',
            'up'      => 'MetaboxPostfeed-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set(
            [
                'attrs'   => [
                    'data-control' => 'metabox.postfeed',
                ],
                'suggest' => [
                    'ajax'  => [
                        'data' => [
                            'query_args' => array_merge(
                                $this->get('query_args', []),
                                [
                                    'post_type'   => $this->get('post_type', 'any'),
                                    'post_status' => $this->get('post_status', 'publish'),
                                ]
                            ),
                        ],
                    ],
                    'alt'   => true,
                    'attrs' => [
                        'placeholder' => $this->get('placeholder'),
                    ],
                    'reset' => false,
                ],
            ]
        );

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
                    'classes'   => $this->get('classes'),
                    'name'      => $this->getName(),
                    'removable' => $this->get('removable'),
                    'sortable'  => $this->get('sortable'),
                    'suggest'   => $this->get('suggest'),
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
        return $this->metaboxManager()->resources('/views/drivers/postfeed');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $index = Request::input('index');
        $max = Request::input('max', 0);

        if (($max > 0) && ($index >= $max)) {
            return [
                'success' => false,
                'data'    => __('Nombre maximum de fichiers partagés atteint.', 'tify'),
            ];
        } elseif ($item = QueryPost::createFromId((int)Request::input('post_id'))) {
            $this->setName(Request::input('name', ''));
            $this->setViewer(Request::input('viewer', []));
            $this->set(
                [
                    'max'    => $max,
                    'viewer' => Request::input('viewer', []),
                ]
            );
            return [
                'success' => true,
                'data'    => $this->view('item-wrap', compact('item')),
            ];
        } else {
            return [
                'success' => false,
                'data'    => __('Impossible de récupérer le contenu associé', 'tify'),
            ];
        }
    }
}