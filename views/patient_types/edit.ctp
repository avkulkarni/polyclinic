<div class="<?=$this->params['controller']?> <?=$html->action?>">
<?php echo $form->create('PatientType');?>
	<fieldset>
 		<legend><?php __('Edit Jenis Pasien');?></legend>
        <table class="input">
            <tr>
                <td class="label-required"><?php echo __('Name', true);?></td>
                <td><?php echo $form->input('name', array('div'=>false, 'label'=>false, 'class'=>'required'));?></td>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                    echo $form->submit(__('Update', true), array('div'=>false)) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Delete', true), array('action'=>'delete', $this->data['PatientType']['id']), array('class'=>'del'), sprintf(__('Are you sure you want to delete', true) . ' %s?', $this->data['PatientType']['name'])) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Back', true), array('action'=>'index'), array('class'=>'back'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>
</div>
