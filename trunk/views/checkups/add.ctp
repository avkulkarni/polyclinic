<?php echo $html->script('jquery.numeric', false);?>
<?php echo $html->scriptBlock($ajaxURL);?>
<?php echo $html->scriptBlock('var units = ' . json_encode($units) . ';');?>
<?php echo $html->script('checkups.js?20101002', false);?>
<?php echo $html->css('checkups', 'stylesheet', array('inline' => false));?>
<div class="<?=$this->params['controller']?> <?=$html->action?>">
<?php echo $form->create('Checkup');?>
	<fieldset>
 		<legend><?php __('Input Pemeriksaan');?></legend>
        <table class="input">
            <tr>
                <td>
                    <table class="input">
                        <tr>
                            <td class="label-required">
                                <?php echo __('Tgl. Pemeriksaan', true);?> &rarr;<br />
                                <span class="label">Tanggal dilakukan pemeriksaan</span>
                            </td>
                            <td>
                                <?php 
                                    echo $form->input('checkup_date', array(
                                        'div' => false, 'label' => false, 'type' => 'date',
                                        'class' => 'inputText'
                                    ));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-required">
                                <?php echo __('Nama Pasien', true);?> &rarr;<br />
                                <span class="label">
                                    Pilih nama pasien,<br />dibawahnya akan muncul detailnya.
                                </span>
                            </td>
                            <td>
                                <?php 
                                    echo $form->select('patient_id', $patients, null, array(
                                        'div' => false, 'label' => false, 'class' => 'inpuText',
                                        'empty' => 'Pilih Nama Pasien'
                                    ));
                                    echo ($form->isFieldError('patient_id')) ? $form->error('patient_id') : '';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" id="patient_detail"></td>
                        </tr>
                        <tr>
                            <td class="label-required">
                                <?php echo __('Nama Pemeriksa', true);?> &rarr;<br />
                                <span class="label">Pilih nama pemeriksa<br />pasien.</span>
                            </td>
                            <td>
                                <?php 
                                    echo $form->select('handler_id', $handlers, null, array(
                                        'div' => false, 'label' => false, 'class' => 'inpuText',
                                        'empty' => 'Pilih Nama Pemeriksa'
                                    ));
                                    echo ($form->isFieldError('handler_id')) ? $form->error('handler_id') : '';
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="input">
                        <tr>
                            <td class="label-required">
                                <?php echo __('Jenis Pemeriksaan', true);?> &rarr;<br />
                                <span class="label">
                                    Tekan Ctrl dan pilih jenis pemeriksaan<br />
                                    untuk lebih dari satu pemilihan
                                </span>
                            </td>
                            <td>
                                <?php
                                    echo $form->input('Checktype', array(
                                        'div' => false, 'label' => false, 'multiple' => true, 'empty' => 'Tidak ada'
                                    ));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-required">
                                <?php echo __('Diagnosis', true);?> &rarr;<br />
                                <span class="label">
                                    Tekan Ctrl dan pilih Diagnosis<br />
                                    untuk lebih dari satu pemilihan.
                                </span>
                            </td>
                            <td>
                                <?php 
                                    echo $form->input('Diagnosis', array(
                                        'div' => false, 'label' => false, 'multiple' => true, 'empty' => 'Tidak ada'
                                    ));
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table id="checkups_medicines" class="checkups_medicines">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Nama Obat</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ( isset($checkups['CheckupsMedicine']) && !empty($checkups['CheckupsMedicine']) ): ?>
                        <?php
                            foreach ($checkups['CheckupsMedicine'] as $key => $checkups_medicine):
                                $total_price = 0;
                                
                                echo '<tr class="row_checkups_medicine" id="r'.$key.'">';
                                    echo '<td>';
                                    echo '<input type="checkbox" name="data[Checkup][CheckupsMedicine]['.$key.'][id]" ' .
                                         'class="cb_checkups_medicines" class="inputText" />';
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $form->select(
                                        'medicine_id', $medicines, $checkups_medicine['medicine_id'],
                                        array(
                                            'id' => null,
                                            'class' => 'inputText medicine_id',
                                            'name' => 'data[Checkup][CheckupsMedicine]['.$key.'][medicine_id]',
                                            'empty' => ''
                                        )
                                    );
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $form->input('qty', array(
                                            'div' => false, 'label' => false, 'size' => 2,
                                            'id' => null, 'class' => 'inputText qty numeric',
                                            'name' => 'data[Checkup][CheckupsMedicine]['.$key.'][qty]',
                                            'value' => $checkups_medicine['qty']
                                        ));
                                    echo '</td>';
                                    echo '<td class="unit">' .
                                         (isset($units[$checkups_medicine['medicine_id']]) ?
                                               $units[$checkups_medicine['medicine_id']] : '') .
                                         '</td>';
                                echo '</tr>';
                            endforeach;
                        ?>
                        <?php else: ?>
                            <tr class="row_checkups_medicine" id="r0">
                                <td>
                                    <input type="checkbox" name="data[Checkup][CheckupsMedicine][0][id]" class="cb_checkups_medicines" class="inputText" />
                                </td>                         
                                <td> 
                                    <?php 
                                        echo $form->select(
                                            'mecicine_id', $medicines, null,
                                            array(
                                                'id' => null,
                                                'class' => 'medicine_id',
                                                'name' => 'data[Checkup][CheckupsMedicine][0][medicine_id]',
                                                'empty' => ''
                                            )
                                        );
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        echo $form->input('qty', array(
                                            'div' => false, 'label' => false, 'size' => 2,
                                            'id' => null, 'class' => 'inputText qty numeric',
                                            'name' => 'data[Checkup][CheckupsMedicine][0][qty]'
                                        ));
                                    ?>
                                </td>
                                <td class="unit">&nbsp;</td>
                            </tr>
                        <?php endif;?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">
                                    <span class="up"></span>
                                    <input type="button" name="add_row" id="add_row" value="+ Tambah Obat" /> &nbsp; atau &nbsp;
                                    <input type="button" name="del_row" id="del_row" value="- Hapus Obat" />
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                    echo $form->submit('Simpan', array('div'=>false)) . "&nbsp;" . __('or', true) . "&nbsp;";
                    echo $html->link(__('Back', true), array('action'=>'index'), array('class'=>'back'));
                ?>
                </td>
            </tr>
        </table>
	</fieldset>
</form>	
</div>
