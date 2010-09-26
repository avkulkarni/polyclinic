<?php
class HandlersController extends AppController {
    var $pageTitle = 'Pemeriksa';
    
    function add() {
        $this->__setAdditionals();
        parent::add();
    }
    
    function edit($id) {
        $this->__setAdditionals();
        parent::edit($id);
    }
    
    function __setAdditionals() {
        $this->set('types', $this->Handler->HandlerType->find('list'));
    }
}
?>
