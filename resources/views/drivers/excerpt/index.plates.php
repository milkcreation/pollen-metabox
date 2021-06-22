<?php

/**
 * @var Pollen\Metabox\MetaboxTemplate $this
 */
echo $this->field(
    'text-remaining',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);
