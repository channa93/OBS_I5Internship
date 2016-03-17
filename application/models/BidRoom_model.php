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

    public function get_all_bidrooms(){
        try {
            $bidrooms = $this->mongo_db->get(TABLE_BIDROOM);
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

}