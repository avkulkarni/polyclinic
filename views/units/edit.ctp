<div class="<?=$this->params['controller']?> <?=$html->action?>">
    <?php echo $form->create('Unit');?>
	<fieldset>
 		<legend><?php __('Add Unit');?></legend>
        <table class="input">
            <tr>
                <td class="label-required"><?php echo __('Name of unit');?>:</td>
                <td><?php echo $form->input('name', array('div'=>false, 'label' => false, 'maxlength' => 100, 'class'=>'required'));?></td>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                    echo $form->submit(__('Save', true), array('div'=>false)) . '&nbsp;' . __('or', true) . '&nbsp;';
                    echo $html->link(__('Delete', true), array('action'=>'delete', $this->data['Unit']['id']), array('class'=>'del'), sprintf(__('Are you sure you want to delete', true) . ' %s?', $this->data['Unit']['name'])) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Back to index', true), array('action'=>'index'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>
</div>
