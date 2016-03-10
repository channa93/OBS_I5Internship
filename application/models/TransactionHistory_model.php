<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @9/3/2016
 */

class TransactionHistory_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('ValidationField_model','vilidateField');

    }
    
    public function add_transaction_history($data){
        
        $data = $this->vilidateField->transaction_history($data);
        $id = $this->mongo_db->insert(TABLE_TRANSACTION_HISTORY,$data);

        // var_dump($data,$id);die;

    }
   
}

