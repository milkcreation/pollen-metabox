<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 */
?>
<h3 class="MetaboxSlidefeed-itemFieldLabel"><?php _e('Légende', 'tify'); ?></h3>

<?php echo $this->partial('tag', [
    'attrs' => [
        'id'    => 'MetaboxSlidefeed-itemFieldCaptionToolbar--' . $this->get('index'),
        'class' => 'MetaboxSlidefeed-itemFieldCaptionToolbar',
    ],
]); ?>

<?php echo $this->field('tinymce', [
    'attrs'   => [
        'id'    => $this->get('name') . '[caption]',
        'class' => 'MetaboxSlidefeed-itemFieldCaptionInput',
    ],
    'options' => [
        'fixed_toolbar_container' => '#MetaboxSlidefeed-itemFieldCaptionToolbar--' . $this->get('index'),
        'inline'                  => true,
        'menubar'                 => false,
        'toolbar'                 => 'bold italic',
    ],
    'tag'     => 'div',
    'value'   => $this->get('value.caption'),
]);