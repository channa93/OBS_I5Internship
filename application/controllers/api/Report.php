<?php
defined('BASEPATH') or exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

/*
 * @Copy Right Borama Consulting
 *
 * @channa
 *
 * @30/3/2016
 */

class Report extends REST_Controller{

    function __construct(){
        parent:: __construct();
        $this->load->model('Product_model', 'product');
        $this->load->model('Report_model', 'report');


        $this->load->model('Profile_model', 'profile');
        $this->load->model('Category_model', 'category');
        $this->load->model('ProductCondition_model', 'condition');
        $this->load->model('Currency_model', 'currency');
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
        $products = $this->product->get_all_products();
        $this->response($products);
    }

    public function add_report_product_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId'),
            'description' => $this->post('description')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $input['reportById'] = $profile['userId'];
            unset($input['accessKey']);
            $respone = $this->report->add_report_product($input);
            $this->response($respone);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

}