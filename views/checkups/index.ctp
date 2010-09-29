<div class="<?php echo $this->params['controller']; ?> index">
<?php echo $this->element('tablegrid',
        array("fields" => array(
                "checkup_date" => array(
                    'title' => 'Tgl. Pemeriksaan',
                    'sortable' => true
                ),
                'patient_id' => array(
                    'title' => 'Nama pasien',
                    'sortable' => false
                ),
                'handler_id' => array(
                    'title' => 'Nama pemeriksa',
                    'sortable' => false
                ),
                'checktype' => array(
                    'title' => 'Jenis pemeriksaan',
                    'sortable' => false
                ),
                'diagnosis' => array(
                    'title' => 'Diagnosis',
                    'sortable' => false
                ),
                'medicine' => array(
                    'title' => 'Obat-obatan',
                    'sortable' => false
                )
              ),
              "editable"  => "checkup_date",
              'assoc' => array(
                'patient_id' => array(
                    'model' => 'Patient',
                    'field' => 'name'
                ),
                'handler_id' => array(
                    'model' => 'Handler',
                    'field' => 'name'
                )
              )
        ));
?>
</div>
