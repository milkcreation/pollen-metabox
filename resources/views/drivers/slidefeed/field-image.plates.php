<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 */
echo $this->field('media-image', [
    'height' => 150,
    'infos'  => false,
    'name'   => $this->get('name') . '[image]',
    'value'  => $this->get('value.image'),
    'size'   => 'thumbnail',
    'width'  => 150
]);