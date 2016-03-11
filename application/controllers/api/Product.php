<?php
defined('BASEPATH') or exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */

class Product extends REST_Controller{

    function __construct(){
        parent:: __construct();
        $this->load->model('Product_model', 'product');
        $this->load->model('Profile_model', 'profile');
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
    public function add_product_post(){

        // check require param accessKey
        $input = array(
            'name' => $this->post('name'),
            'price' => (double)$this->post('price'),
            'currencyType' => (int)$this->post('currencyType'),
            'accessKey' => $this->post('accessKey')
        );
        $this->_require_parameter($input);
        $input['description'] = $this->post('description');
        $input['productCode'] = $this->post('productCode');

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            if(count($_FILES)>0 ){ // if there is files pass to server
                 $input['imageGallery'][] = $this->_upload_image_gallery($_FILES);
            }
            $input['ownerId'] = $profile['_id']->{'$id'};
            unset($input['accessKey']);
            $output = $this->product->add_product($input);
            $this->response($output);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }
    private function _upload_image_gallery($files){
        $imageGallery=array();
        foreach($files as $file){
            $file_name_upload = upload_file(UPLOAD_PATH_IMAGE_PRODUCT, $file);
            if(is_array($file_name_upload)){ // error
                $this->response($file_name_upload);
            }else if($file_name_upload==false){
                $this->response(msg_error('Error when upload file'));
            }
            else{
                $imageGallery[] = base_url().UPLOAD_PATH_IMAGE_PRODUCT.'/'.$file_name_upload;
            }
        }
        if(count($imageGallery)<=0) return;
        return $imageGallery;
    }


    // public function read_available_product_get(){
    //     $response = $this->product->read_available_product();
    //     $this->response($response);
    // }

    // public function update_product_post(){
    //     $params = $this->post();
    //     $response = $this->product->update_product($params);
    //     $this->response($response);
    // }

    // public function delete_product_post(){
    //     $params = $this->post();
    //     $response = $this->product->delete_product($params['id']);
    //     $this->response($response);
    // }




}



