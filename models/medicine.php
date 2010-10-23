<?php
class Medicine extends AppModel {
    var $belongsTo = array(
        'Unit'
    );
    
    function afterDelete() {
        $this->bindModel(array(
            'hasMany' => array(
                'CheckupsMedicine' => array(
                    'className' => 'CheckupsMedicine'
                )
            )
        ));
        
        $this->CheckupsMedicine->deleteAll(
            array('CheckupsMedicine.medicine_id' => $this->id)
        );
        
        return true;
    }
}
?>
