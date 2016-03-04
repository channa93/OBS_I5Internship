<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ValidationField_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set("UTC");
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
            'contactInfo' => array(),
            'accountType' => NORMAL,
            'interestCategoryId' => array(),
            'socialAccount' => array(), //{socialId, type}  , type 1:FACEBOOK,2:GOOGLE+
            'subscriber' => array(),
            'wallet' => 0,
            'hits' => 0,
            'status' => ACTIVE,
            'createdDate' => date("Y-m-d"),
            'modifiedDate' => date("Y-m-d")         
        );
        if ($data == FALSE) {
            return $template_fields;
        }
        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }
    
    
    
    
   

}
