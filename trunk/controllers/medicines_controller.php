<?php
class MedicinesController extends AppController {
    var $pageTitle = 'Obat-obatan';
    
    function add() {
        $this->__setAdditionals();
        parent::add();
    }
    
    function edit($id) {
        $this->__setAdditionals();
        parent::edit($id);
    }
    
    function __setAdditionals() {
        $this->set('units', $this->Medicine->Unit->find('list'));
    }
}
?>
