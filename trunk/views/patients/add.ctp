<div class="<?=$this->params['controller']?> <?=$html->action?>">
<?php echo $form->create('Patient');?>
	<fieldset>
 		<legend><?php __('Tambah Pasien');?></legend>
        <table class="input">
            <tr>
                <td class="label-required"><?php echo __('Name', true);?></td>
                <td><?php echo $form->input('name', array('div'=>false, 'label'=>false, 'class'=>'required'));?></td>
            </tr>
            <tr>
                <td class="label-required"><?php echo __('Jenis Pasien', true);?></td>
                <td>
                <?php
                    echo $form->select('patient_type_id', $types, null, array(
                        'div' => false, 'label' => false, 'class' => 'required',
                        'empty' => false
                    ));
                ?>
                </td>
            </tr>
            <tr>
                <td class="label-required">Pengguna terkait<br />
                <span class="label">Jika pasien ini memiliki username<br />
                dan juga pegawai, pilih namanya.
                </span>
                </td>
                <td>
                <?php
                    echo $form->select('user_id', $users, null, array(
                        'div' => false, 'label' => false, 'class' => 'required',
                        'empty' => ''
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
