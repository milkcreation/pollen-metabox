<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 */
?>
<?php if ($src = $this->get('src')) : ?>
<div class="MetaboxImagefeed-itemPreview">
    <img src="<?php echo $src; ?>" alt="<?php echo $this->get('alt', md5($src)); ?>">
</div>
<?php endif; ?>

<?php echo $this->field('hidden', [
    'attrs' => [
        'data-control' => 'metabox-imagefeed.item.input',
    ],
    'name'  => $this->get('name'),
    'value' => $this->get('value'),
]);