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
        $this->set('types', $this->Patient->PatientType->find('list'));
        $this->set('users', $this->Patient->User->find('list', array(
            'order' => 'name'
        )));
    }
}
?>
