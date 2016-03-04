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
    }
    
    public function add_currency($currency){
        
        //TODO: check for last currecny id
        $total_currency = $this->mongo_db->count(TABLE_CURRENCY);
        
        //TODO: format data before insert into database
        $new_currency = array(
            '_id' => new MongoInt32($total_currency + 1),
            'title' => $currency['title'],
            'description' => $currency['description'],
            'createdDate' => date('Y-m-d'),
            'modifiedDate' => date('Y-m-d')
        );
        
        //TODO: insert format data into database
        $result = $this->mongo_db->insert(TABLE_CURRENCY, $new_currency);
        
        //TODO: query to get that newly insert currency
        $get_currency = $this->mongo_db->where(array('_id' => $result))->get(TABLE_CURRENCY);
        
        //remove field createdDate and modifiedDate before send to client
        unset($get_currency[0]['createdDate'], $get_currency[0]['modifiedDate']);
        
        //must return an object of currency that just created as {code, data, message}
        return $get_currency[0];
    }
    
    public function get_all_currencies(){
        
        $currencies = $this->mongo_db->get(TABLE_CURRENCY);
        $result = [];
        
        foreach($currencies as $obj){
            unset($obj['createdDate'], $obj['modifiedDate']);
            array_push($result, $obj);
        }
        
        //return {code, data, message}
        return $result;
    }
    
    public function update_currency($currency){
        
        $currency_created_date = $this->mongo_db->where(array('_id' => new MongoInt32($currency['_id'])))->get(TABLE_CURRENCY);
        
        //TODO: format data before edit in database
        $update_currency = array(
            'title' => $currency['title'],
            'description' => $currency['description'],
            'createdDate' => $currency_created_date[0]['createdDate'],
            'modifiedDate' => date('Y-m-d') //change modifiedDate before update
        );
        
        //TODO: update format data in database
        $result = $this->mongo_db->where(array('_id' => new MongoInt32($currency['_id'])))->update(TABLE_CURRENCY, $update_currency);
        
        //TODO: query to get that newly update currency
        if($result){
            $get_currency = $this->mongo_db->where(array('_id' => new MongoInt32($currency['_id'])))->get(TABLE_CURRENCY);
            unset($get_currency[0]['createdDate'], $get_currency[0]['modifiedDate']);
           
            //return object of the newly updated currency as {code, data, message}
            return $get_currency[0];
        }
        
    }
    
    public function delete_currency($currency_id){
        $result = $this->mongo_db->where(array('_id' => new MongoInt32($currency_id)))->delete(TABLE_CURRENCY);
        
        //return {code, data, message}
        return $result;
        
    }
   
}

