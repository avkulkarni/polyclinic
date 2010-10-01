<div class="<?=$this->params['controller']?> <?=$html->action?>">
    <?php echo $form->create('MedicineIn');?>
	<fieldset>
 		<legend><?php __('Edit Obat Masuk');?></legend>
        <table class="input">
            <tr>
                <td><?php echo __('Nama Obat');?>:</td>
                <td><?php echo $form->select('medicine_id', $medicines, null, array('div'=>false, 'label' => false));?></td>
            </tr>
            <tr>
                <td class="label-required"><?php echo __('Jumlah');?>:</td>
                <td>
                    <?php 
                        echo $form->input('total', array('div'=>false, 'label' => false, 'maxlength' => 100, 'class'=>'required'));
                    ?>
                    &nbsp; <span id="satuan"></span>
                </td>
            </tr>
            <tr>
                <td><?php echo __('Tanggal terima obat');?>:</td>
                <td><?php echo $form->input('date_in', array('div'=>false, 'label' => false, 'class' => 'inputDate'));?></td>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                    echo $form->submit(__('Save', true), array('div'=>false)) . '&nbsp;' . __('or', true) . '&nbsp;';
                    echo $html->link(__('Delete', true), array('action'=>'delete', $this->data['MedicineIn']['id']), array('class'=>'del'), sprintf(__('Are you sure you want to delete', true) . ' %s?', ' no ' . $this->data['Medicine']['name'])) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Back to index', true), array('action'=>'index'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>
</div>
<?php echo $javascript->codeBlock($getSatuan);?>
<script type="text/javascript">
    $(function() {
        if ( $('#MedicineInMedicineId').val() ) {
            $.get(getSatuan + $('#MedicineInMedicineId').val(), function(result){
                $('#satuan').html(result);
            });
        }
        
        $('#MedicineInMedicineId').change(function() {
            $.get(getSatuan + this.value, function(result){
                $('#satuan').html(result);
            });
        });
    });
</script>
