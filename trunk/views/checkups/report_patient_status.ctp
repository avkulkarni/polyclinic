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
<h1>STATUS PASIEN</h1>
<br />

<center>
<table>
    <tr>
        <td>
            <?php if (!empty($records) && isset($records[1])):?>
            <table class="noborder">
                <tr>
                    <td>Nama</td>
                    <td><?php echo $records[1]['patient_name'];?></td>
                </tr>
                <tr>
                    <td>Nomor Medical Record</td>
                    <td><?php echo $records[1]['patient_code'];?></td>
                </tr>
                <tr>
                    <td>Unit Kerja</td>
                    <td><?php echo $records[1]['patient_work'];?></td>
                </tr>
            </table>
            <?php endif;?>
        </td>
    </tr>
    <tr>
        <td>
            <table class="vAlignTop">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Anamnes/Pemeriksaan</th>
                    <th>Diagnosa/Pengobatan</th>
                    <th>Obat yang diberikan</th>
                    <th>Pemeriksa</th>
                </tr>
            </thead>
            <?php if (!empty($records)):?>
            <?php foreach ($records as $no => $record):?>
            <tr>
                <td><?php echo $no;?></td>
                <td><?php echo $time->format('d/m/Y', $record['date']);?></td>
                <td><?php echo $record['checktypes'];?></td>
                <td><?php echo $record['diagnoses'];?></td>
                <td><?php echo $record['medicines'];?></td>
                <td><?php echo $record['handler'];?></td>
            </tr>
            <?php endforeach;?>
            <?php else:?>
            <tr>
                <td colspan="9">Tidak ada data</td>
            </tr>
            <?php endif;?>
        </table>
        </td>
    </tr>
</table>

</center>
<?php endif;?>
