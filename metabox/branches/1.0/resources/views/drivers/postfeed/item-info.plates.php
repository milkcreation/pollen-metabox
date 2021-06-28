<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 * @var Pollen\WpPost\WpPostQueryInterface $item
 */
?>
<div class="MetaboxPostfeed-itemInfoLine MetaboxPostfeed-itemInfoLine--post_type">
    <label><?php _e('Type :', 'tify'); ?></label>
    <?php echo ucfirst($item->getWpPostType()->label('singular_name')); ?>
</div>
<div class="MetaboxPostfeed-itemInfoLine MetaboxPostfeed-itemInfoLine--post_status">
    <label><?php _e('Statut :', 'tify'); ?></label>
    <?php echo $item->getStatus()->getLabel(); ?>
</div>