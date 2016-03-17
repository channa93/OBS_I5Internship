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

    private function _update_params($params, $id){
        $info = $this->account->get_account_type_by_id($id);
        $output = array();
        foreach ($params as $key => $val){
            if(empty($val) || $val == null){
                $output[$key] = $info[0][$key];
            }else {
                $output[$key] = $val;
            }
        }
        $output['modifiedDate'] = date('Y-m-d H:m:s A');
        return $output;
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

        //check type has existed or not
        $this->_check_type_exist();

        //check length of params
        if(!check_charactor_length($this->post('type'),TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('type'), 'type'));
        }

        $input['type'] = $this->post('type');
        $input['priceCharge'] = (double)($this->post('priceCharge'));
        $input['features'] = json_decode($this->post('features'));

        $response = $this->account->add_account_type($input);
        $this->response($response);

    }

    public function get_all_accounts_type_post(){

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->account->get_all_accounts_type();

        $this->response($response);
    }

    public function update_account_type_post(){
        $params = array(
            'accountId' => $this->post('accountId')
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

        //check type has existed or not
        $this->_check_type_exist();

        $input['type'] = $this->post('type');
        $input['priceCharge'] = (double)($this->post('priceCharge'));
        $input['features'] = json_decode($this->post('features'));

        //update field that is empty or null
        $update_account_type = $this->_update_params($input,$accountId);

        //check length of params
        if(!check_charactor_length($update_account_type['type'],TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_account_type['type'], 'type'));
        }

        //update account
        $response = $this->account->update_account_type($accountId,$update_account_type);

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
