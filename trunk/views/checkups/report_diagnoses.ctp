<?php if ( $show_form ): ?>
<div class="<?=$this->params['controller']?> <?=$html->action?>">
    <?php echo $form->create('Checkup', array('action' => 'report_diagnoses'));?>
	<fieldset>
 		<legend>REKAPITULASI DIAGNOSIS PASIEN</legend>
        <table class="input">
            <tr id="afterThis">
                <td class="label-required"><?php echo __('Bulan / Tahun');?>:</td>
                <td>
                    <?php
                        echo $form->month('month', date('m'), array(), false);
                        echo '-';
                        echo $form->year('year', 2009, date('Y'), date('Y'));
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                    echo $form->submit(__('Print', true), array('div'=>false, 'id' => 'print'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>
</div>
<?php else: ?>
<h1>REKAPITULASI DIAGNOSIS PASIEN <br />Per <?php echo $month . ' ' . $year;?></h1>
<center>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Diagnosis</th>
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
        <td><?php echo number_format(($total / $total_checkup) * 100, 2, '.', '');?> %</td>
    </tr>
    <?php endforeach;?>
    <?php else: ?>
    <tr>
        <td colspan="4" class="center">Tidak ada data</td>
    </tr>
    <?php endif;?>
    </tbody>
</table>
</center>
<?php endif;?>
