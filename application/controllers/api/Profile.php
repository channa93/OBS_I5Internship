<?php
defined('BASEPATH') OR exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

class Profile extends REST_Controller{

    function __construct(){
            parent::__construct();
            $this->load->model('Profile_model','profile');

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
        // $data = array('a','b','c');
         $data =  $this->profile->get_profile_users();
         $this->response($data);
    }
    
    public function login_post(){
        $params = array(
            'socialId' => (string) $this->post('socialId'),
            'socialType' => (int) $this->post('socialType')
        );

        $this->_require_parameter($params);  
        $input['firstName'] = $this->post('firstName');
        $input['lastName'] = $this->post('lastName');
        $input['userName'] = $input['firstName']." ".$input['lastName'];
        $input['avatar'] = $this->post('avatar');    
        $input['socialAccount'][] = $params; // array of associative array = array of object

        $data = $this->profile->login($params);
        if($data){
            $data['message'] = "** Welcome back, ".$input['userName'];
            $this->response(msg_success($data));
        }else{
            $user = $this->add_user($input);
            $this->response($user);
        }    
    }

    public function add_user($params) {        
        $user = $this->profile->add_user($params);
        $user[0]['userId'] = $user[0]['_id']->{'$id'};
        unset($user[0]['_id']);
        $user[0]['message'] = "** Welcome new user! ";

        $data = msg_success($user); 
        return $this->response($data);
    }
    
    
}