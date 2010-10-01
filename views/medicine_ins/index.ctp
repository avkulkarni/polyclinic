<div class="<?php echo $this->params['controller']; ?> index">
<?php
    echo $this->element('tablegrid',
        array("fields" => array(
                "medicine_id" => array(
                    'title' => __("Nama Obat", true),
                    'sortable' => true
                ),
                "total" => array(
                    'title' => __("Jumlah", true),
                    'sortable' => false
                ),
                "unit_name" => array(
                    'title' => __("Satuan", true),
                    'sortable' => false
                ),
                "date_in" => array(
                    'title' => __("Tanggal Terima Barang", true),
                    'sortable' => true
                ),
                "created_by" => array(
                    'title' => __("Diinput oleh", true),
                    'sortable' => true
                ),
                "created" => array(
                    'title' => __("Waktu input", true),
                    'sortable' => true
                )
              ),
              "editable"  => "medicine_id",
              "assoc" => array(
                'medicine_id' => array(
                    'field' => 'name',
                    'model' => 'Medicine'
                ),
                'unit_name' => array(
                    'field' => 'unit_name',
                    'model' => 'Medicine'
                ),
                'created_by' => array(
                    'field' => 'name',
                    'model' => 'User'
                )
              ),
              "displayedAs" => array(
                'date_in' => 'date',
                'created' => 'datetime'
              )
        ));
?>
</div>
