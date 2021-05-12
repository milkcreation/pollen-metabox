<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Http\JsonResponse;
use Pollen\Http\ResponseInterface;
use Pollen\Metabox\MetaboxDriver;
use Pollen\WpPost\WpPostQuery;

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
                'placeholder' => 'Recherche de contenu associé ...',
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
        return $this->title ?? 'Éléments en relation';
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $items = [];

        if (is_array($this->getValue('items'))) {
            foreach ($this->getValue('items') as $id) {
                if ($item = WpPostQuery::createFromId((int)$id)) {
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
            $this->set(["classes.$k" => sprintf($this->get("classes.$k", '%s'), $v)]);
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
        return $this->metabox()->resources('/views/drivers/postfeed');
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): ResponseInterface
    {
        $index = $this->httpRequest()->input('index');
        $max = $this->httpRequest()->input('max', 0);

        if (($max > 0) && ($index >= $max)) {
            return new JsonResponse(
                [
                    'success' => false,
                    'data'    => 'Nombre maximum de fichiers partagés atteint.',
                ]
            );
        }

        if ($item = WpPostQuery::createFromId((int)$this->httpRequest()->input('post_id'))) {
            $this->setName($this->httpRequest()->input('name', ''));
            $this->setViewer($this->httpRequest()->input('viewer', []));
            $this->set(
                [
                    'max'    => $max,
                    'viewer' => $this->httpRequest()->input('viewer', []),
                ]
            );
            return new JsonResponse(
                [
                    'success' => true,
                    'data'    => $this->view('item-wrap', compact('item')),
                ]
            );
        }
        return new JsonResponse(
            [
                'success' => false,
                'data'    => 'Impossible de récupérer le contenu associé',
            ]
        );
    }
}