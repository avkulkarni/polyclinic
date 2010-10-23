<!-- begin tablegrid-head -->
<div class="tablegrid-head">
    <div class="module-head">
        <div class="module-head-c">
            <h2>Obat yang harus dibeli</h2>
            <div class="module-tools">
                <?php if ( $module_permission['operations']['print']): ?>
                <a href='#' class="media print"><span>Print</span></a> | 
                <?php endif;?>
                <?php if ( $module_permission['operations']['csv']): ?>
                <a href='#' class="media csv"><span>Save as csv</span></a> |
                <?php endif;?>
                <?php if ( $module_permission['operations']['pdf']): ?>
                <a href='#' class="media pdf"><span>Save as pdf</span></a>
                <?php endif;?>
            </div>
        </div>
    </div>
    
    <br class="clear" />
    <!-- filter -->
    <?php if (isset($filter)):?>
    <div id="filter-box">
        <form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get" name="search-form" id="search-form">
        <table cellpadding="0" cellspacing="0" id="tablefilter">
            <tr>
                <td colspan="2" class="filter-title"><?php echo $html->image('search.png', array('alt'=>'search')) ?>&nbsp;&nbsp;Filter</td>
            </tr>
            <?php 
                $i = 0;
                foreach ( $filter as $input_field => $label ): 
             ?>
            <tr>
                <td><?php __($label); ?></td>
                <td>
                    <input class="inline" type="text" name="<?php echo "$input_field"; ?>" value="<?php echo isset(${$input_field."Value"}) ? ${$input_field."Value"} : ''; ?>" />
                    <?php 
                        if ( $i == (sizeof($filter) - 1) ) {
                            echo $form->submit('Search', array('div'=>false, 'name' => 'search')) . "&nbsp;";
                            echo $html->link(__('Clear', true), array('action'=>'index'), array('class'=>'clear')) . "&nbsp;";
                        }
                        $i++;
                    ?>
                 </td>
            </tr>
            <?php endforeach; ?>
        </table>
        </form>
    <!-- filter eof --> 
    </div>
    <?php endif;?>
<!-- tablegrid-head eof -->    
</div>

<!-- begin tablegrid -->
<table cellpadding="0" cellspacing="0" class="tablegrid" id="tablegrid_<?php echo $this->params['controller'];?>">
<thead>
<tr>
    <th>No</th>
    <th>Nama Obat</th>
    <th>Satuan</th>
</tr>
</thead>
<tfoot>
<tr>
    <th>No</th>
    <th>Nama Obat</th>
    <th>Satuan</th>
</tr>
</tfoot>
<tbody>
<?php
$i = 0;
if (empty($records)) {
    echo '<tr><td colspan="3" class="noRecord">' . __('No Records', true) . '</td></tr>';
}
foreach ($records as $record):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?php echo $class;?> rel="r<?php echo $record["Medicine"]["id"];?>">
        <td><?php echo $i;?></td>
        <td><?php echo $record['Medicine']['name'];?></td>
        <td><?php echo $record['Unit']['name'];?></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
<!-- end table grid -->
