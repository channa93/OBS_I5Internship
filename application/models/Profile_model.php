<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('General_model','general');
        $this->load->model('ValidationField_model','vilidateField');
    } 

    public function get_profile_users(){
        return $this->mongo_db->get(TABLE_PROFILE);
    }
    public function get_profile_user($id){
        return $this->mongo_db->
                    where(array('_id' => new MongoId($id)))->
                    get(TABLE_PROFILE);
    }
    
    
    public function login($data){
        //var_dump($data);die;
        //var_dump($params['socialAccount']);die;
           
        $get_profile = $this->general->get_profile_by_social_id($data['socialId'], $data['socialType']);
       // var_dump($get_profile);die;
        if(!empty($get_profile)){
            return $get_profile;  // register all ready
        }else{
            return false;
        }    
    }
    
    public function add_user($params){
        $format_data = $this->vilidateField->profile($params);
        $user_id = new MongoId();
        $format_data['_id'] = $user_id;  
        $format_data['accessKey'] = generate_access_key($user_id->{'$id'}, $format_data['createdDate']);
         //var_dump($format_data);die;

        try {
             $user_id = $this->mongo_db->insert(TABLE_PROFILE,$format_data);
             $user = $this->get_profile_user($user_id);
             return $user;
             
        } catch (Exception $e) {
            return e.getMessage();
        }      
    }
   

}
