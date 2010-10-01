<div class="<?php echo $this->params['controller']; ?> index">
<?php echo $this->element('tablegrid',
        array(
            "fields" => array(
                "name" => array(
                    'title' => __('Name', true),
                    'sortable' => false
                ),
                'unit_id' => array(
                    'title' => 'Satuan',
                    'sortable' => true
                )
            ),
            'assoc' => array(
                'unit_id' => array(
                    'model' => 'Unit',
                    'field' => 'name'
                )
            ),
            "editable"  => "name"
        ));
?>
</div>
