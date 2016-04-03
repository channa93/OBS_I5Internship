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

    public function add_product_post(){
        // check require param accessKey
        $input = array(
            'name' => $this->post('name'),
            //'price' => (double)$this->post('price'),
            'price' => (double) number_format(doubleval($this->post('price')), 2, '.', ''), //float with 2 decimal places: .00
            'currencyType' => (int)$this->post('currencyType'),
            'categoryId' => (int)$this->post('categoryId'),
            'condition' => (int)$this->post('condition'),
            'accessKey' => $this->post('accessKey')

        );
        $this->_require_parameter($input);
        $input['description'] = $this->post('description');
        $input['productCode'] = $this->post('productCode');


        if(!check_charactor_length($input['name'],TITLE_LENGTH_LIMITED)) $this->response(invalid_charactor_length($input['name'] , 'name'));
        if(!check_charactor_length($input['description'],DESC_LENGTH_LIMITED)) $this->response(invalid_charactor_length($input['description'] , 'description'));

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $input['ownerId'] = $profile['userId'];
            unset($input['accessKey']);
            // add product then add image gallery if adding product is success
            $output = $this->product->add_product($input);
            if($output['code']==1){// success
                $productId = $output['data']['productId'];
                $imageGallery = $this->_upload_image_gallery($_FILES, $productId);
                $output = $this->product->add_images_product($productId, $imageGallery);
            }
            $this->response($output);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    private function _upload_image_gallery($files, $productId){
        $imageGallery=array();
        $target_path = create_folder($productId);  // create folder in path of image product upload and return path
        $target_path = UPLOAD_PATH_IMAGE_PRODUCT.'/'.$target_path;
        foreach($files as $file){
            $file_name_upload = upload_file($target_path, $file);
            if(is_array($file_name_upload)){ // error
                $this->response($file_name_upload);
            }else if($file_name_upload==false){
                $this->response(msg_error('Error when upload file'));
            }
            else{
                $imageGallery[] = base_url().$target_path.'/'.$file_name_upload;
            }
        }
        return $imageGallery;
    }


    public function get_available_products_post(){
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey')
        );
        $this->_require_parameter($input);

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $data = $this->product->get_available_products();
            $this->response($data);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function edit_product_post()
    {
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId')
        );
        $this->_require_parameter($input);
        $input['description'] = $this->post('description');
        $input['categoryId'] = $this->post('categoryId');
        $input['name'] = $this->post('name');
        $input['productCode'] = $this->post('productCode');
        $input['currencyType'] = $this->post('currencyType');
        $input['price'] = (double) number_format(doubleval($this->post('price')), 2, '.', '');

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            // prepare data for update
            $updateData = filter_param_update($input);
            unset($updateData['accessKey'],$updateData['productId']);
            $output = $this->product->edit_product($updateData, $input['productId']);
            
            // check if update success
            if($output['code']==1){// update success then upload image and add image url to db
                $productId = $output['data']['productId'];
                $imageGallery = $this->_upload_image_gallery($_FILES, $productId);
                if(!empty($imageGallery)){
                    $output = $this->product->add_images_product($productId, $imageGallery);
                }
            }
            $this->response($output);

        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function delete_product_post()
    {
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId')
        );
        $this->_require_parameter($input);

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $userId = $profile['userId'];
                // check if product not exist or not own by this user
            $this->_check_user_product_exist($userId, $input['productId']);
            $product = $this->product->delete_product($input);
            $this->response(msg_success(''));
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    public function _check_user_product_exist($userId, $productId)
    {
        $isExist=false;
        $userProducts = $this->product->get_user_products($userId);
        foreach ($userProducts as $key => $value) {
            //var_dump($value['productId'], $input['productId']);
            if($value['productId'] == $productId){
                $isExist = true;
            }
        }
        if($isExist) return true;
        $this->response(msg_error('Product does not exist or it is not a product of this user'));
    }

    // public function get_products_by_user_id_post()
    // {
    //     // check require param accessKey
    //     $input = array(
    //         'accessKey' => $this->post('accessKey'),
    //         'userId' => $this->post('userId'),
    //     );
    //     $this->_require_parameter($input);

    //     // check if that profile is exist with accessKey
    //     $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
    //     if($profile){
    //         $userId = $input['userId'];
    //         $products = $this->product->get_user_products($userId);
    //         if(!empty($products)){
    //             $this->response(msg_success($products));
    //         }
    //         $this->response(msg_error('no product'));
    //     }else{
    //        $this->response(msg_invalidAccessKey());
    //     }
    // }

    // get all my products with full information, all status of product will be query
    public function get_all_my_products_post()
    {
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $userId = $profile['userId'];
            $products = $this->product->get_all_my_products($userId);
            if(!empty($products)){
                 $data['products'] = $products;
                 $data['userInfo'] = $profile;
                $this->response(msg_success($data));
            }
            $this->response(msg_error('no product'));
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    // get all products of other user by his id , product status is available only
    public function get_other_user_products_by_user_id_post()
    {
        // check require param accessKey
        $input = array(
            'accessKey' => $this->post('accessKey'),
            'otherUserId' => $this->post('otherUserId')
        );
        $this->_require_parameter($input);

        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            
            $products = $this->product->get_other_user_products_by_user_id($input['otherUserId']);
            if(!empty($products)){
                $otherUser = $this->profile->get_profile_user_by_id($input['otherUserId']);
                $data['userInfo'] = $otherUser['data'];
                $data['products'] = $products;
                $this->response(msg_success($data));
            }

            $this->response(msg_error('no product'));
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }




    /**
    * get all product categories, condition, and currencies
    * @params
    * @retun [[categories], [condition], [currencies]]
    **/
    public function get_cat_con_cur_post (){
        //check accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($this->post('accessKey'));

        if(!$profile){
            $this->response(msg_invalidAccessKey());
        }

        //get all categories
        $get_categories = $this->category->get_all_categories();

        //get all conditions
        $get_conditions = $this->condition->get_all_products_condition();

        //get all currencies
        $get_currencies = $this->currency->get_all_currencies();

        //prepare data
        $result['categories'] = $get_categories['data'];
        $result['condition'] = $get_conditions['data'];
        $result['currencies'] = $get_currencies['data'];

        //response data with msg_success
        $this->response(msg_success($result));

    }

    public function get_product_detail_by_id_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $product = $this->product->get_product_by_id($input['productId']);
            $ownerInfo =  $this->profile->get_profile_user_by_id($product['data']['ownerId']);
            $product['data']['ownerInfo'] = $ownerInfo['data'];
            $this->response($product);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }


    // increment number of viewCount in product info
    public function count_view_product_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
            'productId' => $this->post('productId')
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $response = $this->product->count_view_product($input['productId']);
            $this->response($response);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    
    
    // get system products available: popular,new this month, recommend and normal product
    public function get_system_products_available_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $popular = $this->product->get_popular_products();
            $newThisMonth = $this->product->get_new_products_this_month();
            $recommend = $this->product->get_recommened_products();
            $normal = $this->product->get_available_products();

                // check if no products then set to empty
            if(empty($popular['data'])) $popular=(object)array();
            else $popular = $popular['data'][0];

            if(empty($newThisMonth['data'])) $newThisMonth=(object)array();
            else $newThisMonth = $newThisMonth['data'][0];

            $products = array(
                'popular' => $popular,
                'newThisMonth' => $newThisMonth,
                'recommend' => $recommend['data'],
                'normal' => $normal['data']
            );
            $this->response(msg_success($products));
        }else{
           $this->response(msg_invalidAccessKey());
        }

    }

    // get popular product
    public function get_popular_products_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $popular = $this->product->get_popular_products();    
            $this->response($popular);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    // get new this month product
    public function get_new_products_this_month_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $newThisMonth = $this->product->get_new_products_this_month();
            $this->response($newThisMonth);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }

    // get recommend products
    public function get_recommened_products_post()
    {
        // check require param accessKey
        $input = array( 
            'accessKey' => $this->post('accessKey'),
        );
        $this->_require_parameter($input);
        
        // check if that profile is exist with accessKey
        $profile = $this->profile->get_profile_user_by_accessKey($input['accessKey']);
        if($profile){
            $recommend = $this->product->get_recommened_products();
            $this->response($recommend);
        }else{
           $this->response(msg_invalidAccessKey());
        }
    }




}
