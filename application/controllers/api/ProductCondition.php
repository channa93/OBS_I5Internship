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

class ProductCondition extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('ProductCondition_model', 'condition');
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
        $all_products = $this->mongo_db->get(TABLE_CONDITION);
        foreach($all_products as $obj){
            if($obj['title'] === $this->post('title')){
                $this->response(msg_error('This title already exist', $this->post('title')));
            }
        }
    }

    private function _check_id_exist(){
        $all_products = $this->mongo_db->get(TABLE_CONDITION);
        foreach($all_products as $obj){
            if($obj['_id'] === (int)($this->post('conditionId'))){
                return (int)($this->post('conditionId'));
            }
        }
    }

    private function _update_params($params, $id){
        $info = $this->condition->get_product_condition_by_id($id);
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
    
    public function add_product_condition_post(){
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
        
        $response = $this->condition->add_production_condition($input);
        $this->response($response);
        
    }
    
    public function get_all_products_condition_post(){

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->condition->get_all_products_condition();

        $this->response($response);
    }
    
    public function update_product_condition_post(){
        $params = array(
            'conditionId' => $this->post('conditionId')
        );
        
        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));
        
        //check require params
        $this->_require_parameter($params);

        //check id exist
        $conditionId = $this->_check_id_exist();
        if(empty($conditionId)){
            $this->response(msg_error('This id does not exist', $this->post('$conditionId')));
        }

        //check title has existed or not
        $this->_check_title_exist();

        $input['title'] = $this->post('title');
        $input['description'] = $this->post('description');

        //update field that is empty or null
        $update_condition = $this->_update_params($input,$conditionId);
        
        //check length of params
        if(!check_charactor_length($update_condition['title'],TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_condition['title'], 'title'));
        }
        if(!check_charactor_length($update_condition['description'], DESC_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_condition['description'], 'description'));
        }

        //update product condition
        $response = $this->condition->update_product_condition($conditionId,$update_condition);

        $this->response($response);
        
    }
    
    public function delete_product_condition_post(){

        $params = array(
            'conditionId' => $this->post('conditionId')
        );

        $this->_require_parameter($params);

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check id exist
        $conditionId = $this->_check_id_exist();
        if(empty($conditionId)){
            $this->response(msg_error('This id does not exist', $this->post('$conditionId')));
        }

        $response = $this->condition->delete_product_condition($conditionId);

        $this->response($response);
    }
    
    
    
    
    
    
}


