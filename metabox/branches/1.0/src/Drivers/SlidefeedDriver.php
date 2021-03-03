<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;
use tiFy\Support\Proxy\Request;

class SlidefeedDriver extends MetaboxDriver implements SlidefeedDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'slidefeed';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'addnew'  => true,
                'classes' => [],
                'fields'  => ['image', 'title', 'url', 'caption'],
                'max'     => -1,
                'suggest' => false,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Diaporama', 'tify');
    }

    /**
     * Définition d'un élément.
     *
     * @param int|string $index Indice de l'élément.
     * @param int|string|array $value Données.
     *
     * @return array
     */
    public function item($index, $value): array
    {
        $name = $this->get('name');
        $index = !is_numeric($index) ? $index : uniqid();

        return [
            'fields' => $this->get('fields', []),
            'index'  => $index,
            'name'   => $this->get('max', -1) === 1 ? "{$name}[items][]" : "{$name}[items][{$index}]",
            'value'  => $value,
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($values = $this->getValue('items')) {
            $items = [];
            array_walk(
                $values,
                function ($value, $index) use (&$items) {
                    $items[] = $this->item($index, $value);
                }
            );
            $this->set(compact('items'));
        }

        $defaultClasses = [
            'addnew'  => 'MetaboxSlidefeed-addnew ThemeButton--primary ThemeButton--normal',
            'down'    => 'MetaboxSlidefeed-itemSortDown ThemeFeed-itemSortDown',
            'item'    => 'MetaboxSlidefeed-item ThemeFeed-item',
            'items'   => 'MetaboxSlidefeed-items ThemeFeed-items',
            'order'   => 'MetaboxSlidefeed-itemOrder ThemeFeed-itemOrder',
            'remove'  => 'MetaboxSlidefeed-itemRemove ThemeFeed-itemRemove',
            'sort'    => 'MetaboxSlidefeed-itemSortHandle ThemeFeed-itemSortHandle',
            'suggest' => 'MetaboxSlidefeed-suggest',
            'up'      => 'MetaboxSlidefeed-itemSortUp ThemeFeed-itemSortUp',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        if ($suggest = $this->get('suggest', true)) {
            $defaultSuggest = [
                'ajax'    => true,
                'attrs'   => [
                    'data-control' => 'metabox-slidefeed.suggest',
                    'placeholder'  => __('Rechercher parmi les contenus du site', 'tify'),
                ],
                'classes' => [
                    'wrap' => '%s MetaboxSlidefeed-suggestWrap',
                ],
            ];
            $this->set(['suggest' => is_array($suggest) ? array_merge($defaultSuggest, $suggest) : $defaultSuggest]);
        }

        $this->set(
            [
                'addnew'             => [
                    'attrs'   => [
                        'data-control' => 'metabox-slidefeed.addnew',
                    ],
                    'content' => __('Ajouter une vignette', 'tify'),
                ],
                'attrs.class'        => sprintf($this->get('attrs.class', '%s'), 'MetaboxSlidefeed'),
                'attrs.data-control' => 'metabox-slidefeed',
                'attrs.data-options' => [
                    'ajax'    => [
                        'data'     => [
                            'fields' => $this->get('fields', []),
                            'max'    => $this->get('max', -1),
                            'name'   => $this->getName(),
                            'viewer' => $this->getViewer(),
                        ],
                        'dataType' => 'json',
                        'method'   => 'post',
                        'url'      => $this->getXhrUrl(),
                    ],
                    'classes' => $this->get('classes', []),
                    'suggest' => $this->get('suggest'),
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
        return $this->metaboxManager()->resources('/views/drivers/slidefeed');
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
                'data'    => __('Nombre maximum de vignette atteint.', 'tify'),
            ];
        } else {
            $this
                ->setName(Request::input('name', ''))
                ->setViewer(Request::input('viewer', []))
                ->set(
                    [
                        'fields' => Request::input('fields', []),
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