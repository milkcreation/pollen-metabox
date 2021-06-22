<?php
/**
 * @var Pollen\Metabox\MetaboxTemplate $this
 */
echo $this->partial('tag', [
    'attrs'   => [
        'data-control' => 'metabox-videofeed.addnew',
    ],
    'content' => _n(
        __('Ajouter une video', 'tify'),
        __('Ajouter des vidéos', 'tify'),
        (($this->get('max', -1) === 1) ? 1 : 2),
        'tify'
    ),
    'tag'     => 'button',
]);