<h1>LAPORAN STOK OBAT</h1>
<h2>Per <?php echo date('d/m/Y'); ?></h2>
<br />
<table align="center">
<thead>
    <tr>
        <th rowspan="2">No.</th>
        <th rowspan="2">Nama Obat</th>
        <th rowspan="2">Satuan</th>
        <th colspan="2" style="text-align: center">Jumlah</th>
        <th rowspan="2">Stok</th>
    </tr>
    <tr>
        <th>Penerimaan</th>
        <th>Pengeluaran</th>
    </tr>
</thead>
<tbody>
<?php
    $no = 1;
    if (!empty($medicines)) {
        foreach ($medicines as $medicine) {
            echo '<tr>';
            echo '<td>'.$no++.'</td>';
            echo '<td>'.$medicine['Medicine']['name'].'</td>';
            echo '<td>'.$medicine['Unit']['name'].'</td>';
            echo '<td>'.$medicine['Medicine']['penerimaan'].'</td>';
            echo '<td>'.$medicine['Medicine']['pengeluaran'].'</td>';
            echo '<td>'.$medicine['Medicine']['stok'].'</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">Tidak ada data.</td></tr>';
    }
?>
</tbody>
</table>
<br />
<span>Obat di bawah minimum : <?php echo $minimum['item']['Medicine']['name']; ?></span>
<span>Obat terlaris : <?php echo $terlaris['item']['Medicine']['name']; ?></span>
