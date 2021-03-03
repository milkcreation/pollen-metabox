<?php

/**
 * @var Pollen\Metabox\MetaboxViewLoaderInterface $this
 */
echo $this->field(
    'colorpicker',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);