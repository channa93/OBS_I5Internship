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

class Currency extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('Currency_model', 'currency');
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

    private function _check_title_exist(){
        $all_currencies = $this->mongo_db->get(TABLE_CURRENCY);
        foreach($all_currencies as $obj){
            if($obj['title'] === $this->post('title')){
                $this->response(msg_error('This title already exist', $this->post('title')));
            }
        }
    }

    private function _check_id_exist(){
        $all_currencies = $this->mongo_db->get(TABLE_CURRENCY);
        foreach($all_currencies as $obj){
            if($obj['_id'] === (int)($this->post('currencyId'))){
                return (int)($this->post('currencyId'));
            }
        }
    }

    private function _update_params($params, $id){
        $info = $this->currency->get_currency_by_id($id);
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
    
    public function add_currency_post(){
        $params = array(
            'title' => $this->post('title'),
            'description' => $this->post('description')
        );

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check required parameter
        $this->_require_parameter($params);

        //check title has existed or not
        $this->_check_title_exist();

        //check length of params
        if(!check_charactor_length($this->post('title'),TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('title'), 'title'));
        }
        if(!check_charactor_length($this->post('description'), DESC_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('description'), 'description'));
        }

        $input['title'] = $this->post('title');
        $input['description'] = $this->post('description');
        
        $response = $this->currency->add_currency($input);
        $this->response($response);
        
    }
    
    public function get_all_currencies_post(){

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->currency->get_all_currencies();

        $this->response($response);
    }
    
    public function update_currency_post(){
        $params = array(
            'currencyId' => $this->post('currencyId')
        );
        
        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));
        
        //check require params
        $this->_require_parameter($params);

        //check id exist
        $currencyId = $this->_check_id_exist();
        if(empty($currencyId)){
            $this->response(msg_error('This id does not exist', $this->post('currencyId')));
        }

        //check title has existed or not
        $this->_check_title_exist();

        $input['title'] = $this->post('title');
        $input['description'] = $this->post('description');

        //update field that is empty or null
        $update_currency = $this->_update_params($input,$currencyId);

        //check length of params
        if(!check_charactor_length($update_currency['title'],TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_currency['title'], 'title'));
        }
        if(!check_charactor_length($update_currency['description'], DESC_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_currency['description'], 'description'));
        }

        //update currency
        $response = $this->currency->update_currency($currencyId,$update_currency);

        $this->response($response);
        
    }
    
    public function delete_currency_post(){

        $params = array(
            'currencyId' => $this->post('currencyId')
        );

        $this->_require_parameter($params);

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check id exist
        $currencyId = $this->_check_id_exist();
        if(empty($currencyId)){
            $this->response(msg_error('This id does not exist', $this->post('currencyId')));
        }

        $response = $this->currency->delete_currency($currencyId);

        $this->response($response);
    }
    
    
    
    
    
    
}



