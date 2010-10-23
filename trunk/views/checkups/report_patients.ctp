<h1>REKAPITULASI JUMLAH PASIEN</h1>
<br />
<center>
<table class="vAlignTop">
    <thead>
        <tr>
            <th>No</th>
            <th>Pasien</th>
            <th>Jumlah</th>
            <th>Presentase</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($records)):?>
        <?php $no = 0;?>
        <?php foreach ($records as $name => $total):?>
        <tr>
            <td><?php echo ++$no;?></td>
            <td><?php echo $name;?></td>
            <td><?php echo $total;?> pasien</td>
            <td><?php echo number_format(($total / $total_patients) * 100, 2, '.', '');?> %</td>
        </tr>
        <?php endforeach;?>
        <tr>
            <td colspan="2" class="right">Jumlah</td>
            <td><?php echo $total_patients;?> pasien</td>
            <td>100 %</td>
        </tr>
    <?php else: ?>
    <tr>
        <td colspan="4" class="center">Tidak ada data</td>
    </tr>
    <?php endif;?>
    </tbody>
</table>
</center>