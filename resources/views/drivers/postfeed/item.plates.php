<?php
/**
 * @var Pollen\Metabox\MetaboxTemplate $this
 * @var Pollen\WpPost\WpPostQueryInterface $item
 */
?>
<?php if ($thumbnail = $item->getThumbnail('thumbnail', ['class' => 'MetaboxPostfeed-itemThumbImg'])) : ?>
    <figure class="MetaboxPostfeed-itemThumb"><?php echo $thumbnail; ?></figure>
<?php else : ?>
    <figure class="MetaboxPostfeed-itemThumb">
        <?php echo $this->partial('holder', ['content' => __('indispo.', 'tify')]); ?>
    </figure>
<?php endif; ?>

<h4 class="MetaboxPostfeed-itemTitle"><?php echo $item->getTitle(); ?></h4>

<?php echo $this->field('hidden', [
    'attrs' => [
        'data-control' => 'metabox-postfeed.item.input',
    ],
    'name'  => $this->getName() . '[items][]',
    'value' => $item->getId()
]);