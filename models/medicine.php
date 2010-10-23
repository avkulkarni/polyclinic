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
                ),
                'MedicineIn' => array(
                    'className' => 'MedicineIn'
                )
            )
        ));
        
        $this->CheckupsMedicine->deleteAll(
            array('CheckupsMedicine.medicine_id' => $this->id)
        );
        $this->MedicineIn->deleteAll(
            array('MedicineIn.medicine_id' => $this->id)
        );
        
        return true;
    }
}
?>
