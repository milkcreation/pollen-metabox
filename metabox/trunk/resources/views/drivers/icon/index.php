<?php

/**
 * @var Pollen\Metabox\MetaboxViewLoaderInterface $this
 */
echo $this->field(
    'select-image',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);