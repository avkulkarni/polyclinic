<div class="<?php echo $this->params['controller']; ?> index">
<?php echo $this->element('tablegrid',
        array(
            "fields" => array(
                "name" => array(
                    'title' => __('Name', true),
                    'sortable' => false
                ),
                "code" => array(
                    'title' => __('Nomor Pasien', true),
                    'sortable' => false
                ),
                "patient_type_id" => array(
                    'title' => __('Jenis Pasien', true),
                    'sortable' => true
                ),
                "created" => array(
                    'title' => __('Tanggal Input', true),
                    'sortable' => true
                ),
                "created_by" => array(
                    'title' => __('Diinput oleh', true),
                    'sortable' => false
                )
            ),
            "editable"  => "name",
            'assoc' => array(
                'patient_type_id' => array(
                    'model' => 'PatientType',
                    'field' => 'name'
                ),
                'created_by' => array(
                    'model' => 'CreatedBy',
                    'field' => 'name'
                )
            ),
            'displayedAs' => array(
                'created' => 'datetime'
            )
        ));
?>
</div>
