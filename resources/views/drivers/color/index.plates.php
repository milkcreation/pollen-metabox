<?php
/**
 * @var Pollen\Metabox\MetaboxTemplate $this
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