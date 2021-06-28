<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
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