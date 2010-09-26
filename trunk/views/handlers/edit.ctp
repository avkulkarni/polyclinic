<div class="<?=$this->params['controller']?> <?=$html->action?>">
<?php echo $form->create('Handler');?>
	<fieldset>
 		<legend><?php __('Edit Pemeriksa');?></legend>
        <table class="input">
            <tr>
                <td class="label-required"><?php echo __('Name', true);?></td>
                <td><?php echo $form->input('name', array('div'=>false, 'label'=>false, 'class'=>'required'));?></td>
            </tr>
            <tr>
                <td class="label-required"><?php echo __('Jenis Pemeriksa', true);?></td>
                <td>
                <?php
                    echo $form->select('handler_type_id', $types, null, array(
                        'div' => false, 'label' => false, 'class' => 'required',
                        'empty' => false
                    ));
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                    echo $form->submit(__('Update', true), array('div'=>false)) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(
                        __('Delete', true),
                        array('action'=>'delete', $this->data['Handler']['id']),
                        array('class'=>'del'),
                        sprintf(__('Are you sure you want to delete', true) . ' %s?', $this->data['Handler']['name'])
                    ) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Back', true), array('action'=>'index'), array('class'=>'back'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>
</div>
