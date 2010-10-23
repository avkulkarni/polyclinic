<?php
echo '<ul style="padding-top: 3px;">' .
     '<li><span style="color: #666">Total Obat</span> : <strong>' . $total_items . '</strong></li>' .
     '<li><span style="color: #666">Obat yang Tersedia</span>: <strong>' . $available_stocks . '</strong></li>' .
     '<li><span style="color: #666">Obat di bawah stok</span> : <strong>' . $zero_stocks . '</strong></li>' .
     '<li><span style="color: #666">Ketersedian Obat : <strong>' .
        number_format(($available_stocks/$total_items)*100, 2, '.', ',') . '%</strong></li>' .
     '</ul>';
?>
