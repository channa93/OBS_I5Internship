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

class Currency extends REST_Controller{
    
    function __construct(){
        parent:: __construct();
        $this->load->model('Currency_model', 'currency');
    }
    
    public function add_currency_post(){
        $params = array(
            'title' => $this->post('title'),
            'description' => $this->post('description')
        );
        
        //TODO: check accesskey
        
        //TODO: check require params
        
        //TODO: check lenght of params
        
        $response = $this->currency->add_currency($params);
        $this->response($response);
        
    }
    
    public function get_all_currencies_get(){
        $response = $this->currency->get_all_currencies();
        $this->response($response);
    }
    
    public function update_currency_post(){
        $params = array(
            '_id' => $this->post('_id'),
            'title' => $this->post('title'),
            'description' => $this->post('description')
        );
        
        //TODO: check accesskey
        
        //TODO: check require params
        
        //TODO: check lenght of params
        
        $response = $this->currency->update_currency($params);
        $this->response($response);
        
    }
    
    public function delete_currency_post(){
        
        //TODO: check accesskey
        
        //TODO: check require params
        
        $response = $this->currency->delete_currency($this->post('_id'));
        $this->response($response);
        
        
    }
    
    
    
    
    
    
}



