<?php if ($show_form):?>
<fieldset>
<legend>Rekapitulasi Berobat dan Cek Kesehatan</legend>
<?php echo $form->create();?>
    <table class="input">
        <tr>
            <td>Nama Pasien</td>
            <td>
                <?php 
                    echo $form->select('patient_id', $patients, null, array(
                        'div' => false, 'label' => false, 'empty' => 'Pilih pasien'
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Cetak" />
            </td>
        </tr>
    </table>
</form>
</fieldset>
<?php else:?>

<?php endif;?>
