<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */
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

    public function get_profile_user_by_accessKey($accessKey){

        try {
             $user =  $this->mongo_db->
                         where(array('accessKey'=> $accessKey))->
                         get(TABLE_PROFILE);
            if(empty($user)) return false;            
            return $user[0];        
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    } 

    public function get_profile_user_by_id($id){
        try {
             $user =  $this->mongo_db->
                         where(array('_id'=> new MongoId($id)))->
                         get(TABLE_PROFILE);
            if(empty($user)) return false;            
            return $user[0];
             
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }

    
    
    public function login($data){     
        $get_profile = $this->general->get_profile_by_social_id($data['socialId'], $data['socialType']);
        if(!empty($get_profile)){    
            return $get_profile;  // register already
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
             $user = $this->get_profile_user_by_id($user_id->{'$id'});
             return $user;
             
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }      
    }

    public function edit_profile($data){

        // foreach($_FILES as $image){
        //     var_dump($image);
        // }
        
        try{
            $id = $this->mongo_db->where(array('accessKey'=>$data['accessKey']))
                         ->set($data)->update(TABLE_PROFILE);
            if($id){
                $user =  $this->get_profile_user_by_accessKey($data['accessKey']);
                $user['userId'] =   $user['_id']->{'$id'} ;
                unset($user['_id']);
                return $user;
            }
            
        }catch (Exception $e){
            return msg_exception($e->getMessage());
            //return $this->response(msg_error(e.getMessage()));

        }
        
        
    }

    public function add_interest_category($input){
        $categoryId = $input['categoryId'];
        $accessKey = $input['accessKey'];
        try{
            $categoryId = new MongoInt32($categoryId);
            $success = $this->mongo_db->where(array('accessKey' => $accessKey)) ->
                        push(array('interestCategoryId' => $categoryId)) ->
                        update(TABLE_PROFILE);
            return $success;
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }

    public function delete_interest_category($input){
        $categoryId = $input['categoryId'];
        $accessKey = $input['accessKey'];
        try{
            $categoryId = new MongoInt32($categoryId);
            $success = $this->mongo_db->where(array('accessKey' => $accessKey)) ->
                        pull('interestCategoryId' , $categoryId) ->
                        update(TABLE_PROFILE);
            return $success;
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }

    public function get_interest_category($accessKey){
        try{
            $data = $this->mongo_db->
                            where(array('accessKey' => $accessKey)) ->
                            select(array('interestCategoryId','_id'))->
                            get(TABLE_PROFILE);
            $data[0]['userId'] = $data[0]['_id']->{'$id'};
            unset($data[0]['_id']);
            return $data[0];
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }

    public function get_category_by_id($categoryId){
        try{
            $data = $this->mongo_db->
                            where(array('_id' => $categoryId)) ->
                            select(array('title','_id'))->
                            get(TABLE_CATEGORY);
            //$data[0]['userId'] = $data[0]['_id']->{'$id'};
            //unset($data[0]['_id']);
            return $data[0];
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }


    public function add_money($profile, $sandboxMoney){
        try{
            $money_add = $profile['wallet'] + $sandboxMoney;
            $data = $this->mongo_db->
                            where(array('accessKey' => $profile['accessKey']))->
                            set('wallet', $money_add)->
                            update(TABLE_PROFILE);
            $user = $this->get_profile_user_by_accessKey($profile['accessKey']);
            return $user;
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }     
    }

    public function add_subscriber($input){
        $subscriberId = $input['subscriberId'];
        $accessKey = $input['accessKey'];
        try{
            $success = $this->mongo_db->where(array('accessKey' => $accessKey)) ->
                        push(array('subscriber' => $subscriberId)) ->
                        update(TABLE_PROFILE);
            return $success;
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }

    public function delete_subscriber($input){
        $subscriberId = $input['subscriberId'];
        $accessKey = $input['accessKey'];
        try{
            $success = $this->mongo_db->where(array('accessKey' => $accessKey)) ->
                        pull('subscriber' , $subscriberId) ->
                        update(TABLE_PROFILE);
            return $success;
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }

    public function upgrade_account($input)
    {  
        $toAccId = $input['accountType'];
        $accessKey = $input['accessKey'];
        $remainMoney = $input['remainMoney']; 
        try{
            $id = $this->mongo_db->where(array('accessKey'=>$accessKey))
                         ->set(array(
                                'accountType' => new MongoInt32($toAccId),
                                'wallet' => $remainMoney
                                ))
                         ->update(TABLE_PROFILE);
            if($id){
                $user =  $this->get_profile_user_by_accessKey($accessKey);
                $user['userId'] =   $user['_id']->{'$id'} ;
                unset($user['_id']);
                return msg_success($user);
            }
            return false;
        }catch (Exception $e){
            return msg_exception($e->getMessage());
            //return $this->response(msg_error(e.getMessage()));

        }
    }


  
}
