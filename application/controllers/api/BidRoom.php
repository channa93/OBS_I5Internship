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

    public function get_all_bidrooms_of_users_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $bidrooms = $this->bidroom->get_all_bidrooms_of_users();
            $this->response($bidrooms);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function create_bidroom_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId'),
            'startDate' => $this->post('startDate'),
            'endDate' => $this->post('endDate'),
            'startupPrice' => (double)$this->post('startupPrice'),
            'currencyType' => (int)$this->post('currencyType'),
            'ownerId' => $this->post('ownerId'),
            'title' => $this->post('title')
        );

        // check require param and validate date, startupPrice
        $this->_require_parameter($input);
        $this->_check_date_price($input['startDate'], $input['endDate'], $input['startupPrice']); 
  
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

     // check if start date and date is valide
    private function _check_date_price($startDate, $endDate, $startupPrice)
    {
        $today = date(DATE_FORMAT);
        if($today > $startDate) $this->response(msg_error('start date must be in the present'));
        if($startDate > $endDate) $this->response(msg_error('end date must be earlier than start date'));
        if($startupPrice < 0) $this->response(msg_error('startupPrice must be positive'));
    }

    public function get_bidroom_by_id_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'bidroomId' => $this->post('bidroomId')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $bidroom = $this->bidroom->get_bidroom_by_id($input['bidroomId']);
            $this->response($bidroom);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function edit_bidroom_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'bidroomId' => $this->post('bidroomId')
        );
        $this->_require_parameter($input);
        $input['title'] = $this->post('title');
        $input['startupPrice'] = (double)$this->post('startupPrice');
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $bidroomId = $input['bidroomId'];
            $isOwner = $this->bidroom->check_bidroom_owner($bidroomId, $profile['userId']);
            if($isOwner){
                $updateData = filter_param_update($input);
                unset($updateData['accessKey'], $updateData['bidroomId']);
                $bidroom = $this->bidroom->edit_bidroom($updateData, $bidroomId);
                $this->response($bidroom);
            }else{
                $this->response(msg_error('this bidroom not belong to you'));
            }
                  
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function delete_bidroom_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'bidroomId' => $this->post('bidroomId')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $isOwner = $this->bidroom->check_bidroom_owner($input['bidroomId'], $profile['userId']);
            // var_dump($isOwner,$profile, $input['bidroomId']);die;
            if($isOwner){
                $response = $this->bidroom->delete_bidroom($input['bidroomId']);
                $this->response($response);

            }else{
                $this->response(msg_error('this bidroom not belong to you'));
            }
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function get_bidroom_by_product_id_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $bidroom = $this->bidroom->get_bidroom_by_product_id($input['productId']);
            $this->response($bidroom);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function get_all_my_bidrooms_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $bidrooms = $this->bidroom->get_all_my_bidrooms($profile['userId']);
            $this->response($bidrooms);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }
}