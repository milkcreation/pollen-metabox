<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 */
?>
<div <?php echo $this->htmlAttrs();?>>
    <ul data-control="metabox-imagefeed.items">
        <?php foreach ($this->get('items', []) as $item) : ?>
            <?php $this->insert('item-wrap', $item); ?>
        <?php endforeach; ?>
    </ul>
    <?php $this->insert('button', $this->all()); ?>
</div>