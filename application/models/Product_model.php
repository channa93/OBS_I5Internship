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
        $this->load->model('Profile_model','profile');
    }

    public function get_all_products(){
        try {
            $products = $this->mongo_db->order_by(array('createdDate' => 'DESC'))->get(TABLE_PRODUCT);
            foreach ($products as $key => $value) {
                $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
                $products[$key]['totalLikes'] =  count($products[$key]['likerId']);
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
            $product[0]['totalLikes'] =  count($product[0]['likerId']);

            return msg_success($product[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

    
    public function add_product($product){
        try {
            $product = $this->validateField->product($product);
            $productId = $this->mongo_db->insert(TABLE_PRODUCT, $product);
            $output = $this->get_product_by_id($productId);
            return msg_success($output['data']);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
       
    }
   
   public function get_available_products(){
      try {
          $products = $this->mongo_db
                          ->order_by(array('createdDate' => 'DESC'))
                          ->where(array('isDelete' => false))
                          ->where_in('status.status', array(ACCEPTED, AVAILABLE))
                          ->get(TABLE_PRODUCT);
          foreach ($products as $key => $value) {
              $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
              $products[$key]['totalLikes'] =  count($products[$key]['likerId']);
              $ownerInfo =  $this->profile->get_profile_user_by_id($products[$key]['ownerId']);
              $products[$key]['ownerInfo'] = $ownerInfo['data'];
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
          $products =  $this->mongo_db //->select(array('_id','name','imageGallery','likerId'))
                        ->order_by(array('createdDate' => 'DESC'))
                        ->where(array('ownerId' => $ownerId , 'isDelete'=>false))
                        ->get(TABLE_PRODUCT);
          foreach ($products as $key => $value) {
              $products[$key]['productId'] = $value['_id']->{'$id'};
              $products[$key]['totalLikes'] =  count($value['likerId']);
              unset($products[$key]['_id']); 
          }
          return $products;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }

   // get all my products with any products status
   public function get_all_my_products($userId){
      $ownerId = $userId;
      try {
          $products =  $this->mongo_db 
                        ->order_by(array('createdDate' => 'DESC'))
                        ->where(array('ownerId' => $ownerId , 'isDelete'=>false))
                        ->get(TABLE_PRODUCT);
          foreach ($products as $key => $value) {
              $products[$key]['productId'] = $value['_id']->{'$id'};
              $products[$key]['totalLikes'] =  count($value['likerId']);
              unset($products[$key]['_id']); 
          }
          return $products;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }

   // get other user products by his id with available status product only
   public function get_other_user_products_by_user_id($otherUserId){
      $ownerId = $otherUserId;
      try {
          $products =  $this->mongo_db 
                        ->order_by(array('createdDate' => 'DESC'))
                        ->where(array('ownerId' => $ownerId , 'isDelete'=>false)) 
                        ->where_in('status.status', array(ACCEPTED, AVAILABLE))
                        ->get(TABLE_PRODUCT);
          foreach ($products as $key => $value) {
              $products[$key]['productId'] = $value['_id']->{'$id'};
              $products[$key]['totalLikes'] =  count($value['likerId']);
              unset($products[$key]['_id']); 
          }
          return $products;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }



   public function count_view_product($productId)
   {
      try {
          // find current number of view
          $product = $this->get_product_by_id($productId);
          $curentNumView = $product['data']['viewCount'];
          
          // increment viewCount on product
          $updateData = array(
            'viewCount' => $curentNumView+1
          );  
          $product = $this->edit_product($updateData, $productId);
          return $product;
      } catch (Exception $e) {
          return msg_exception($e->getMessage()); 
      }
   }

   // get most popular product based on viewCount
   public function get_popular_products()
   {
        try {
            $products = $this->mongo_db
                            ->order_by(array('createdDate' => 'DESC'))
                            ->where(array('isDelete' => false))
                            ->where_in('status.status', array(ACCEPTED, AVAILABLE))
                            ->order_by(array('viewCount' => 'DESC'))
                            ->limit(LIMIT_MAX_POPULAR_PRODUCT)
                            ->get(TABLE_PRODUCT);
            // filter productId and count total likes
            foreach ($products as $key => $value) {
                $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
                $products[$key]['totalLikes'] =  count($products[$key]['likerId']);
                $ownerInfo =  $this->profile->get_profile_user_by_id($products[$key]['ownerId']);
                $products[$key]['ownerInfo'] = $ownerInfo['data'];
                unset($products[$key]['_id']);
            }

            return msg_success($products);
        } catch (Exception $e) {
            return msg_exception($e->getMessage()); 

        }
   } 

   // get all new available products in this month
   public function get_new_products_this_month()
   {
        $firstDay = date('Y-m-01');
        $lastDay = date('Y-m-t');
        try {
            $products = $this->mongo_db
                            ->order_by(array('createdDate' => 'DESC'))
                            ->where(array('isDelete' => false))
                            ->where_in('status.status', array(ACCEPTED, AVAILABLE))
                            ->where_between('createdDate', $firstDay, $lastDay)
                            ->limit(LIMIT_MAX_POPULAR_PRODUCT)
                            ->get(TABLE_PRODUCT);
            
            // filter productId and count total likes
            foreach ($products as $key => $value) {
                $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
                $products[$key]['totalLikes'] =  count($products[$key]['likerId']);          
                $ownerInfo =  $this->profile->get_profile_user_by_id($products[$key]['ownerId']);
                $products[$key]['ownerInfo'] = $ownerInfo['data'];

                unset($products[$key]['_id']);
            }
            
            return msg_success($products);
        } catch (Exception $e) {
            return msg_exception($e->getMessage()); 
        }
   }


   // get all all recommenned product => new this month and most popular 
   public function get_recommened_products()
   {
        $firstDay = date('Y-m-01');
        $lastDay = date('Y-m-t');
        try {
            $products = $this->mongo_db
                            ->where(array('isDelete' => false))
                            ->where_in('status.status', array(ACCEPTED, AVAILABLE))
                            ->order_by(array('createdDate' => 'DESC', 'viewCount'  => 'DESC'))
                            ->where_between('createdDate', $firstDay, $lastDay)
                            ->limit(LIMIT_MAX_POPULAR_PRODUCT)
                            ->get(TABLE_PRODUCT);
            
            // filter productId and count total likes
            foreach ($products as $key => $value) {
                $products[$key]['productId'] =  $products[$key]['_id']->{'$id'};
                $products[$key]['totalLikes'] =  count($products[$key]['likerId']);            
                $ownerInfo =  $this->profile->get_profile_user_by_id($products[$key]['ownerId']);
                $products[$key]['ownerInfo'] = $ownerInfo['data'];
                
                unset($products[$key]['_id']);
            }
            
            return msg_success($products);
        } catch (Exception $e) {
            return msg_exception($e->getMessage()); 
        }
   }   
}

