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
            $products = $this->mongo_db->get(TABLE_PRODUCT);
            foreach ($products as $key => $value) {
                $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
                unset($products[$key]['_id']);
            }
            return msg_success($products);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }
    public function get_product_by_id($id){
        try {
            $product = $this->mongo_db->where(array('_id' => new MongoId($id)))->get(TABLE_PRODUCT);
            $product[0]['productId'] = $product[0]['_id']->{'$id'};
            unset($product[0]['_id']);
            return msg_success($product[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

    
    public function add_product($product){
        try {
            $product = $this->validateField->product($product);
            $productId = $this->mongo_db->insert(TABLE_PRODUCT, $product);
            $output = $this->mongo_db->where(array('_id' => new MongoId($productId)))->
                              get(TABLE_PRODUCT);
            $output[0]['productId'] = $output[0]['_id']->{'$id'};
            unset($output[0]['_id']);
            return msg_success($output[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
       
    }
   
   public function get_available_products(){
      try {
          $products = $this->mongo_db->where(array(
                              'isDelete'=>false,
                              'status.status'=> new MongoInt32(1))
                            )  
                            ->get(TABLE_PRODUCT);
          foreach ($products as $key => $value) {
              $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
              unset($products[$key]['_id']);
          }
          return msg_success($products);
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }

   public function add_images_product($productId, $imageGallery){
      try {
          $status =  $this->mongo_db->where(array('_id' => new MongoId($productId)))->
                            set(array('imageGallery' => $imageGallery))->update(TABLE_PRODUCT);
          $product = $this->get_product_by_id($productId);
          return $product;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }
  
   public function edit_product($updateData, $productId){
      
      try {
          $status =  $this->mongo_db->where(array('_id' => new MongoId($productId)))->
                        set($updateData)->update(TABLE_PRODUCT);
          $product = $this->get_product_by_id($productId);
          return $product;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   } 

   public function delete_product($input){
      $accessKey = $input['accessKey'];
      $productId = $input['productId'];
      try {
          $status =  $this->mongo_db->where(array('_id' => new MongoId($productId)))->
                        set('isDelete',true)->update(TABLE_PRODUCT);
          // $product = $this->get_product_by_id($productId);
          return $status;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }
   public function get_user_products($userId){
      $ownerId = $userId;
      try {
          $products =  $this->mongo_db->select(array('_id','name','imageGallery'))
                        ->where(array('ownerId' => $ownerId , 'isDelete'=>false))
                        ->get(TABLE_PRODUCT);
          foreach ($products as $key => $value) {
              $products[$key]['productId'] = $value['_id']->{'$id'};
              unset($products[$key]['_id']); 
          }
          return $products;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }


   
   
}

