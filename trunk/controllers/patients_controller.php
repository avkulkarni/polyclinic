<?php
class PatientsController extends AppController {
    var $pageTitle = 'Pasien';
    
    function add() {
        parent::add();
        $this->__setAdditionals();
    }
    
    function edit($id) {
        parent::edit($id);
        $this->__setAdditionals();
    }
    
    function __setAdditionals() {
        $types = $this->Patient->PatientType->find('list');
        $this->set('types', $types);
    }
}
?>
