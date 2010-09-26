<?php
class CheckupsController extends AppController {
    var $pageTitle = 'Data Pemeriksaan';
    
    function add() {
        $this->__setAdditionals();
        $this->__saving();
    }
    
    function edit($id) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid parameter', true), 'error');
			$this->__redirect();
        }
		$this->set('id', $id);
        
        $this->__setAdditionals(1, $id);
        
        $this->__saving(1, $id);
        
		if (empty($this->data)) {
			$this->data = $this->{$this->modelName}->find('first', array(
                'conditions' => array($this->modelName . '.id' => $id)
            ));
            
            if (!$this->data) {
                $this->Session->setFlash(__('Invalid parameter', true), 'error');
                $this->__redirect('index');
            }
		} else {
            $this->data[$this->modelName]['id'] = $id;
        }
    }
    
    function __saving($edit = false, $id = null) {
        if ( !empty($this->data) ) {
			$messageFlashSuccess = (isset($this->niceName) ? $this->niceName : $this->modelName) . ' ' . __('successfully added', true);
			$messageFlashError = (isset($this->niceName) ? $this->niceName : $this->modelName) . ' ' .
                __('cannot add this new record. Please fix the errors mentioned belows', true);
            if ( $edit ) {
                $messageFlashSuccess = (isset($this->niceName) ? $this->niceName : $this->modelName) . ' ' . __("successcully edited", true);
                $messageFlashError = (isset($this->niceName) ? $this->niceName : $this->modelName) . ' ' .
                    __("cannot save this modification. Please fix the errors mentioned belows", true);
            }
            
            if ( $edit ) {
                $this->Checkup->id = $id;
            } else {
                $this->Checkup->create();
            }
            $checkups = $this->data;
            
			if ( $this->Checkup->save($this->data) ) {
                // on editing we need to backup and then delete all medicines
                if ( $edit && $id ) {
                    // delete all
                    $this->Checkup->CheckupsMedicine->deleteAll(
                        array('CheckupsMedicine.checkup_id' => $id),
                        true,
                        true
                    );
                }
				
                foreach ( $this->data['Checkup']['CheckupsMedicine'] as $key => $bd ) {
                    if ( $this->data['Checkup']['CheckupsMedicine'][$key]['medicine_id'] ) {
                        $this->data['Checkup']['CheckupsMedicine'][$key]['checkup_id'] = $this->Checkup->id;
                    } else {
                        unset($this->data['Checkup']['CheckupsMedicine'][$key]);
                    }
                }
                
                if ( !empty($this->data['Checkup']['CheckupsMedicine']) &&
                     !$this->Checkup->CheckupsMedicine->saveAll($this->data['Checkup']['CheckupsMedicine']))
                {
                    // failed saving CheckupsMedicine
                    $this->set('checkups', $checkups['Checkup']);
                    $this->Session->setFlash($messageFlashError, 'error');
                } else {
                    $this->Session->setFlash( $messageFlashSuccess, 'success');
                    $this->__redirect();
                }
			} else {
                $this->set('checkups', $checkups['Checkup']);
                $this->Session->setFlash($messageFlashError, 'error');
			}
		}
    }
    
    function __setAdditionals($edit = false, $id = null) {
        $this->set('patients', $this->Checkup->Patient->find('list'));
        $handler_records = $this->Checkup->Handler->find('all');
        $handlers = array();
        foreach ( $handler_records as $handler_record ) {
            $handlers[ $handler_record['Handler']['id'] ] = $handler_record['HandlerType']['name'] . ' ' .
                $handler_record['Handler']['name'];
        }
        $this->set('handlers', $handlers);
        
        $this->set('medicines', $this->Checkup->CheckupsMedicine->Medicine->find('list'));
        $this->set('diagnoses', $this->Checkup->Diagnosis->find('list'));
        $this->set('checktypes', $this->Checkup->Checktype->find('list'));
        
        if ( $edit ) {
            // get checkups
            $this->Checkup->Behaviors->attach('Containable');
            $checkups = $this->Checkup->find('first', array(
                'conditions' => array(
                    'Checkup.id' => $id
                ),
                'contain' => array(
                    'CheckupsMedicine' => array(
                        'order' => 'CheckupsMedicine.id ASC'
                    )
                ),
                'order' => 'Checkup.id ASC'
            ));
            $this->set('checkups', $checkups);
            
            // get patient detail
            $this->Checkup->Patient->Behaviors->attach('Containable');
            $patient = $this->Checkup->Patient->find('first', array(
                'conditions' => array(
                    'Patient.id' => $checkups['Checkup']['patient_id']
                ),
                'contain' => array(
                    'PatientType'
                )
            ));
            $this->set('patient', $patient);
        }
        
        $this->set('ajaxURL', 'var ajaxURL = "' . $this->__pathToController() . '/patient_detail/";');
    }
    
    function patient_detail($id) {
        $this->layout = 'ajax';
        Configure::write('debug', 0);
        
        $this->Checkup->Patient->Behaviors->attach('Containable');
        $patient = $this->Checkup->Patient->find('first', array(
            'conditions' => array(
                'Patient.id' => $id
            ),
            'contain' => array(
                'PatientType'
            )
        ));
        $this->set('patient', $patient);
    }
    
    function report_diagnoses() {
        $this->layout = 'printhtml';
        Configure::write('debug', 0);
        
        $this->Checkup->Behaviors->attach('Containable');
        $checkups = $this->Checkup->find('all', array(
            'fields' => array('id'),
            'contain' => array(
                'Diagnosis' => array(
                    'fields' => array('id', 'name')
                )
            )
        ));
        
        $records = array();
        foreach ($checkups as $checkup) {
            if ( !empty($checkup['Diagnosis']) ) {
                foreach ( $checkup['Diagnosis'] as $diagnosis ) {
                    if ( !isset($records[$diagnosis['name']]) ) {
                        $records[$diagnosis['name']] = 1;
                    } else {
                        $records[$diagnosis['name']] += 1;
                    }
                }
            }
        }
        $total_checkup = count($checkups);
        $this->set('records', $records);
        $this->set('total_checkup', $total_checkup);
    }
    
    function report_checktypes() {
        $this->layout = 'printhtml';
        Configure::write('debug', 0);
        
        $this->Checkup->Behaviors->attach('Containable');
        $checkups = $this->Checkup->find('all', array(
            'fields' => array('id'),
            'contain' => array(
                'Checktype' => array(
                    'fields' => array('id', 'name')
                )
            )
        ));
        
        $records = array();
        foreach ($checkups as $checkup) {
            if ( !empty($checkup['Checktype']) ) {
                foreach ( $checkup['Checktype'] as $checktype ) {
                    if ( !isset($records[$checktype['name']]) ) {
                        $records[$checktype['name']] = 1;
                    } else {
                        $records[$checktype['name']] += 1;
                    }
                }
            }
        }
        $total_checkup = count($checkups);
        $this->set('records', $records);
        $this->set('total_checkup', $total_checkup);
    }
    
    function report_patients() {
        $this->layout = 'printhtml';
        //Configure::write('debug', 0);
        
        $this->Checkup->Patient->Behaviors->attach('Containable');
        $patients = $this->Checkup->Patient->find('all', array(
            'fields' => array('id'),
            'contain' => array(
                'PatientType' => array(
                    'fields' => array('name')
                )
            )
        ));
        $records = array();
        foreach ($patients as $patient) {
            if ( !isset($records[$patient['PatientType']['name']]) ) {
                $records[$patient['PatientType']['name']] = 1;
            } else {
                $records[$patient['PatientType']['name']]++;
            }
        }
        
        $total_patients = count($patients);
        $this->set('records', $records);
        $this->set('total_patients', $total_patients);
    }
    
    function report_checkups() {
        $show_form = false;
        if ( !empty($this->data['Checkup']['date_from']) && !empty($this->data['Checkup']['date_until']) ) {
            $this->layout = 'printhtml';
            Configure::write('debug', 0);
            
            $date_from = $this->data['Checkup']['date_from']['year'] . '-' .
                         $this->data['Checkup']['date_from']['month'] . '-' .
                         $this->data['Checkup']['date_from']['day'];
            $date_until = $this->data['Checkup']['date_until']['year'] . '-' .
                         $this->data['Checkup']['date_until']['month'] . '-' .
                         $this->data['Checkup']['date_until']['day'];
            $conditions = array();
            $date = date_format(date_create($date_from), 'd/m/Y');
            
            if ( $date_from == $date_until ) {
                $conditions['Checkup.checkup_date'] = $date_from;
            } else {
                $conditions['Checkup.checkup_date >='] = $date_from;
                $conditions['Checkup.checkup_date <='] = $date_until;
                $date .= ' s/d ' . date_format(date_create($date_until), 'd/m/Y');
            }
            $this->set('date', $date);
            
            $this->Checkup->Behaviors->attach('Containable');
            $checkups = $this->Checkup->find('all', array(
                'condition' => $conditions,
                'contain' => array(
                    'Patient' => array(
                        'fields' => array('name', 'code')
                    ),
                    'Handler',
                    'CheckupsMedicine' => array(
                        'fields' => array('qty'),
                        'Medicine'
                    ),
                    'Checktype', 'Diagnosis'
                )
            ));
            $handler_types = $this->Checkup->Handler->HandlerType->find('list');
            $records = array();
            $no = 1;
            foreach ($checkups as $checkup) {
                $records[$no] = array(
                    'date' => $checkup['Checkup']['checkup_date'],
                    'patient_code' => $checkup['Patient']['code'],
                    'patient_name' => $checkup['Patient']['name'],
                    'patient_work' => '',
                    'checktypes' => '',
                    'diagnoses' => '',
                    'medicines' => '',
                    'handler' =>  ($checkup['Handler']['handler_type_id'] ? 
                                        $handler_types[$checkup['Handler']['handler_type_id']] : '') . ' ' . 
                                  $checkup['Handler']['name']
                );
                if ( !empty($checkup['Checktype']) ) {
                    // check types
                    $records[$no]['checktypes'] = '<ul>';
                    foreach ($checkup['Checktype'] as $checktype) {
                        $records[$no]['checktypes'] .= '<li>' . $checktype['name'] . '</li>';
                    }
                    $records[$no]['checktypes'] .= '</ul>';
                    
                    // diagnoses
                    $records[$no]['diagnoses'] = '<ul>';
                    foreach ($checkup['Diagnosis'] as $diagnosis) {
                        $records[$no]['diagnoses'] .= '<li>' . $diagnosis['name'] . '</li>';
                    }
                    $records[$no]['diagnoses'] .= '</ul>';
                    
                    // medicines
                    $records[$no]['medicines'] = '<ul>';
                    foreach ($checkup['CheckupsMedicine'] as $medicine) {
                        $records[$no]['medicines'] .= '<li>' .$medicine['Medicine']['name'] .
                            ' &rarr; ' . $medicine['qty'] . '</li>';
                    }
                    $records[$no]['medicines'] .= '</ul>';
                }
                
                $no++;
            }
            $this->set('records', $records);
        } else {
            $show_form = true;
        }
        
        $this->set('show_form', $show_form);
    }
    
    function report_patient_status() {
        $show_form = false;
        if ( !empty($this->data['Checkup']['patient_id']) ) {
            $this->layout = 'printhtml';
            Configure::write('debug', 0);
        
        } else {
            $show_form = true;
            $this->set('patients', $this->Checkup->Patient->find('list'));
        }
        
        $this->set('show_form', $show_form);
    }
}
?>
