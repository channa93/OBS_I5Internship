<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */

class TestFunction_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('ValidationField_model','validateField');
    }


    public function get_all()
    {
        try {
            $data = $this->mongo_db->get(TABLE_TEST_FUNCTION);
            return msg_success($data);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    } 

    public function get_ctrls()
    {
        try {
            // $data = $this->mongo_db->select(array('controller'))
                // ->get(TABLE_TEST_FUNCTION);

               // aggregate_group(array('fieldToAgregate' => array('fieldRename' => '$oldfield'))) , when select 2 field not correct
            $data = $this->mongo_db->aggregate_group(array('_id' => array('controller' => '$controller')))
                    ->aggregate(TABLE_TEST_FUNCTION);

            foreach ($data as $key => $value) {
                $data[$key]['controller'] = $data[$key]['_id']['controller'];
                unset($data[$key]['_id']);
            }
            return msg_success($data);
        } catch (Exception $e) {
            var_dump($e->getMessage());die;
            return msg_exception($e->getMessage());
        }
    } 

    public function get_funcs($ctrlName){
        try {
            $data = $this->mongo_db->order_by(array('action' => 'ASC'))->select(array('action'))
                      ->where(array('controller' => $ctrlName))
                        ->get(TABLE_TEST_FUNCTION);
            return msg_success($data);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

    public function get_info_of_function($funcName){
        try {
            $data = $this->mongo_db->select(array('params','method','description'))
                      ->where(array('action' => $funcName))
                        ->get(TABLE_TEST_FUNCTION);
            return msg_success($data);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

}