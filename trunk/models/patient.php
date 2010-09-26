<?php
class Patient extends AppModel {
    var $validate = array(
        'name' => array(
            'required' => array(
                'required' => true,
                'allowEmpty' => false,
                'rule' => '/^\w+[\w\s]?/',
                'message' => 'This field cannot be left blank and must be alphanumeric'
            ),
            'maxlength' => array(
                'rule' => array('maxLength', 100),
                'message' => 'Maximum characters is 100 characters'
            )
        )
        
    );
    
    var $belongsTo = array(
        'PatientType',
        'CreatedBy' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'fields' => array('id', 'name')
        )
    );
    
    function beforeSave($created) {
        if ( $created ) {
            $date_conditions = date('Y-m-d');
            $counter = $this->find('count', array(
                'conditions' => array(
                    'created >=' => $date_conditions . ' 00:00:00',
                    'created <' => $date_conditions . ' 24:00:00'
                ), 'recursive' => -1
            ));
            
            $this->data[$this->alias]['code'] = str_pad($counter+1, 4 , '0', STR_PAD_LEFT) . date('dmy');
        }
        
        return true;
    }
}
?>
