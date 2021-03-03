<?php

/**
 * @var Pollen\Metabox\MetaboxViewLoaderInterface $this
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