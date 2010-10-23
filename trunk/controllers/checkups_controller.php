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
        
        $this->Checkup->Behaviors->attach('Containable');
        $checkups = $this->Checkup->find('all', array(
            'contain' => array(
                'Patient' => array(
                    'fields' => array('id'),
                    'PatientType' => array(
                        'fields' => array('PatientType.name')
                    )
                )
            )
        ));
        $types = $this->Checkup->Patient->PatientType->find('list', array(
            'fields' => array('name', 'id')
        ));
        foreach ($types as $key => $val) {
            $types[$key] = 0;
        }
        $records = array();
        $total_patients = 0;
        foreach ($checkups as $checkup) {
            if ( isset($checkup['Patient']['PatientType']['name']) && !empty($checkup['Patient']['PatientType']['name']) ) {
                if ( !isset($records[$checkup['Patient']['PatientType']['name']]) ) {
                    $records[$checkup['Patient']['PatientType']['name']] = 1;
                } else {
                    $records[$checkup['Patient']['PatientType']['name']]++;
                }
                $total_patients++;
            }
        }

        $total_patients = $total_patients;
        $this->set('records', array_merge($types, $records));
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
                        'Medicine' => array(
                            'Unit'
                        )
                    ),
                    'Checktype', 'Diagnosis'
                ),
                'order' => 'Checkup.checkup_date ASC'
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
                            ' &rarr; ' . $medicine['qty'] . 
                            (isset($medicine['Medicine']['Unit']['name']) ? ' ' . $medicine['Medicine']['Unit']['name'] : '') .
                            '</li>';
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
                'conditions' => $conditions,
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
            Configure::write('debug', 0);
            $checkup_date = $this->data['Checkup']['periode']['year'] . '-' .
                            $this->data['Checkup']['periode']['month'] . '-' .
                            $this->data['Checkup']['periode']['day'];
            $periode = $checkup_date;
            $this->set('checkup_date', $checkup_date);
            
            $medicine_id = $this->data['Checkup']['medicine_id'];
            $this->Checkup->CheckupsMedicine->Medicine->Behaviors->attach('Containable');
            $medicine = $this->Checkup->CheckupsMedicine->Medicine->find('first', array(
                'conditions' => array(
                    'Medicine.id' => $medicine_id
                ),
                'contain' => array('Unit')
            ));
            $this->set('medicine', $medicine);
            
            $medicine_ins = $this->__getStockInExt( $medicine_id, $checkup_date);
            $penerimaan = 0;
            $medicines = array();
            $counter = 0;
            foreach ( $medicine_ins as $key => $medicine_in ) {
                $medicines[$counter]['date_in'] = $medicine_in['MedicineIn']['date_in'];
                $penerimaan += $medicine_in[0]['total'];
                $medicines[$counter]['penerimaan_periode'] = $medicine_in[0]['total']*1;
                
                if ( !isset($medicines[$counter]['penerimaan_total']) ) {
                    $medicines[$counter]['penerimaan_total']  = $penerimaan;
                } else {
                    $medicines[$counter]['penerimaan_total'] += $penerimaan;
                }
                
                if ( !isset($stock) ) {
                    $stock  = $medicines[$counter]['penerimaan_periode'];
                } else {
                    $stock += $medicines[$counter]['penerimaan_periode'];
                }
                $medicines[$counter]['pengeluaran'] = 0;
                $medicines[$counter]['pengebon'] = '';
                $medicines[$counter]['stock'] = $stock;
                
                // get medicine out on current date_in
                // to the next record date_in, if exists.
                // If not exists, compare last date_in
                // with passed period
                if ( isset($medicine_ins[$counter+1]) ) {
                    $medicine_outs = $this->__getStockOutExt($medicine_id, $medicine_in['MedicineIn']['date_in'], $medicine_ins[$key+1]['MedicineIn']['date_in']);
                    $counter++;
                    foreach ( $medicine_outs as $k2 => $medicine_out ) {
                        $medicines[$counter]['date_in'] = $medicine_out['Checkup']['checkup_date'];
                        $medicines[$counter]['pengeluaran'] = $medicine_out['Checkup']['total_qty']*1;
                        $medicines[$counter]['penerimaan_periode'] = '';
                        $medicines[$counter]['pengebon'] = $medicine_out['Patient']['name'];
                        
                        $stock -= $medicine_out['Checkup']['total_qty'];
                        $medicines[$counter]['stock'] = $stock;
                        $counter++;
                    }
                } else if ( $medicine_in['MedicineIn']['date_in'] != $periode ) {
                    // if this is the last record
                    // compare date_in with passed period
                    if ( isset($medicine_ins[$key+1]['MedicineIn']['date_in']) ) {
                        $medicine_outs = $this->__getStockOutExt($medicine_id, $medicine_in['MedicineIn']['date_in'], $medicine_ins[$key+1]['MedicineIn']['date_in']);
                    } else {
                        $medicine_outs = $this->__getStockOutExt($medicine_id, $medicine_in['MedicineIn']['date_in'], $periode);
                    }
                    
                    $counter++;
                    foreach ( $medicine_outs as $k3 => $medicine_out2 ) {
                        $medicines[$counter]['date_in'] = $medicine_out2['Checkup']['checkup_date'];
                        $medicines[$counter]['pengeluaran'] = $medicine_out2['Checkup']['total_qty']*1;
                        $medicines[$counter]['penerimaan_periode'] = '';
                        $medicines[$counter]['pengebon'] = $medicine_out2['Patient']['name'];
                        
                        $stock -= $medicine_out2['Checkup']['total_qty'];
                        $medicines[$counter]['stock'] = $stock;
                        $counter++;
                    }
                    
                } else {
                    $counter++;
                }
            }
            $this->set('medicines', $medicines);
            
        } else {
            $show_form = true;
            $this->set('medicines', $this->Checkup->CheckupsMedicine->Medicine->find('list'));
        }
        
        $this->set('show_form', $show_form);
    }
    
    function report_stock() {
        $this->layout = 'printhtml';
        Configure::write('debug', 0);
        
        $medicines = array();
        $_medicines = $this->Checkup->CheckupsMedicine->Medicine->find('all', array(
            'order' => 'Medicine.name ASC'
        ));
        // ordered key by id
        foreach ($_medicines as $key => $medicine) {
            $medicine['Medicine']['pengeluaran'] = 0;
            $medicine['Medicine']['penerimaan']  = 0;
            $medicines[$medicine['Medicine']['id']] = $medicine;
        }
        $terlaris = array(
            'item' => array(),
            'total' => 0
        );
        $minimum = array(
            'item' => array(),
            'total' => 0
        );
        $this->Checkup->Behaviors->attach('Containable');
        $checkups = $this->Checkup->find('all', array(
            'fields' => array('id'),
            'contain' => array(
                'CheckupsMedicine'
            ),
            'order' => 'Checkup.checkup_date ASC, Checkup.created ASC'
        ));
        foreach ($checkups as $checkup) {
            if ( !empty($checkup['CheckupsMedicine']) ) {
                foreach ( $checkup['CheckupsMedicine'] as $q ) {
                    $medicines[$q['medicine_id']]['Medicine']['pengeluaran'] += $q['qty'];
                }
            }
        }
        // get medicine ins
        $this->Checkup->CheckupsMedicine->Medicine->bindModel(array(
            'hasMany' => array('MedicineIn')
        ));
        $fields = array(
            'MedicineIn.date_in',
            'MedicineIn.medicine_id',
            'SUM(MedicineIn.total) as total'
        );
        $medicine_ins = $this->Checkup->CheckupsMedicine->Medicine->MedicineIn->find('all', array(
            'fields' => $fields,
            'contain' => array(),
            'group' => 'MedicineIn.medicine_id'
        ));
        foreach ($medicine_ins as $medicine_in) {
            $medicines[$medicine_in['MedicineIn']['medicine_id']]['Medicine']['penerimaan'] += $medicine_in[0]['total'];
        }
        
        foreach ($medicines as $key => $medicine) {
            $medicines[$key]['Medicine']['stok'] = $medicines[$key]['Medicine']['penerimaan'] - $medicines[$key]['Medicine']['pengeluaran'];
            if ( is_null( $minimum['total'] )  ) {
                $minimum['total'] = $medicines[$key]['Medicine']['stok'];
            }
            
            if ( $medicines[$key]['Medicine']['pengeluaran'] > $terlaris['total'] ) {
                $terlaris['total'] = $medicines[$key]['Medicine']['pengeluaran'];
                $terlaris['item'] = $medicine;
            }
            
            if ( $medicines[$key]['Medicine']['stok'] < $minimum['total'] ) {
                $minimum['total'] = $medicines[$key]['Medicine']['stok'];
                $minimum['item'] = $medicine;
            }
        }
        
        $this->set('medicines', $medicines);
        $this->set('terlaris', $terlaris);
        $this->set('minimum', $minimum);
    }
    
    function view_zero_stocks() {
        $medicines = array();
        $_medicines = $this->Checkup->CheckupsMedicine->Medicine->find('all', array(
            'order' => 'Medicine.name ASC'
        ));
        // ordered key by id
        foreach ($_medicines as $key => $medicine) {
            $medicine['Medicine']['pengeluaran'] = 0;
            $medicine['Medicine']['penerimaan']  = 0;
            $medicines[$medicine['Medicine']['id']] = $medicine;
        }
        $this->Checkup->Behaviors->attach('Containable');
        $checkups = $this->Checkup->find('all', array(
            'fields' => array('id'),
            'contain' => array(
                'CheckupsMedicine'
            ),
            'order' => 'Checkup.checkup_date ASC, Checkup.created ASC'
        ));
        foreach ($checkups as $checkup) {
            if ( !empty($checkup['CheckupsMedicine']) ) {
                foreach ( $checkup['CheckupsMedicine'] as $q ) {
                    $medicines[$q['medicine_id']]['Medicine']['pengeluaran'] += $q['qty'];
                }
            }
        }
        // get medicine ins
        $this->Checkup->CheckupsMedicine->Medicine->bindModel(array(
            'hasMany' => array('MedicineIn')
        ));
        $fields = array(
            'MedicineIn.date_in',
            'MedicineIn.medicine_id',
            'SUM(MedicineIn.total) as total'
        );
        $medicine_ins = $this->Checkup->CheckupsMedicine->Medicine->MedicineIn->find('all', array(
            'fields' => $fields,
            'contain' => array(),
            'group' => 'MedicineIn.medicine_id'
        ));
        foreach ($medicine_ins as $medicine_in) {
            $medicines[$medicine_in['MedicineIn']['medicine_id']]['Medicine']['penerimaan'] += $medicine_in[0]['total'];
        }
        
        foreach ($medicines as $key => $medicine) {
            $medicines[$key]['Medicine']['stok'] = $medicines[$key]['Medicine']['penerimaan'] - $medicines[$key]['Medicine']['pengeluaran'];
            unset( $medicines[$key]['Medicine']['penerimaan'] );
            unset( $medicines[$key]['Medicine']['pengeluaran'] );
            
            if ( $medicines[$key]['Medicine']['stok'] > 0 ) {
                unset($medicines[$key]);
            }
            
        }
        
        $this->pageTitle = 'Obat yang harus dibeli';
        
        $this->set('records', $medicines);
    }
    
    function __getStockInExt($medicine_id, $periode = null) {
        $this->Checkup->CheckupsMedicine->Medicine->bindModel(array(
            'hasMany' => array('MedicineIn')
        ));
        return $this->Checkup->CheckupsMedicine->Medicine->MedicineIn->getTotalExt($medicine_id, $periode);
    }
    
    function __getStockOutExt($medicine_id, $date_from = null, $date_to = null) {
        $conditions = array();
        
        if ( isset($date_from) && !empty($date_from) ) {
            $conditions['Checkup.checkup_date >='] = $date_from;
        }
        if ( isset($date_to) && !empty($date_to) ) {
            $conditions['Checkup.checkup_date <'] = $date_to;
        }
        $this->Checkup->Behaviors->attach('Containable');
        $checkups = $this->Checkup->find('all', array(
            'fields' => null,
            'conditions' => $conditions,
            'contain' => array(
                'CheckupsMedicine' => array(
                    'fields' => array('qty'),
                    'conditions' => array(
                        'CheckupsMedicine.medicine_id' => $medicine_id
                    )
                ),
                'Patient' => array(
                    'fields' => array('name')
                )
            ),
            'order' => 'Checkup.checkup_date ASC, Checkup.created ASC'
        ));
        
        foreach ($checkups as $key => $checkup) {
            if ( !empty($checkup['CheckupsMedicine']) ) {
                $checkups[$key]['Checkup']['total_qty'] = 0;
                foreach ( $checkup['CheckupsMedicine'] as $q ) {
                    $checkups[$key]['Checkup']['total_qty'] += $q['qty'];
                }
            } else { // unset empty medicines on checkup
                unset($checkups[$key]);
            }
        }
        
        return $checkups;
    }
}
?>
