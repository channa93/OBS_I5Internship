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

class TestFunction extends REST_Controller{

    function __construct(){
        parent:: __construct();
        $this->load->model('Product_model', 'product');
        $this->load->model('Profile_model', 'profile');
        $this->load->model('Category_model', 'category');
        $this->load->model('ProductCondition_model', 'condition');
        $this->load->model('Currency_model', 'currency');
        $this->load->model('TestFunction_model', 'test_function');
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

    public function index_get()
    {
        $ctrls= $this->test_function->get_all();
        $this->response($ctrls);

    }
    public function get_ctrls_get()
    {
        $ctrls= $this->test_function->get_ctrls();
        $this->response($ctrls);

    }

    public function get_funcs_post()
    {
         // var_dump($this->post());die;
        // $this->response(msg_success($this->post()));
        $ctrlName = $this->post('ctrlName');
        $funcs= $this->test_function->get_funcs($ctrlName);
        $this->response($funcs);
    }

    public function get_info_of_function_post()
    {
        // var_dump($this->post());die;
        $funcName = $this->post('funcName');
        $info= $this->test_function->get_info_of_function($funcName);
        $this->response($info);
    }

    public function add_function_post()
    {

        //$this->response(msg_success($this->post()));
        // check require param accessKey
        $input = array( 
            'controller' => $this->post('controller'),
            'action' => $this->post('action'),
            'method' => 'POST',
            'description' => $this->post('description'),
            'params' => $this->post('params')
        );

        $response = $this->test_function->add_function($input);
        $this->response($response);
    }


    public function delete_function_post()
    {

        $input = array( 
            'id' => $this->post('id')
        );
        $this->_require_parameter($input);

        $response = $this->test_function->delete_function($input['id']);
        $this->response($response);
    }



}