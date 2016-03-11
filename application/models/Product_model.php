<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */

class Product_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('ValidationField_model','validateField');
    }

    public function get_all_products(){
        try {
            $product = $this->mongo_db->get(TABLE_PRODUCT);
            return msg_success($product);
        } catch (Exception $e) {
            return msg_exception(e.getMessage());
        }
    }
    
    public function add_product($product){
        try {
            $product = $this->validateField->product($product);
            $productId = $this->mongo_db->insert(TABLE_PRODUCT, $product);
            $output = $this->mongo_db->where(array('_id' => new MongoId($productId)))->
                              get(TABLE_PRODUCT);
            return msg_success($output[0]);
        } catch (Exception $e) {
            return msg_exception(e.getMessage());
        }
       
    }
   
   public function read_available_product(){
       //$field = $status['status'];       
//       return $this->mongo_db->where_match_element('status'->'status', array('product_status' => 4))->get(TABLE_PRODUCT);
      
   }
   
   public function update_product($updateProduct){
      return $this->mongo_db->where(array('_id' => new MongoId($updateProduct['id'])))->update(TABLE_PRODUCT,array('name' => $updateProduct['name'], 'condition' => $updateProduct['condition']));
   }
   
   public function delete_product($productId){
       return $this->mongo_db->where(array('_id' => new MongoId($productId)))->update(TABLE_PRODUCT, array('isDelete' => TRUE));
   }
   
}

