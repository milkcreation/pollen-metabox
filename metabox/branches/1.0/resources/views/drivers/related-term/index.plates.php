<?php
/**
 * @var Pollen\Metabox\MetaboxTemplateInterface $this
 */
?>
<?php foreach ($this->get('taxonomy', []) as $tax) : ?>
    <?php echo $this->field('hidden', ['name' => "tax_input[{$tax}][]", 'value' => '']); ?>
<?php endforeach; ?>

<?php if ($this->get('multiple', true)) : ?>
    <?php echo $this->field('checkbox-collection', [
        'choices' => $this->get('items', []),
        'name'    => $this->get('name'),
        'value'   => $this->get('value')
    ]); ?>
<?php else : ?>
    <?php echo $this->field('radio-collection', [
        'choices' => $this->get('items', []),
        'name'    => $this->get('name'),
        'value'   => $this->get('value')
    ]); ?>
<?php endif; ?>
