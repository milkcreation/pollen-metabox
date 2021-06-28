<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 * @var Pollen\WpPost\WpPostQueryInterface $item
 */
?>
<li data-control="metabox-postfeed.item">
    <?php $this->insert('item', compact('item')); ?>

    <div data-control="metabox-postfeed.item.tooltip">
        <?php $this->insert('item-info', compact('item')); ?>
    </div>
</li>