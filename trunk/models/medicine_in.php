<?php
class MedicineIn extends AppModel {
    var $validate = array(
        'medicine_id' => array(
            'rule' => array('vMedicine'),
            'message' => 'Obat kosong'
        ),
        'total' => array(
            'required' => true,
            'allowEmpty' => false,
            'rule' => 'numeric',
            'message' => 'This field cannot be left blank and must be numeric'
        ),
        'date_in' => array(
            'required' => true,
            'allowEmpty' => false,
            'rule' => 'date',
            'message' => 'This field cannot be left blank and must be date'
        )
    );

    var $belongsTo = array(
        'Medicine',
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'fields' => array('id', 'name')
        )
    );
    var $cacheQueries = true;
    
    function afterFind($results) {
        if ( isset($results[0]['Medicine']) && !empty($results[0]['Medicine']) ) {
            // get list of item's unit
            // TODO: caching this one
            $units = $this->Medicine->Unit->find('list');
            
            foreach ( $results as $key => $result ) {
                $results[$key]['Medicine']['unit_name'] = $units[$result['Medicine']['unit_id']];
            }
        }
        
        return $results;
    }
    
    function vMedicine($field) {
        $exist = $this->Medicine->find('count', array(
            'conditions' => array(
                'Medicine.id' => $field["medicine_id"]
            ),
            'recursive' => -1)
        );
        return $exist > 0;
    }
    
    function getTotal($medicine_id, $periode = null) {
        $conditions = array(
            'MedicineIn.medicine_id' => $medicine_id
        );
        $fields = array('MedicineIn.total', 'MedicineIn.created');
        
        if ( !is_null($periode) ) {
            $conditions['MedicineIn.date_in <='] = $periode;
            $fields[] = 'MedicineIn.date_in';
        }
        
        $totals = $this->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
            'contain' => array(),
            'order' => 'MedicineIn.created ASC'
        ));
        
        if ( !is_null($periode) ) {
            return $totals;
        }
        
        $result = 0;
        foreach ($totals as $total) {
            $result += $total['MedicineIn']['total'];
        }
        return $result;
    }
    
    function getTotalExt($medicine_id, $periode) {
        $conditions = array(
            'MedicineIn.medicine_id' => $medicine_id,
            'MedicineIn.date_in <=' => $periode
        );
        $fields = array(
            'MedicineIn.created',
            'MedicineIn.date_in',
            'SUM(MedicineIn.total) as total'
        );
        
        return $this->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
            'contain' => array(),
            'group' => 'MedicineIn.date_in',
            'order' => 'MedicineIn.date_in ASC'
        ));
    }
}
?>
