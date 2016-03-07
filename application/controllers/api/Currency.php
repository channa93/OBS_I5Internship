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
        if(!$checked_param){
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
    
    public function add_currency_post(){
        $params = array(
            'accessKey' => $this->post('accessKey'),
            'title' => $this->post('title'),
            'description' => $this->post('description')
        );

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check required parameter
        $this->_require_parameter($params);
        
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

        //TODO: check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->currency->get_all_currencies();

        $this->response($response);
    }
    
    public function update_currency_post(){
        $params = array(
            'title' => $this->post('title'),
            'description' => $this->post('description')
        );
        
        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));
        
        //check require params
        $this->_require_parameter($params);
        
        //check length of params
        if(!check_charactor_length($this->post('title'),TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('title'), 'title'));
        }
        if(!check_charactor_length($this->post('description'), DESC_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($this->post('description'), 'description'));
        }

        //update currency
        $response = $this->currency->update_currency($this->post('_id'),$params);

        $this->response($response);
        
    }
    
    public function delete_currency_post(){
        
        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->currency->delete_currency($this->post('_id'));

        $this->response($response);
        
        
    }
    
    
    
    
    
    
}



