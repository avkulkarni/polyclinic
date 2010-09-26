<?php echo $this->element('tablegrid.box',
        array("fields"    => array("code" => "Code", "name" => "Name"),
              "editable"  => "code",
              "filter"    => array("code" => "Code", "name" => "Name")
        ));
?>
