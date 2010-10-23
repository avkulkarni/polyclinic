<?php
echo '<ul style="padding-top: 3px;">' .
     '<li><span style="color: #666">Total Barang</span> : <strong>' . $total_items . '</strong></li>' .
     '<li><span style="color: #666">Barang yang Tersedia</span>: <strong>' . $available_stocks . '</strong></li>' .
     '<li><span style="color: #666">Barang di bawah stok</span> : <strong>' . $zero_stocks . '</strong></li>' .
     '<li><span style="color: #666">Ketersedian Barang : <strong>' .
        number_format(($available_stocks/$total_items)*100, 2, '.', ',') . '%</strong></li>' .
     '</ul>';
?>
