<?php

/**
 * @var Pollen\Metabox\MetaboxTemplate $this
 */
echo $this->field(
    'media-image',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);