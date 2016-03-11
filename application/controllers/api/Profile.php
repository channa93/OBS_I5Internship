<?php
defined('BASEPATH') OR exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */
class Profile extends REST_Controller{

    function __construct(){
            parent::__construct();
            $this->load->model('Profile_model','profile');
            $this->load->model('General_model','general');
            $this->load->model('TransactionHistory_model','transaction_history');
            date_default_timezone_set("Asia/Bangkok");
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
         $data =  $this->profile->get_profile_users();
         $this->response(msg_success($data));
    }
    
        /*****Login/Regitser*/

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
            $data['message'] = "** Welcome back, ".$data['firstName']." ".$data['lastName']."!";
            $this->response(msg_success($data));
        }else{
            $user = $this->add_user($input);
            $this->response(msg_success($user));
        }    
    }

        /***** Add user */

    public function add_user($params) {        
        $user = $this->profile->add_user($params);
        $user['userId'] = $user['_id']->{'$id'};
        unset($user['_id']);
        $user['message'] = "** Welcome new user! ";
        $data = msg_success($user); 
        return $this->response($data);
    }

        /*****Edit profile*/

    public function edit_profile_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);

        // check if that profile is exist with accessKey     
        $accessKey = $this->post('accessKey');
        $profile = $this->profile->get_profile_user_by_accessKey($accessKey);
         
        if($profile){
            $data = $profile;
            $input['firstName'] = $this->post('firstName');
            $input['lastName'] = $this->post('lastName');
            $input['userName'] = $input['firstName'].' '.$input['lastName'];
            $input['sex'] = $this->post('sex');
            $input['contactInfo'] = array(
                'address' => $this->post('address'),
                'website' => $this->post('website'),
                'companyName' => $this->post('companyName')
            );

            
                // $input['phones'] = $this->post('number');
            $number = $this->post('number');
            $email = $this->post('email');

            if(isset($_FILES['avatar'])){                  
                $file_name_upload = upload_file(UPLOAD_PATH_IMAGE_PROFILE,$_FILES['avatar']);
                if(is_array($file_name_upload)){ // error
                    $this->response($file_name_upload);
                }else if($file_name_upload==false){
                    $this->response(msg_error('Error when upload file'));
                }
                else{
                    $input['avatar'] = base_url().UPLOAD_PATH_IMAGE_PROFILE.'/'.$file_name_upload;
                }   
            }
            if(isset($number)){
                $input['phones'][] = array(
                    'countryCode' => '+855',
                    'number' => $this->post('number'),
                    'status' => 1
                );
            }  
            if(isset($email)){
                if(validate_email($email)){
                    $input['emails'] = $email;
                }else{
                    $this->response(msg_error('Invalid email address'));
                }
            }

            $filterParam= $this->_param_update($input);
           // var_dump($filterParam);die;
            $user = $this->profile->edit_profile($filterParam);
            $this->response(msg_success($user));  
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }   

    private function _param_update($update_data) {
        $output = array();
        foreach ($update_data as $key => $val) {
            if ($val != '' || $val!=null || $val!=false || !empty($val))
                $output[$key] = $val;
        }
        $output['modifiedDate'] = date('Y-m-d H:m:s A');
        $output['isEdit'] = true;

        return $output;
    }


    private function _get_random_image_name($file){
        $image_name = $file['name']; 
        $full_name = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).round(microtime(true)) . basename($file['name']);
        $full_name =str_replace(" ","_",$full_name);
        $full_name =  preg_replace("/( |\+|\|\,|\(|\)|')/", "", $full_name);
        $full_name =strip_tags($full_name);
        return $full_name;
    }

        /*****Add interest category*/

    public function add_interest_category_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'categoryId' => $this->post('categoryId')
        );
        $this->_require_parameter($input);
            // check if that profile is exist with accessKey     
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
         
        if($profile){
            $status = $this->profile->add_interest_category($input);
            if($status === true){
                $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
                $this->response(msg_success($profile));
            }else{
                $this->response(msg_error($status));                
            }
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

        /*****Remove interest category*/

    public function delete_interest_category_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'categoryId' => (int) $this->post('categoryId')
        );
        $this->_require_parameter($input);
            // check if that profile is exist with accessKey     
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
         
        if($profile){
            $status = $this->profile->delete_interest_category($input);
            if($status === true){
                $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
                $this->response(msg_success($profile));
            }else{
                $this->response(msg_error($status));                
            }
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

        /*****Get interest category*/

    public function get_interest_category_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);
            // check if that profile is exist with accessKey     
        $accessKey = $this->post('accessKey');
        $profile = $this->profile->get_profile_user_by_accessKey($accessKey);
         
        if($profile){
            $user = $this->profile->get_interest_category($accessKey);// list of interest cateogry id
            $data = $this->_get_category_info($user['interestCategoryId']);
            //$data['userId'] = $user['userId'];
            //var_dump($data);die;
            $this->response(msg_success($data));
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    private function _get_category_info($listCategoryId){
        $categoryList = array();
        for($i=0 ; $i<count($listCategoryId); $i++){
            $category = $this->profile->get_category_by_id($listCategoryId[$i]);
            $categoryList[] =  array(
                '_id' => $category['_id'],
                'title' => $category['title']
            );
        }   
        return $categoryList;
    }

        /*****Add sanbox money*/
    public function add_money_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'money' => $this->post('money')
        );
        $this->_require_parameter($input); 
        
        // check if the money is greater than 0 
        if($input['money']<0){
            $this->response(msg_error('Money must greater than 0'));
        }
            // check if that profile is exist with accessKey      
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
         // var_dump($profile);
        if($profile){
            $data = $this->profile->add_money($profile, $input['money']);
            $this->_add_transaction_history($data,$input['money']);
            $this->response(msg_success($data));
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    private function _add_transaction_history($data,$amount){
        $record['profileId'] = $data['_id'];
        $record['type'] = new MongoInt32(1); // 1:Deposit, 2:Withdraw
        $record['amount'] = new MongoInt32($amount);
        // var_dump($record);die;
        $this->transaction_history->add_transaction_history($record);
    }

        
        /*****Add subscriber to user*/
    public function add_subscriber_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'subscriberId' => $this->post('subscriberId')
        );
        $this->_require_parameter($input);

        // check if that profile is exist with accessKey      
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
       
        if($profile){       
            $status = $this->profile->add_subscriber($input);
            if($status === true){
                $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
                $this->response(msg_success($profile));
            }else{
                $this->response(msg_error($status));                
            }
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

        /*****Remove subscriber*/

    public function delete_subscriber_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'subscriberId' => $this->post('subscriberId')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey     
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
         
        if($profile){
            $status = $this->profile->delete_subscriber($input);
            if($status === true){
                $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
                $this->response(msg_success($profile));
            }else{
                $this->response(msg_error($status));                
            }
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }








}