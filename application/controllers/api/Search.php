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

    private function _search_user($keyword, $limit, $offset){
        $result = $this->mongo_db->where(array(
            'status' => 1,
            '$or' => array(
                        array(
                            'displayName' => array(
                                '$regex' => $keyword,
                                '$options' => '$im'
                            )
                        ),
                        array(
                            'firstName' => array(
                                '$regex' => $keyword,
                                '$options' => '$im'
                            )
                        ),
                        array(
                            'lastName' => array(
                                '$regex' => $keyword,
                                '$options' => '$im'
                            )
                        ),
                        array(
                            'phones.number' => array(
                                '$regex' => $keyword,
                                '$options' => '$im'
                            )
                        )
            )
        ))->limit($limit)->offset($offset)->get(TABLE_PROFILE);

        return $result;
    }

    private function _search_product ($keyword, $limit, $offset){
        $result = $this->mongo_db->where_in(
            'status.status', array(ACCEPTED, AVAILABLE)
        )->where(
            array(
                'name' => array(
                    '$regex' => $keyword,
                    '$options' => '$im'
                )
            )
        )->limit($limit)->offset($offset)->get(TABLE_PRODUCT);

        return $result;
    }

    //TODO: implement after create bidroom is implemented
    private function _search_bidroom ($keyword, $limit, $offset){

    }

    private function _render_search_result($result, $flag){
        $tmp = $result;
        $res['flag'] = $flag;
        $res['result'] = $tmp;
        return $res;
    }
    
    public function index_post(){

        $params = array(
            'keyword' => $this->post('keyword'),
            'searchType' => $this->post('searchType')
        );

        //check profile exist
        $this->_check_profile_exist($this->post('accessKey'));

        //check require params
        $this->_require_parameter($params);

        $keyword = $this->post('keyword');
        $searchType = (int)($this->post('searchType'));
        $filter = $this->post('filter');
        $limit = $this->post('limit');
        $offset = $this->post('offset');

        if($filter == null){
            if($searchType == 0){
                $result = $this->_search_user($keyword, $limit, $offset);
            } else if($searchType == 1){
                $result = $this->_search_product($keyword, $limit, $offset);
            } else if($searchType == 2){
                $result = $this->_search_bidroom($keyword, $limit, $offset);
            }
        }

        if(empty($result)){
            //TODO: change message error to message success
            $this->response(msg_error('Result not found!!!'));
        }else {
            $res = count($result) > 10 ? $this->_render_search_result($result, 1) : $this->_render_search_result($result, 0);
            $this->response(msg_success($res));
        }
    }
    

}



