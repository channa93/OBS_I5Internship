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

class Category extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('Category_model', 'category');
    }
    
    private function _require_parameter($input){
        $checked_param = require_parameter($input);
        if($checked_param !== TRUE){
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
        $all_categories = $this->mongo_db->get(TABLE_CATEGORY);
        foreach($all_categories as $obj){
            if($obj['title'] === $this->post('title')){
                $this->response(msg_error('This title already exist', $this->post('title')));
            }
        }
    }

    private function _update_params($params, $id){
        $info = $this->category->get_category_by_id($id);
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

    private function _check_id_exist(){
        $all_categories = $this->mongo_db->get(TABLE_CATEGORY);
        foreach($all_categories as $obj){
            if($obj['_id'] === (int)($this->post('categoryId'))){
                return (int)($this->post('categoryId'));
            }
        }
    }
    
    public function add_category_post(){
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
        
        $response = $this->category->add_category($input);
        $this->response($response);
        
    }
    
    public function get_all_categories_post(){

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        $response = $this->category->get_all_categories();

        $this->response($response);
    }
    
    public function update_category_post(){
        $params = array(
            'categoryId' => $this->post('categoryId')
        );
        
        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));
        
        //check require params
        $this->_require_parameter($params);

        //check id exist
        $categoryId = $this->_check_id_exist();
        if(empty($categoryId)){
            $this->response(msg_error('This id does not exist', $this->post('categoryId')));
        }

        //check title has existed or not
        $this->_check_title_exist();

        $input['title'] = $this->post('title');
        $input['description'] = $this->post('description');

        //update field that is empty or null
        $update_category = $this->_update_params($input,$categoryId);

        //check length of params
        if(!check_charactor_length($update_category['title'],TITLE_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_category['title'], 'title'));
        }
        if(!check_charactor_length($update_category['description'], DESC_LENGTH_LIMITED)){
            $this->response(invalid_charactor_length($update_category['description'], 'description'));
        }

        //update category
        $response = $this->category->update_category($categoryId,$update_category);

        $this->response($response);
        
    }
    
    public function delete_category_post(){

        $params = array(
            'categoryId' => $this->post('categoryId')
        );

        $this->_require_parameter($params);

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check id exist
        $categoryId = $this->_check_id_exist();
        if(empty($categoryId)){
            $this->response(msg_error('This id does not exist', $this->post('categoryId')));
        }

        $response = $this->category->delete_category($categoryId);

        $this->response($response);

    }

    // added by channa as requested by iOS) : 3/17/16 
    
    public function get_categories_by_ids_post(){ // get cateogries by array of ids

        $params = array(
            'categoryIds' => $this->post('categoryIds')
        );

        $this->_require_parameter($params);

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));
  
        $categoryIds = json_decode($params['categoryIds'], true);
        $response = $this->category->get_categories_by_ids($categoryIds);
        if(empty($response['data'])){
            $this->response(msg_error('category  does not exist'));
        }
        $this->response($response);

    }
    
    
    
    
}



