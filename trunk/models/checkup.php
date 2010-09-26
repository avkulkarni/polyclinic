<?php
class Checkup extends AppModel {
    var $validate = array(
        'checkup_date' => array(
            'rule' => 'date',
            'message' => 'Tanggal tidak valid'
        ),
        'patient_id' => array(
            'rule' => 'vPatient',
            'message' => 'Data pasien tidak ada'
        ),
        'handler_id' => array(
            'rule' => 'vHandler',
            'message' => 'Data pemeriksa tidak ada'
        )
    );
    
    var $belongsTo = array(
        'Patient', 'Handler',
        'CreatedBy' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'fields' => array('id', 'name')
        )
    );
    
    var $hasMany = array(
        'CheckupsMedicine' => array(
            'className' => 'CheckupsMedicine'
        )
    );
    
    var $hasAndBelongsToMany = array(
        'Checktype' => array(
            'className' => 'Checktype',
            'joinTable' => 'checktypes_checkups',
            'foreignKey' => 'checkup_id',
            'associationForeignKey' => 'checktype_id',
            'unique' => true
        ),
        'Diagnosis' => array(
            'className' => 'Diagnosis',
            'joinTable' => 'checkups_diagnoses',
            'foreignKey' => 'checkup_id',
            'associationForeignKey' => 'diagnosis_id',
            'unique' => true
        )
    );
    
    function vPatient($field) {
        return $this->Patient->find('count', array(
            'conditions' => array(
                'id' => $field['patient_id']
            ),
            'recursive' => -1
        ));
    }
    
    function vHandler($field) {
        return $this->Handler->find('count', array(
            'conditions' => array(
                'id' => $field['handler_id']
            ),
            'recursive' => -1
        ));
    }
}
