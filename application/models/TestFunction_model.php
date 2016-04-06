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

    // 1st method: using aggregate_group in Mongo_db library
    // public function get_ctrls() //// get array list of controllers with sorting ascendant
    // {
    //     try {
    //         // $data = $this->mongo_db->select(array('controller'))
    //             // ->get(TABLE_TEST_FUNCTION);

    //            // aggregate_group(array('fieldToAgregate' => array('fieldRename' => '$oldfield'))) , when select 2 field not correct
    //         $data = $this->mongo_db->aggregate_group(array('_id' => array('controller' => '$controller')))
    //                 ->aggregate(TABLE_TEST_FUNCTION);

    //         foreach ($data as $key => $value) {
    //             $data[$key]['controller'] = $data[$key]['_id']['controller'];
    //             unset($data[$key]['_id']);
    //         }
    //         return msg_success($data);
    //     } catch (Exception $e) {
    //         var_dump($e->getMessage());die;
    //         return msg_exception($e->getMessage());
    //     }
    // } 

    // 2nd method: usig simple method
    public function get_ctrls()  // get array list of controllers with sorting ascendant
    {
        try {
            $data= $this->mongo_db->order_by(array('controller' => 'ASC'))->get(TABLE_TEST_FUNCTION);

            $responseData = array();
            for ($i=0; $i < count($data); $i++) { 
                $controller = $data[$i]['controller'];
                if(!in_array($controller, $responseData)) // if not exist then push 
                    array_push($responseData, $controller);
            }
        
            return msg_success($responseData);
        } catch (Exception $e) {
            var_dump($e->getMessage());die;
            return msg_exception($e->getMessage());
        }
    } 
    // public function _check_if_exist($array, $controller)
    // {
    //     for ($i=0; $i < count($array); $i++) { 
    //         # code...
    //     }
    // }

    public function get_funcs($ctrlName){
        try {
            $data = $this->mongo_db->order_by(array('action' => 'ASC'))
                      ->where(array('controller' => $ctrlName))
                        ->get(TABLE_TEST_FUNCTION);
            
                // filter id
            foreach ($data as $key => $value) {
                $data[$key]['id'] =  $data[$key]['_id']->{'$id'};                
                unset($data[$key]['_id']);
            }
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

    public function add_function($data)
    {
        try {
            $id = $this->mongo_db->insert(TABLE_TEST_FUNCTION,$data);
            $response = $this->get_funcs($data['controller']);
            return msg_success($response['data']);

        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }

    } 


    public function delete_function($id)
    {
        try {
            $data = $this->mongo_db
                        ->where(array('_id' => new MongoId($id)))
                        ->delete(TABLE_TEST_FUNCTION);
            return msg_success($data);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }

    } 

    public function edit_function($id, $updateData)
    {
        try {
            $status = $this->mongo_db
                        ->where(array('_id' => new MongoId($id)))
                        ->set($updateData)
                        ->update(TABLE_TEST_FUNCTION);
            $response = $this->get_funcs($updateData['controller']);
            return msg_success($response['data']);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }

    }



}