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
        
        $_medicines = $this->Checkup->CheckupsMedicine->Medicine->find('all');
        $medicines = array();
        $units = array();
        foreach ($_medicines as $medicine) {
            $medicines[$medicine['Medicine']['id']] = $medicine['Medicine']['name'];
            $units[$medicine['Medicine']['id']] = $medicine['Unit']['name'];
        }
        
        $this->set('medicines', $medicines);
        $this->set('units', $units);
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
            
            $conditions = array(
                'patient_id' => $this->data['Checkup']['patient_id']
            );
            $this->Checkup->Behaviors->attach('Containable');
            $checkups = $this->Checkup->find('all', array(
                'condition' => $conditions,
                'contain' => array(
                    'Patient' => array(
                        'fields' => array('name', 'code'),
                        'PatientType', 'User'
                    ),
                    'Handler',
                    'CheckupsMedicine' => array(
                        'fields' => array('qty'),
                        'Medicine' => array('Unit')
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
                    'patient_work' => $checkup['Patient']['PatientType']['name'],
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
                }
                
                if ( !empty($checkup['Diagnosis']) ) {
                    // diagnoses
                    $records[$no]['diagnoses'] = '<ul>';
                    foreach ($checkup['Diagnosis'] as $diagnosis) {
                        $records[$no]['diagnoses'] .= '<li>' . $diagnosis['name'] . '</li>';
                    }
                    $records[$no]['diagnoses'] .= '</ul>';
                }
                
                if ( !empty($checkup['CheckupsMedicine']) ) {
                    // medicines
                    $records[$no]['medicines'] = '<ul>';
                    foreach ($checkup['CheckupsMedicine'] as $medicine) {
                        $records[$no]['medicines'] .= '<li>' .$medicine['Medicine']['name'] .
                            ' &rarr; ' . $medicine['qty'] . ' ' . $medicine['Medicine']['Unit']['name'] . '</li>';
                    }
                    $records[$no]['medicines'] .= '</ul>';
                }
                
                /**
                 * Medical data
                 */
                $records[$no]['medic_data'] = '<table class="noborder">';
                if ( $checkup['Checkup']['anamnesis'] ) {
                    $records[$no]['medic_data'] .= '<tr>';
                    $records[$no]['medic_data'] .= '<td><strong>Anamnesis</strong></td>';
                    $records[$no]['medic_data'] .= '<td>' . $checkup['Checkup']['anamnesis'] . '</td>';
                    $records[$no]['medic_data'] .= '</tr>';
                }
                if ( $checkup['Checkup']['physical_check'] ) {
                    $records[$no]['medic_data'] .= '<tr>';
                    $records[$no]['medic_data'] .= '<td><strong>Pemeriksaan fisik</strong></td>';
                    $records[$no]['medic_data'] .= '<td>' . $checkup['Checkup']['physical_check'] . '</td>';
                    $records[$no]['medic_data'] .= '</tr>';
                }
                
                if ( $checkup['Checkup']['glucose_check'] || $checkup['Checkup']['uric_acid_check'] ||
                     $checkup['Checkup']['cholesterol_check'] )
                {
                    $records[$no]['medic_data'] .= '<tr>';
                    $records[$no]['medic_data'] .= '<td><strong>Pemeriksaan darah</strong></td>';
                    $records[$no]['medic_data'] .= '<td>';
                    $records[$no]['medic_data'] .= '<ul>';
                    if ( $checkup['Checkup']['glucose_check'] ) {
                        $records[$no]['medic_data'] .= '<li>Glukosa:&nbsp;<strong>' . $checkup['Checkup']['glucose_check'] . '</strong>&nbsp;mg/dl</li>';
                    }
                    if ( $checkup['Checkup']['uric_acid_check'] ) {
                        $records[$no]['medic_data'] .= '<li>Asam&nbsp;urat:&nbsp;<strong>' . $checkup['Checkup']['uric_acid_check'] . '</strong>&nbsp;mg/dl</li>';
                    }
                    if ( $checkup['Checkup']['cholesterol_check'] ) {
                        $records[$no]['medic_data'] .= '<li>Kolesterol:&nbsp;<strong>' . $checkup['Checkup']['cholesterol_check'] . '</strong>&nbsp;mg/dl</li>';
                    }
                    $records[$no]['medic_data'] .= '</ul>';
                    $records[$no]['medic_data'] .= '</td>';
                    $records[$no]['medic_data'] .= '</tr>';
                } else {
                    $records[$no]['medic_data'] .= '<tr><td colspan="2">&nbsp;</td></tr>';
                }
                $records[$no]['medic_data'] .= '</table>';
                
                $no++;
            }
            $this->set('records', $records);
        } else {
            $show_form = true;
            $this->set('patients', $this->Checkup->Patient->find('list'));
        }
        
        $this->set('show_form', $show_form);
    }
    
    function report_stock_medicines() {
        $show_form = false;
        if ( !empty($this->data['Checkup']['medicine_id']) && !empty($this->data['Checkup']['periode']) ) {
            $this->layout = 'printhtml';
            // Configure::write('debug', 0);
            $checkup_date = $this->data['Checkup']['periode']['year'] . '-' .
                            $this->data['Checkup']['periode']['month'] . '-' .
                            $this->data['Checkup']['periode']['day'];
            $this->set('checkup_date', $checkup_date);
            
            $conditions = array(
                'CheckupsMedicine.medicine_id' => $this->data['Checkup']['medicine_id'],
                'Checkup.checkup_date' => $checkup_date
            );
            $this->Checkup->Behaviors->attach('Containable');
            $checkups = $this->Checkup->find('all', array(
                'fields' => null,
                'condition' => $conditions,
                'contain' => array(
                    'CheckupsMedicine' => array(
                        'fields' => array('qty'),
                        'conditions' => array(
                            'CheckupsMedicine.medicine_id' => $this->data['Checkup']['medicine_id']
                        )
                    ),
                    'Patient' => array(
                        'fields' => array('name')
                    )
                )
            ));
            
            $this->Checkup->CheckupsMedicine->Medicine->Behaviors->attach('Containable');
            $medicine = $this->Checkup->CheckupsMedicine->Medicine->find('first', array(
                'conditions' => array(
                    'Medicine.id' => $this->data['Checkup']['medicine_id']
                ),
                'contain' => array('Unit')
            ));
            $this->set('medicine', $medicine);
            
            $medicines = array();
            $counter = 0;
            foreach ( $checkups as $key => $checkup ) {
                if ( isset($checkup['CheckupsMedicine']) && !empty($checkup['CheckupsMedicine']) ) {
                    $medicines[$cnt]['pengeluaran'] = $checkup['CheckupsMedicine'][0]['qty'];
                    $medicines[$cnt]['pengebon'] = $checkup['Patient']['name'];
                
                    $medicines[$counter]['date_in'] = $item_in['ItemIn']['date_in'];
                    $penerimaan += $item_in[0]['total'];
                    $medicines[$counter]['penerimaan_periode'] = $item_in[0]['total']*1;
                    
                    if (!isset($medicines[$counter]['penerimaan_total']) ) {
                        $medicines[$counter]['penerimaan_total'] = $penerimaan;
                    } else {
                        $medicines[$counter]['penerimaan_total'] += $penerimaan;
                    }
                    
                    if ( !isset($stock) ) {
                        $stock = $medicines[$counter]['penerimaan_periode'];
                    } else {
                        $stock += $medicines[$counter]['penerimaan_periode'];
                    }
                    $medicines[$counter]['pengeluaran'] = 0;
                    $medicines[$counter]['pengebon'] = '';
                    $medicines[$counter]['stock'] = $stock;
                    
                    // get item out on current date_in
                    // to the next record date_in, if exists.
                    // If not exists, compare last date_in
                    // with passed period
                    if ( isset($item_ins[$counter+1]) ) {
                        $item_outs = $this->__getStockOutExt($item['Item']['id'], $item_in['ItemIn']['date_in'], $item_ins[$key+1]['ItemIn']['date_in']);
                        
                        $counter++;
                        foreach ( $item_outs as $k2 => $item_out ) {
                            $medicines[$counter]['date_in'] = $item_out['ItemOut']['date_approved'];
                            $medicines[$counter]['pengeluaran'] = $item_out['ItemOut']['total_approved']*1;
                            // $medicines[$counter]['penerimaan_periode'] = $stock;
                            $medicines[$counter]['penerimaan_periode'] = '';
                            $medicines[$counter]['pengebon'] = $item_out['User']['name'];
                            
                            $stock -= $item_out['ItemOut']['total_approved'];
                            $medicines[$counter]['stock'] = $stock;
                            $counter++;
                        }
                        
                    } else if ( $item_in['ItemIn']['date_in'] != $periode ) {
                        // if this is the last record
                        // compare date_in with passed period
                        if ( isset($item_ins[$key+1]['ItemIn']['date_in']) ) {
                            $item_outs = $this->__getStockOutExt($item['Item']['id'], $item_in['ItemIn']['date_in'], $item_ins[$key+1]['ItemIn']['date_in']);
                        } else {
                            $item_outs = $this->__getStockOutExt($item['Item']['id'], $item_in['ItemIn']['date_in'], $periode);
                        }
                        
                        $counter++;
                        foreach ( $item_outs as $k3 => $item_out2 ) {
                            $medicines[$counter]['date_in'] = $item_out2['ItemOut']['date_approved'];
                            $medicines[$counter]['pengeluaran'] = $item_out2['ItemOut']['total_approved']*1;
                            // $medicines[$counter]['penerimaan_periode'] = $stock;
                            $medicines[$counter]['penerimaan_periode'] = '';
                            $medicines[$counter]['pengebon'] = $item_out2['User']['name'];
                            
                            $stock -= $item_out2['ItemOut']['total_approved'];
                            $medicines[$counter]['stock'] = $stock;
                            $counter++;
                        }
                        
                    } else {
                        $counter++;
                    }
                
                }
            }
            $this->set('medicines', $medicines);
            
        } else {
            $show_form = true;
            $this->set('medicines', $this->Checkup->CheckupsMedicine->Medicine->find('list'));
        }
        
        $this->set('show_form', $show_form);
    }
}
?>
