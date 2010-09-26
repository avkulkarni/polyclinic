<div class="<?php echo $this->params['controller']; ?> index">
<?php echo $this->element('tablegrid',
        array(
            "fields" => array(
                "name" => array(
                    'title' => __('Name', true),
                    'sortable' => false
                ),
                "handler_type_id" => array(
                    'title' => __('Jenis Pemeriksa', true),
                    'sortable' => true
                )
            ),
            "editable"  => "name",
            'assoc' => array(
                'handler_type_id' => array(
                    'model' => 'HandlerType',
                    'field' => 'name'
                )
            )
        ));
?>
</div>
