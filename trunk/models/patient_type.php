<?php
class PatientType extends AppModel {
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
}
?>
