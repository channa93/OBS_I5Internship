<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class Currency_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('General_model','general');
        $this->load->model('ValidationField_model','validateField');
    }
    
    public function add_currency($currency){
        
        //check for last currency id
        $total_currency = $this->mongo_db->order_by(array('_id' => -1))->get(TABLE_CURRENCY);
        if(empty($total_currency)){
            $currency['_id'] = new MongoInt32(1) ;
        }else {
            $currency['_id'] = new MongoInt32($total_currency[0]['_id'] + 1) ;
        }

        try{
            //format data before insert into database
            $format_data = $this->validateField->currency($currency);

            //insert format data into database
            $result = $this->mongo_db->insert(TABLE_CURRENCY, $format_data);

            if($result){
                //query to get that newly insert currency
                $get_currency = $this->mongo_db->where(array('_id' => $result))->get(TABLE_CURRENCY);
                $get_currency[0]['currencyId'] = $get_currency[0]['_id'];

                //remove field createdDate and modifiedDate before send to client
                unset($get_currency[0]['_id'], $get_currency[0]['createdDate'], $get_currency[0]['modifiedDate']);

                //must return an object of currency that just created as {code, data, message}
                return msg_success($get_currency[0]);
            } else {
                return msg_error('Unable to create new currency');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function get_all_currencies(){

        try{
            $currencies = $this->mongo_db->get(TABLE_CURRENCY);
            $result = [];

            foreach($currencies as $obj){
                $obj['currencyId'] = $obj['_id'];
                unset($obj['_id'],$obj['createdDate'], $obj['modifiedDate']);
                array_push($result, $obj);
            }

            return msg_success($result);

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function update_currency($currency_id, $currency){

        try{
            $currency['modifiedDate'] = date('Y-m-d H:m:s A');

            //update format data in database
            $result = $this->mongo_db->where(array('_id' => new MongoInt32($currency_id)))->set($currency)->update(TABLE_CURRENCY);

            //query to get that newly update currency
            if($result){
                $get_currency = $this->mongo_db->where(array('_id' => new MongoInt32($currency_id)))->get(TABLE_CURRENCY);
                $get_currency[0]['currencyId'] = $get_currency[0]['_id'];
                unset($get_currency[0]['_id'], $get_currency[0]['createdDate'], $get_currency[0]['modifiedDate']);
                return msg_success($get_currency[0]);
            }else {
                return msg_error('Unable to update currency');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
        
    }
    
    public function delete_currency($currency_id){

        $result = $this->mongo_db->where(array('_id' => new MongoInt32($currency_id)))->get(TABLE_CURRENCY);

        if(!empty($result)){
            $result = $this->mongo_db->delete(TABLE_CURRENCY);
            return msg_success($result);
        }else {
            return msg_error('Unable to delete currency');
        }

    }
   
}

