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

class Search extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('Profile_model', 'profile');
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
//
//    private function _check_title_exist(){
//        $all_currencies = $this->mongo_db->get(TABLE_CURRENCY);
//        foreach($all_currencies as $obj){
//            if($obj['title'] === $this->post('title')){
//                $this->response(msg_error('This title already exist', $this->post('title')));
//            }
//        }
//    }
//
//    private function _check_id_exist(){
//        $all_currencies = $this->mongo_db->get(TABLE_CURRENCY);
//        foreach($all_currencies as $obj){
//            if($obj['_id'] === (int)($this->post('currencyId'))){
//                return (int)($this->post('currencyId'));
//            }
//        }
//    }
    
    public function search_user_post(){

        $params = array(
            'filter' => $this->post('filter'),
            'limit' => $this->post('limit'),
            'offset' => $this->post('offset')
        );

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check require params
        $this->_require_parameter($params);
        
        //do the search
        $result = $this->mongo_db->where(array(
            'status' => 1,
            '$or' => array(
                        array(
                            'userName' => array(
                                '$regex' => $this->post('filter'),
                                '$options' => '$im'
                            )
                        ),
                        array(
                            'firstName' => array(
                                '$regex' => $this->post('filter'),
                                '$options' => '$im'
                            )
                        ),
                        array(
                            'phones' => array(
                                '$in' => array(
                                    (int)($this->post('filter'))
                                    )
                                )
                            )
                        )

        ))->limit($this->post('limit'))->offset($this->post('offset'))->get(TABLE_PROFILE);

        if(empty($result)){
            $this->response(msg_error('Result not found!!!'));
        }else {
            $this->response(msg_success($result));
        }
    }
    

}



