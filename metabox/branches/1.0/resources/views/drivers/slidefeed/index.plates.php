<?php
/**
 * @var Pollen\Metabox\MetaboxTemplate $this
 */
?>
<div <?php echo $this->htmlAttrs(); ?>>
    <?php if ($suggest = $this->get('suggest')) : ?>
        <?php echo $this->field('suggest', $suggest); ?>
    <?php endif; ?>

    <ul data-control="metabox-slidefeed.items">
        <?php foreach ($this->get('items', []) as $item) : ?>
            <?php echo $this->insert('item-wrap', $item); ?>
        <?php endforeach; ?>
    </ul>

    <?php if ($addnew = $this->get('addnew')) : ?>
        <?php echo $this->field('button', $addnew); ?>
    <?php endif; ?>
</div>
