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
    
    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        $this->Behaviors->attach('Containable');
        $contain = array(
            'Patient' => array('fields' => array('name')),
            'Handler' => array('fields' => array('name')),
            'CheckupsMedicine',
            'Checktype' => array('fields' => array('name')),
            'Diagnosis' => array('fields' => array('name'))
        );
        $records = $this->find('all', compact(
            'conditions', 'fields', 'order', 'limit',
            'page', 'recursive', 'group', 'contain'
            )
        );
        
        $_medicines = $this->CheckupsMedicine->Medicine->find('all');
        $medicines = array();
        $units = array();
        foreach ($_medicines as $medicine) {
            $medicines[$medicine['Medicine']['id']] = $medicine['Medicine']['name'];
            $units[$medicine['Medicine']['id']] = $medicine['Unit']['name'];
        }
        foreach ($records as $key => $record) {
            if ( !empty($record['Checktype']) ) {
                $records[$key]['Checkup']['checktype'] = '<ul>';
                foreach ( $record['Checktype'] as $checktype ) {
                    $records[$key]['Checkup']['checktype'] .= '<li>' . $checktype['name'] . '</li>';
                }
                $records[$key]['Checkup']['checktype'] .= '</ul>';
            }
            
            if ( !empty($record['Diagnosis']) ) {
                $records[$key]['Checkup']['diagnosis'] = '<ul>';
                foreach ( $record['Diagnosis'] as $diagnosis ) {
                    $records[$key]['Checkup']['diagnosis'] .= '<li>' . $diagnosis['name'] . '</li>';
                }
                $records[$key]['Checkup']['diagnosis'] .= '</ul>';
            }
            
            if ( !empty($record['CheckupsMedicine']) ) {
                $records[$key]['Checkup']['medicine'] = '<ul>';
                foreach ( $record['CheckupsMedicine'] as $medicine ) {
                    if ( $medicine['qty'] ) {
                        $records[$key]['Checkup']['medicine'] .= '<li>' . $medicines[$medicine['medicine_id']] .
                                                                 ' &rarr ' . $medicine['qty'];
                        if ( isset($units[$medicine['medicine_id']]) ) {
                            $records[$key]['Checkup']['medicine'] .= ' ' . $units[$medicine['medicine_id']];
                        }
                        $records[$key]['Checkup']['medicine'] .= '</li>';
                    }
                }
                $records[$key]['Checkup']['medicine'] .= '</ul>';
            }
            
            /**
             * Medical data
             */
            $records[$key]['Checkup']['medic_data'] = '<table class="noborder">';
            if ( $record['Checkup']['anamnesis'] ) {
                $records[$key]['Checkup']['medic_data'] .= '<tr>';
                $records[$key]['Checkup']['medic_data'] .= '<td><strong>Anamnesis</strong></td>';
                $records[$key]['Checkup']['medic_data'] .= '<td>' . $record['Checkup']['anamnesis'] . '</td>';
                $records[$key]['Checkup']['medic_data'] .= '</tr>';
            }
            if ( $record['Checkup']['physical_check'] ) {
                $records[$key]['Checkup']['medic_data'] .= '<tr>';
                $records[$key]['Checkup']['medic_data'] .= '<td><strong>Pemeriksaan fisik</strong></td>';
                $records[$key]['Checkup']['medic_data'] .= '<td>' . $record['Checkup']['physical_check'] . '</td>';
                $records[$key]['Checkup']['medic_data'] .= '</tr>';
            }
            
            if ( $record['Checkup']['glucose_check'] || $record['Checkup']['uric_acid_check'] ||
                 $record['Checkup']['cholesterol_check'] )
            {
                $records[$key]['Checkup']['medic_data'] .= '<tr>';
                $records[$key]['Checkup']['medic_data'] .= '<td><strong>Pemeriksaan darah</strong></td>';
                $records[$key]['Checkup']['medic_data'] .= '<td>';
                $records[$key]['Checkup']['medic_data'] .= '<ul>';
                if ( $record['Checkup']['glucose_check'] ) {
                    $records[$key]['Checkup']['medic_data'] .= '<li>Glukosa:&nbsp;<strong>' . $record['Checkup']['glucose_check'] . '</strong>&nbsp;mg/dl</li>';
                }
                if ( $record['Checkup']['uric_acid_check'] ) {
                    $records[$key]['Checkup']['medic_data'] .= '<li>Asam&nbsp;urat:&nbsp;<strong>' . $record['Checkup']['uric_acid_check'] . '</strong>&nbsp;mg/dl</li>';
                }
                if ( $record['Checkup']['cholesterol_check'] ) {
                    $records[$key]['Checkup']['medic_data'] .= '<li>Kolesterol:&nbsp;<strong>' . $record['Checkup']['cholesterol_check'] . '</strong>&nbsp;mg/dl</li>';
                }
                $records[$key]['Checkup']['medic_data'] .= '</ul>';
                $records[$key]['Checkup']['medic_data'] .= '</td>';
                $records[$key]['Checkup']['medic_data'] .= '</tr>';
            } else {
                $records[$key]['Checkup']['medic_data'] .= '<tr><td colspan="2">&nbsp;</td></tr>';
            }
            $records[$key]['Checkup']['medic_data'] .= '</table>';
        }
        
        return $records;
    }
}
