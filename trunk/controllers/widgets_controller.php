<?php
class WidgetsController extends AppController {
    var $uses = null;
    var $pageTitle = '';
    
    public function summary_medicines() {
        // turn off debugging and uses ajax layout
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        
        App::import('Model', array('Checkup'));
        $this->Checkup = new Checkup();
        
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
        
        $total_items = count($medicines);
        $zero_stocks = $available_stocks = 0;
        
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
            
            if ( $medicines[$key]['Medicine']['stok'] <= 0 ) {
                $zero_stocks++;
            } else {
                $available_stocks++;
            }
        }
        
        $this->set('total_items', $total_items);
        $this->set('zero_stocks', $zero_stocks);
        $this->set('available_stocks', $available_stocks);
    }
}
?>
