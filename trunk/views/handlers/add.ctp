<div class="<?=$this->params['controller']?> <?=$html->action?>">
<?php echo $form->create('Handler');?>
	<fieldset>
 		<legend><?php __('Tambah Pemeriksa');?></legend>
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
                    echo $form->submit('Add', array('div'=>false)) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Back', true), array('action'=>'index'), array('class'=>'back'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>	
</div>
