<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @15/3/2016
 */

class BidRoom_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('ValidationField_model','validateField');
    }

    public function get_all_products(){
        try {
            $product = $this->mongo_db->get(TABLE_PRODUCT);
            return msg_success($product);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

    public function create_bidroom($data)
    {
        try {
            $data = $this->validateField->bidroom($data);
            var_dump($data);die;
            $id = $this->mongo_db->insert(TABLE_BIDROOM, $data);
            $output = $this->mongo_db->where(array('_id' => new MongoId($id)))
                          ->get(TABLE_BIDROOM);
            // var_dump($output);die;
            return msg_success($output[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

}