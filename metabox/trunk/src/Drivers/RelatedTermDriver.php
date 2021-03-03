<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;

/**
 * @todo
 */
class RelatedTermDriver extends MetaboxDriver implements RelatedTermDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'related_term';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'multiple' => true,
                'taxonomy' => 'category',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Catégories associées', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $terms = get_terms(
            [
                'taxonomy'   => $this->get('taxonomy'),
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key'     => '_order',
                        'value'   => 0,
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    ],
                    [
                        'key'     => '_order',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
                'orderby'    => 'meta_value_num',
                'order'      => 'ASC',
                'get'        => 'all',
            ]
        );

        if (is_wp_error($terms)) {
            return '';
        }

        $this->set('taxonomy', (array)$this->get('taxonomy'));
        $checked = wp_get_object_terms($post->ID, $this->get('taxonomy'), array_merge($args, ['fields' => 'ids']));

        $items = [];
        if ($this->get('multiple', true)) {
            /** @var \WP_Term $t */
            foreach ($terms as $t) {
                $items[] = [
                    'label'    => [
                        'content' => $t->name,
                    ],
                    'checkbox' => [
                        'name'    => "tax_input[{$t->taxonomy}][]",
                        'value'   => is_taxonomy_hierarchical($t->taxonomy) ? $t->term_id : $t->name,
                        'checked' => in_array($t->term_id, $checked),
                    ],
                ];
            }
        } else {
            /** @var \WP_Term $t */
            foreach ($terms as $t) {
                $items[] = [
                    'label' => [
                        'content' => $t->name,
                    ],
                    'radio' => [
                        'name'    => "tax_input[{$t->taxonomy}][]",
                        'value'   => is_taxonomy_hierarchical($t->taxonomy) ? $t->term_id : $t->name,
                        'checked' => in_array($t->term_id, $checked),
                    ],
                ];
            }
        }

        $this->set('items', $items);

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/related-term');
    }
}