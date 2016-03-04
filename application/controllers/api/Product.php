<?php
defined('BASEPATH') or exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class Product extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('Product_model', 'product');
    }
    
    public function create_product_post(){
        $params = $this->post();
        $input = array(
            'name' => $params['name'],
            'price' => $params['price'],
            'status' => array(
                'status' => $params['status'],
                'date' => $params['date']
            ),
            'type' => $params['type'],
            'description' => $params['description'],
            'currencyType' => $params['currencyType'],
            'condition' => $params['condition'],
            'createdDate' => $params['createdDate'],
            'modifiedDate' => $params['modifiedDate']
        );
        //var_dump($input);
        //die();
        //$response = $this->product->create_product($params);
        $this->response($input);
    }
    
    public function read_available_product_get(){
        $response = $this->product->read_available_product();
        $this->response($response);
    }
    
    public function update_product_post(){
        $params = $this->post();
        $response = $this->product->update_product($params);
        $this->response($response);
    }
    
    public function delete_product_post(){
        $params = $this->post();
        $response = $this->product->delete_product($params['id']);
        $this->response($response);
    }
    
    
    
    
}



