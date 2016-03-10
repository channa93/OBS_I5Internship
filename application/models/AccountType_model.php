<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class AccountType_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('General_model','general');
        $this->load->model('ValidationField_model','validateField');
    }
    
    public function add_account_type($accountType){
        
        //check for last account id
        $total_accounts = $this->mongo_db->order_by(array('_id' => -1))->get(TABLE_ACCOUNT);
        if(empty($total_accounts)){
            $accountType['_id'] = new MongoInt32(1) ;
        }else {
            $accountType['_id'] = new MongoInt32($total_accounts[0]['_id'] + 1) ;
        }

        try{
            //format data before insert into database
            $format_data = $this->validateField->accountType($accountType);

            //insert format data into database
            $result = $this->mongo_db->insert(TABLE_ACCOUNT, $format_data);

            if($result){
                //query to get that newly insert account type
                $get_account = $this->mongo_db->where(array('_id' => $result))->get(TABLE_ACCOUNT);
                $get_account[0]['accountId'] = $get_account[0]['_id'];

                //remove field createdDate and modifiedDate before send to client
                unset($get_account[0]['_id'], $get_account[0]['createdDate'], $get_account[0]['modifiedDate']);

                //must return an object of currency that just created as {code, data, message}
                return msg_success($get_account[0]);
            } else {
                return msg_error('Unable to create new currency');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function get_all_accounts_type(){

        try{
            $accounts = $this->mongo_db->get(TABLE_ACCOUNT);
            $result = [];

            foreach($accounts as $obj){
                $obj['accountId'] = $obj['_id'];
                unset($obj['_id'],$obj['createdDate'], $obj['modifiedDate']);
                array_push($result, $obj);
            }

            return msg_success($result);

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function update_account_type($accountId, $account){

        try{
            $account['modifiedDate'] = date('Y-m-d H:m:s A');

            //update format data in database
            $result = $this->mongo_db->where(array('_id' => new MongoInt32($accountId)))->set($account)->update(TABLE_ACCOUNT);

            //query to get that newly update account type
            if($result){
                $get_account = $this->mongo_db->where(array('_id' => new MongoInt32($accountId)))->get(TABLE_ACCOUNT);
                $get_account[0]['currencyId'] = $get_account[0]['_id'];
                unset($get_account[0]['_id'], $get_account[0]['createdDate'], $get_account[0]['modifiedDate']);
                return msg_success($get_account[0]);
            }else {
                return msg_error('Unable to update account type');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
        
    }
    
    public function delete_account_type($accountId){

        try{
            $result = $this->mongo_db->where(array('_id' => new MongoInt32($accountId)))->get(TABLE_ACCOUNT);

            if(!empty($result)){
                $result = $this->mongo_db->delete(TABLE_ACCOUNT);
                return msg_success($result);
            }else {
                return msg_error('Unable to delete account type');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
   
}

