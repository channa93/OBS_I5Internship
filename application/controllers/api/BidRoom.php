<?php
defined('BASEPATH') or exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @15/03-/2016
 */

class BidRoom extends REST_Controller{

    function __construct(){
        parent:: __construct();
        $this->load->model('Product_model', 'product');
        $this->load->model('BidRoom_model', 'bidroom');
        $this->load->model('Profile_model', 'profile');
    }
    /**
     * used to check if the input passed the required param or not
     * @param  $input : associatave array
     * @return message of missing param if required param is not pass
     */
    private function _require_parameter($input) {
        $checked_param = require_parameter($input);
        if ($checked_param !== TRUE) {
            $this->response(msg_missingParameter($checked_param));
        }
    }
    
    public function index_get(){
        $products = $this->bidroom->get_all_bidrooms();
        $this->response($products);
    }

    public function create_bidroom_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId'),
            'startDate' => $this->post('startDate'),
            'endDate' => $this->post('endDate'),
            'startupPrice' => $this->post('startupPrice'),
            'ownerId' => $this->post('ownerId'),
            'title' => $this->post('title')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            unset($input['accessKey']);
            $bidroom = $this->bidroom->create_bidroom($input);
            $this->response($bidroom);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }
}