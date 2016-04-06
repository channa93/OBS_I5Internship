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
            'displayName' => '',
            'firstName' => '',
            'lastName' => '',
            'userName' => '',  // use for future scale that user can login with username and password
            'accessKey' => '',
            'sex' => '',
            'avatar' => '',
            'emails' => array(), //[{email,status},...] //status 0: not comfirm yet, 1:comfirmed,
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
            // 'totalSubscriber' => new MongoInt32(0),
            'wallet' => 100,
            'hits' => 0,
            'status' => ACTIVE,
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT),
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
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT)
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
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT)
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
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT)
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
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT)
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
            'createdDate' => date(DATE_FORMAT)
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }


    /* template for collection Product
     * @param [array] $data
     * @return [array] array of data template
     */
    public function product($data){

        $template_fields = array(
            'name' => '',
            'price' => 0,
            'currencyType' => 1,  //1:KH
            'productCode' => '',
            'status' => array(
                'status' => REVIEW,  //1:review, 2:accepted, 3:rejected, 4:available, 5:sold
                'date' => date(DATE_FORMAT),  //date(DATE_FORMAT) = 2016-03-15 14:03:25 PM
                'reason' => ''   // if status is rejected , then must have reason
            ),
            'type' => new MongoInt32(1),  // 1:simple, 2:event
            'description' => '',
            'imageGallery' => array(),
            'videoGallery' => array(),
            'likerId' => array(),
            'categoryId' => array(),
            'viewCount' => new MongoInt32(0),
            'condition' => 1,  //1: review
            'ownerId' => '',
            'isEdit' => false,
            'isDelete' => false,
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT)
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }

    /* template for collection BidRoom
     * @param [array] $data
     * @return [array] array of data template
     */
    public function bidroom($data){

        $template_fields = array(
            'title' => '',
            'startupPrice' => 0,
            'currencyType' => 1,
            'finalPrice' => -1,
            'ownerId' => '',
            'productId' => '',
            'startDate' => date(DATE_FORMAT),
            'endDate' => date(DATE_FORMAT),
            'isEdit' => false,
            'createdDate' => date(DATE_FORMAT),
            'modifiedDate' => date(DATE_FORMAT),
            'status' => PENDING,   // 0:close, 1:open, 2:pending . by default it is pending
            'isDelete' => false
        );
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    } 

    /* template for collection Report
     * @param [array] $data
     * @return [array] array of data template
     */
    public function report($data){
        if(isset($data['productId'])){ // report product
            $template_fields = array(
                'productId' => '',
                'reportById' => '',
                'description' => '',
                'created' => date(DATE_FORMAT),
                'type' => 1,  // 1: report product, 2: report user
                'statusInfo' => array(
                    'status' => REVIEW,
                    'date'   => date(DATE_FORMAT)
                )    
            );
        }else{  // report user
            $template_fields = array(
                'userId' => '',
                'reportById' => '',
                'description' => '',
                'created' => date(DATE_FORMAT),
                'type' => 1,  // 1: report product, 2: report user
                'statusInfo' => array(
                    'status' => REVIEW,   // //1:review, 2:accepted, 3:rejected 
                    'date'   => date(DATE_FORMAT)
                )    
            );
        }
        
        if(!$data){
            return $template_fields;
        }

        $output = $this->_prepare_input($template_fields, $data);
        return $output;
    }   

}
