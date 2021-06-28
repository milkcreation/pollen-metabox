<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 */
?>
<div class="MetaboxVideofeed-itemPoster">
    <?php echo $this->field('media-image', [
        'attrs'   => [
            'data-control' => 'metabox-videofeed.item.poster',
        ],
        'content' => __('Image de couverture', 'tify'),
        'infos'   => false,
        'height'  => 150,
        'name'    => $this->get('name') . '[poster]',
        'value'   => $this->get('value.poster'),
        'width'   => 150,
    ]); ?>
</div>

<div class="MetaboxVideofeed-itemSrc">
    <?php echo $this->field('textarea', [
        'attrs' => [
            'data-control' => 'metabox-videofeed.item.input',
            'placeholder'  => __('Url de la vidéo ou iframe', 'tify'),
            'rows'         => 5,
            'cols'         => 40,
        ],
        'name'  => $this->get('name') . '[src]',
        'value' => $this->get('value.src'),
    ]); ?>
</div>

<div class="MetaboxVideofeed-itemLibrary">
    <?php echo $this->partial('tag', [
        'attrs'   => [
            'data-control' => 'metabox-videofeed.item.library',
        ],
        'content' => __('Vidéo de la librairie média', 'tify'),
        'tag'     => 'button',
    ]); ?>
</div>