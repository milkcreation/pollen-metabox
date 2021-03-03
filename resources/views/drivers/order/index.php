<?php
/**
 * @var Pollen\Metabox\MetaboxViewLoaderInterface $this
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <label><?php _e( 'Choix de l\'ordre', 'tify'); ?></label>
            <em style="display:block;color:#999;font-size:11px;font-weight:normal;">
                <?php _e( '(-1 pour masquer l\'élément)', 'tify'); ?>
            </em>
        </th>
        <td>
            <?php echo $this->field('number', array_merge($this->all(), [
                'name' => $this->getName(),
                'value' => $this->getValue()
            ])); ?>
        </td>
    </tr>
    </tbody>
</table>