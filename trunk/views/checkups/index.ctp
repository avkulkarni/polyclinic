<div class="<?php echo $this->params['controller']; ?> index">
<?php echo $this->element('tablegrid',
        array("fields" => array(
                "checkup_date" => array(
                    'title' => __("Tgl. Pemeriksaan", true), 
                    'sortable' => true
                )
              ),
              "editable"  => "checkup_date",
        ));
?>
</div>
