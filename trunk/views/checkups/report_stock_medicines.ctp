<?php if ($show_form):?>
<fieldset>
<legend>Kartu Stok Obat</legend>
<?php echo $form->create('Checkup');?>
    <table class="input">
        <tr>
            <td>Nama Obat</td>
            <td>
                <?php 
                    echo $form->select('medicine_id', $medicines, null, array(
                        'type' => 'date', 'div' => false, 'label' => false,
                        'empty' => false
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <td>Sampai Tanggal</td>
            <td>
                <?php
                    echo $form->input('periode', array(
                        'type' => 'date', 'div' => false, 'label' => false
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
<h1>KARTU STOK OBAT</h1>
<h2>Per <?php echo $checkup_date;?></h2>
<br />
<center>
<table class="vAlignTop">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Tanggal Terima</th>
            <th colspan="2" style="text-align: center">Jumlah</th>
            <th rowspan="2">Stok</th>
            <th rowspan="2">Pasien</th>
        </tr>
        <tr>
            <th>Penerimaan</th>
            <th>Pengeluaran</th>
        </tr>
    </thead>
    <?php
    $no = 0;
    if (!empty($medicines)) {
        foreach ($medicines as $medicine) {
            echo '<tr>';
            echo '<td>' . ++$no . '</td>';
            echo '<td>';
                if ( isset($medicine['date_in']) && !empty($medicine['date_in']) ) {
                    echo $time->format('d/m/Y', $medicine['date_in']);
                } else {
                    echo '&nbsp;';
                }
            echo '</td>';
            echo '<td>'.$medicine['penerimaan_periode'].'</td>';
            echo '<td>'.$medicine['pengeluaran'].'</td>';
            echo '<td>'.$medicine['stock'].'</td>';
            echo '<td>'.$medicine['pengebon'].'</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6" class="center">Tidak ada data.</td></tr>';
    }
    ?>
</table>
</center>
<?php endif;?>
