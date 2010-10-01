<?php
class MedicineInsController extends AppController {
    var $pageTitle = 'Input Obat';
    
    function add() {
        $this->__setAdditionals();
        parent::add();
    }
    
    function edit($id = null) {
        $this->__setAdditionals();
        parent::edit($id);
    }
    
    function getSatuan($medicine_id = null) {
        $this->layout = 'ajax';
        Configure::write('debug', 0);
        $result = $this->MedicineIn->Medicine->find('first', array(
            'conditions' => array(
                'Medicine.id' => $medicine_id
            ),
            'fields' => array('Unit.name')
        ));
        
        $this->set('result', $result);
    }
    
    function __setAdditionals() {
        $medicines = $this->MedicineIn->Medicine->find('list', array(
            'order' => array('Medicine.name ASC')
        ));
        $this->set('medicines', $medicines);
        
        $this->set('getSatuan', "var getSatuan = '" . $this->webroot . $this->params['controller'] . "/getSatuan/';");
    }
}
?>
