<?php
/**
 * @var Pollen\Metabox\MetaboxViewLoaderInterface $this
 * @var tiFy\Wordpress\Contracts\Query\QueryPost $item
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <?php echo $this->field('suggest', $this->get('suggest', [])); ?>

    <ul data-control="metabox-postfeed.items">
        <?php foreach ($this->get('items', []) as $item) : ?>
           <?php $this->insert('item-wrap', compact('item')); ?>
        <?php endforeach; ?>
    </ul>
</div>