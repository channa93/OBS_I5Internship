<?php
defined('BASEPATH') or exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class AccountType extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('AccountType_model', 'account');
    }
    
    private function _require_parameter($input){
        $checked_param = require_parameter($input);
        if($checked_param !== TRUE ){
            $this->response(msg_missingParameter($checked_param));
        }
    }

    private function _check_profile_exist($accessKey, $show_error=1) {
        $is_exist_profile = authenticate($accessKey);
        if ($is_exist_profile === FALSE && $show_error) {
            $this->response(msg_invalidAccessKey());
        } else {
            unset($is_exist_profile['_id']);
            return $is_exist_profile;
        }
    }

    private function _check_type_exist(){
        $all_accounts = $this->mongo_db->get(TABLE_ACCOUNT);
        foreach($all_accounts as $obj){
            if($obj['type'] === $this->post('type')){
                $this->response(msg_error('This type already exist', $this->post('type')));
            }
        }
    }

    private function _check_id_exist(){
        $all_accounts = $this->mongo_db->get(TABLE_ACCOUNT);
        foreach($all_accounts as $obj){
            if($obj['_id'] === (int)($this->post('accountId'))){
                return (int)($this->post('accountId'));
            }
        }
    }
    
    public function add_account_type_post(){
        $params = array(
            'type' => $this->post('type'),
            'priceCharge' => $this->post('priceCharge'),
            'features' => $this->post('features')
        );

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check required parameter
        $this->_require_parameter($params);

        //check title has existed or not
        $this->_check_type_exist();

        //check length of params
        if(!check_charactor_length($this->post('type'),TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('type'), 'type'));
        }

        $input['type'] = $this->post('type');
        $input['priceCharge'] = (double)($this->post('priceCharge'));
        $input['features'] = $this->post('features');
        
        $response = $this->account->add_account_type($input);
        $this->response($response);
        
    }
    
    public function get_all_accounts_type_post(){

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->account->get_all_account_type();

        $this->response($response);
    }
    
    public function update_account_type_post(){
        $params = array(
            'accountId' => $this->post('accountId'),
            'type' => $this->post('type'),
            'priceCharge' => $this->post('priceCharge'),
            'features' => $this->post('features')
        );
        
        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));
        
        //check require params
        $this->_require_parameter($params);

        //check id exist
        $accountId = $this->_check_id_exist();
        if(empty($accountId)){
            $this->response(msg_error('This id does not exist', $this->post('accountId')));
        }

        //check title has existed or not
        $this->_check_type_exist();
        
        //check length of params
        if(!check_charactor_length($this->post('type'),TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('type'), 'type'));
        }

        $input['type'] = $this->post('type');
        $input['priceCharge'] = (double)($this->post('priceCharge'));
        $input['features'] = $this->post('features');

        //update currency
        $response = $this->account->update_account_type($accountId,$input);

        $this->response($response);
        
    }
    
    public function delete_account_type_post(){

        $params = array(
            'accountId' => $this->post('accountId')
        );

        $this->_require_parameter($params);

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check id exist
        $accountId = $this->_check_id_exist();
        if(empty($accountId)){
            $this->response(msg_error('This id does not exist', $this->post('$accountId')));
        }

        $response = $this->account->delete_account_type($accountId);

        $this->response($response);
    }

}



