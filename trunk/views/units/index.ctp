<div class="<?php echo $this->params['controller']; ?> index">
<?php echo $this->element('tablegrid',
        array("fields" => array(
                "name" => array(
                    'title' => __("Nama Satuan", true), 
                    'sortable' => true
                )
              ),
              "editable"  => "name"
        ));
?>
</div>
