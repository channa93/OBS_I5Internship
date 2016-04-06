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
        $this->load->model('Product_model','product');
        $this->load->model('Profile_model','profile');
    }

    public function get_all_bidrooms_of_users(){
        try {
            $bidrooms = $this->mongo_db
                        ->order_by(array('createdDate' => 'DESC'))
                        ->where(array('isDelete' => false))
                        ->where_in('status', array(PENDING, OPEN))
                        ->get(TABLE_BIDROOM);
            foreach ($bidrooms as $key => $value) {
                $bidrooms[$key]['bidroomId'] =  $bidrooms[$key]['_id']->{'$id'};
                unset($bidrooms[$key]['_id']);
            }
            return msg_success($bidrooms);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

    public function create_bidroom($data)
    {
        
        try {
            $data = $this->validateField->bidroom($data);
            $id = $this->mongo_db->insert(TABLE_BIDROOM, $data);
            $output = $this->mongo_db->where(array('_id' => new MongoId($id)))
                          ->get(TABLE_BIDROOM);
            $output[0]['bidroomId'] =  $output[0]['_id']->{'$id'};
            unset($output[0]['_id']);
            // UPDATE product status
            $productId = $this->mongo_db->where(array('_id' => new MongoId($data['productId'])))
                            ->set(array('status.status' => AVAILABLE))
                            ->update(TABLE_PRODUCT);
                // for future get all data if client needs
            // $userInfo = $this->profile->get_profile_user_by_id($data['ownerId']);
            // $productInfo = $this->product->get_product_by_id($data['productId']);
            // $output[0]['productInfo'] = $productInfo['data'];
            // $output[0]['userInfo'] = $userInfo['data'];
            return msg_success($output[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }
    public function get_bidroom_by_id($bidroomId)
    {
        try {
            $bidroomId = new MongoId($bidroomId);
            $bidroom = $this->mongo_db->where(array('_id' => $bidroomId))
                                ->get(TABLE_BIDROOM);
            if(empty($bidroom)) return msg_error('bidroom , id='.$bidroomId." does not exist");
            $bidroom[0]['bidroomId'] =  $bidroom[0]['_id']->{'$id'};
            unset($bidroom[0]['_id']);
            return msg_success($bidroom[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
        
    }

    public function edit_bidroom($updateData, $bidroomId)
    {
        try {
            $status =  $this->mongo_db->where(array('_id' => new MongoId($bidroomId)))->
                        set($updateData)->update(TABLE_BIDROOM);
            $bidroom = $this->get_bidroom_by_id($bidroomId);
            return $bidroom;
        } catch (Exception $e) {
              return msg_exception($e->getMessage()); 
        } 
    }

    public function check_bidroom_owner($bidroomId, $userId)
    {
        try {
            $bidroom = $this->get_bidroom_by_id($bidroomId);
            $ownerId = $bidroom['data']['ownerId'];
            //var_dump($ownerId, $userId);die;
            if($ownerId === $userId) {
                return true;
            }
            else return false;
            
        } catch (Exception $e) {
              return msg_exception($e->getMessage()); 
        } 
    }

    public function delete_bidroom($bidroomId)
    {
        try {
           $bidroom = $this->get_bidroom_by_id($bidroomId);
           if($bidroom['code']==1){
                $this->mongo_db->where(array('_id'=>new MongoId($bidroomId)))->delete(TABLE_BIDROOM);
                return msg_success('');

           }else{
                return msg_error('bidroom does not exist');
           }
        } catch (Exception $e) {
            return msg_exception($e->getMessage()); 
        }
    }


    public function get_bidroom_by_product_id($productId)
    {
        try {
            $bidroom = $this->mongo_db->where(array('productId' => $productId))
                                ->get(TABLE_BIDROOM);
            if(empty($bidroom)) return msg_error('no bidroom yet for this productId='+$productId);
            $bidroom[0]['bidroomId'] =  $bidroom[0]['_id']->{'$id'};
            unset($bidroom[0]['_id']);
            return msg_success($bidroom[0]);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
        
    }

    // get all my bidrooms (all status of bidrooms)
    public function get_all_my_bidrooms($userId)
    {
        try {
            $bidrooms = $this->mongo_db
                            ->order_by(array('createdDate' => 'DESC'))
                            ->where(array('isDelete' => false, 'ownerId' => $userId))
                            ->where_in('status', array(PENDING, OPEN, CLOSE))
                            ->get(TABLE_BIDROOM);
            foreach ($bidrooms as $key => $value) {
                $bidrooms[$key]['bidroomId'] =  $bidrooms[$key]['_id']->{'$id'};
                unset($bidrooms[$key]['_id']);
            }
            return msg_success($bidrooms);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
        
    }

    

}