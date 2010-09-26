<?php if ($show_form):?>
<fieldset>
<legend>Rekapitulasi Berobat dan Cek Kesehatan</legend>
<?php echo $form->create();?>
    <table class="input">
        <tr>
            <td>Dari Tanggal</td>
            <td>
                <?php 
                    echo $form->input('date_from', array(
                        'type' => 'date', 'div' => false, 'label' => false
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <td>Sampai Tanggal</td>
            <td>
                <?php 
                    echo $form->input('date_until', array(
                        'type' => 'date', 'div' => false, 'label' => false
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">
                    Untuk rekapitulasi satu hari, pilihan Dari Tanggal dan Sampai Tanggal<br />
                    disamakan saja.<br />
                </span><br />
                <input type="submit" value="Cetak" />
            </td>
        </tr>
    </table>
</form>
</fieldset>
<?php else:?>
<h1>REKAPITULASI PASIEN BEROBAT DAM CEK KESEHATAN</h1>
<h2>Per <?php echo $date;?></h2>
<br />
<center>
<table class="vAlignTop">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No Rekam Medis</th>
            <th>Nama Pasien</th>
            <th>Unit Kerja / Pekerjaan</th>
            <th>Pemeriksaan</th>
            <th>Diagnosis</th>
            <th>Obat yang diberikan</th>
            <th>Pemeriksa</th>
        </tr>
    </thead>
    <?php if (!empty($records)):?>
    <?php foreach ($records as $no => $record):?>
    <tr>
        <td><?php echo $no;?></td>
        <td><?php echo $time->format('d/m/Y', $record['date']);?></td>
        <td><?php echo $record['patient_code'];?></td>
        <td><?php echo $record['patient_name'];?></td>
        <td><?php echo $record['patient_work'];?></td>
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
</center>
<?php endif;?>
