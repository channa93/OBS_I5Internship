<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class Product_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    
    public function create_product($product){
        return $this->mongo_db->insert(TABLE_PRODUCT, $product);
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

