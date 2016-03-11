<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */
class ValidationField_model extends CI_Model {

    public function __construct(){
        parent::__construct();
       // date_default_timezone_set("UTC");
        date_default_timezone_set("Asia/Bangkok");
    } 

    /**
     * used to prepare the template data
     * @param  [array] $template_fields 
     * @param  [array] $data            
     * @return [array]  array template of data               
     */
    private function _prepare_input($template_fields,$data){
        try {
            foreach ($data as $key => $val) {
                if ($val != FALSE && $val != NULL) {
                    $template_fields[$key] = $val;
                }
            }
            return $template_fields;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * template for collection Profile
     * @param  [array] $data 
     * @return [array] array of data template      
     */
    public function profile($data){
        $template_fields = array(
            'userName' => '',
            'firstName' => '',
            'lastName' => '',
            'accessKey' => '',
            'sex' => '',
            'avatar' => '',
            'emails' => array(), //[{email,status},...]
            'phones' => array(), //[{countryCode, number, status},....]
            'contactInfo' => array(
                'address' => '',
                'website' => '',
                'companyName' => ''
            ),
            'accountType' => NORMAL,
            'interestCategoryId' => array(),
            'socialAccount' => array(), //{socialId, type}  , type 1:FACEBOOK,2:GOOGLE+
            'subscriber' => array(),
            'wallet' => 0,
            'hits' => 0,
            'status' => ACTIVE,
            'createdDate' => date("Y-m-d H:m:s A"),
            'modifiedDate' => date("Y-m-d H:m:s A"),
            'isEdit' => false         
        );
        if ($data == FALSE) {
            return $template_fields;
        }
        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }

    /**
     * template for collection currency
     * @param [array] $data
     * @return [array] array of data template
     */
    public function currency($data){

        $template_fields = array(
            '_id' => new MongoInt32(0),
            'title' => '',
            'description' => '',
            'createdDate' => date('Y-m-d H:m:s A'),
            'modifiedDate' => date('Y-m-d H:m:s A')
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }

    /**
     * template for collection category
     * @param [array] $data
     * @return [array] array of data template
     */
    public function category($data){

        $template_fields = array(
            '_id' => new MongoInt32(0),
            'title' => '',
            'description' => '',
            'createdDate' => date('Y-m-d H:m:s A'),
            'modifiedDate' => date('Y-m-d H:m:s A')
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }

    /**
     * template for collection productCondition
     * @param [array] $data
     * @return [array] array of data template
     */
    public function condition($data){

        $template_fields = array(
            '_id' => new MongoInt32(0),
            'title' => '',
            'description' => '',
            'createdDate' => date('Y-m-d H:m:s A'),
            'modifiedDate' => date('Y-m-d H:m:s A')
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }

    /**
     * template for collection account type
     * @param [array] $data
     * @return [array] array of data template
     */
    public function accountType($data){

        $template_fields = array(
            '_id' => new MongoInt32(0),
            'type' => '',
            'priceCharge' => (double)(0),
            'features' => array(),
            'createdDate' => date('Y-m-d H:m:s A'),
            'modifiedDate' => date('Y-m-d H:m:s A')
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }

    /* template for collection TransactionHistory
     * @param [array] $data
     * @return [array] array of data template
     */
    public function transaction_history($data){

        $template_fields = array(
            '_id' => new MongoId(),
            'type' => '',
            'amount' => 0,
            'profileId' => '',
            'createdDate' => date('Y-m-d H:m:s A')
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }
    
    
   

}
