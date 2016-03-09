<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class ProductCondition_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('General_model','general');
        $this->load->model('ValidationField_model','validateField');
    }
    
    public function add_production_condition($condition){
        
        //check for last condition id
        $total_condition = $this->mongo_db->order_by(array('_id' => -1))->get(TABLE_CONDITION);
        if(empty($total_condition)){
            $condition['_id'] = new MongoInt32(1) ;
        }else {
            $condition['_id'] = new MongoInt32($total_condition[0]['_id'] + 1) ;
        }

        try{
            //format data before insert into database
            $format_data = $this->validateField->condition($condition);

            //insert format data into database
            $result = $this->mongo_db->insert(TABLE_CONDITION, $format_data);

            if($result){
                //query to get that newly insert condition
                $get_condition = $this->mongo_db->where(array('_id' => $result))->get(TABLE_CONDITION);
                $get_condition[0]['currencyId'] = $get_condition[0]['_id'];

                //remove field createdDate and modifiedDate before send to client
                unset($get_condition[0]['_id'], $get_condition[0]['createdDate'], $get_condition[0]['modifiedDate']);

                //must return an object of currency that just created as {code, data, message}
                return msg_success($get_condition[0]);
            } else {
                return msg_error('Unable to create new currency');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function get_all_products_condition(){

        try{
            $conditions = $this->mongo_db->get(TABLE_CONDITION);
            $result = [];

            foreach($conditions as $obj){
                $obj['conditionId'] = $obj['_id'];
                unset($obj['_id'],$obj['createdDate'], $obj['modifiedDate']);
                array_push($result, $obj);
            }

            return msg_success($result);

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function update_product_condition($conditionId, $condition){

        try{
            $condition['modifiedDate'] = date('Y-m-d H:m:s A');

            //update format data in database
            $result = $this->mongo_db->where(array('_id' => new MongoInt32($conditionId)))->set($condition)->update(TABLE_CONDITION);

            //query to get that newly update condition
            if($result){
                $get_condition = $this->mongo_db->where(array('_id' => new MongoInt32($conditionId)))->get(TABLE_CONDITION);
                $get_condition[0]['currencyId'] = $get_condition[0]['_id'];
                unset($get_condition[0]['_id'], $get_condition[0]['createdDate'], $get_condition[0]['modifiedDate']);
                return msg_success($get_condition[0]);
            }else {
                return msg_error('Unable to update currency');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
        
    }
    
    public function delete_product_condition($conditionId){

        $result = $this->mongo_db->where(array('_id' => new MongoInt32($conditionId)))->get(TABLE_CONDITION);

        if(!empty($result)){
            $result = $this->mongo_db->delete(TABLE_CONDITION);
            return msg_success($result);
        }else {
            return msg_error('Unable to delete currency');
        }

    }
   
}

