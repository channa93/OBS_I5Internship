<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @sokunthearith
 * 
 * @3/3/2016
 */

class Category_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('General_model','general');
        $this->load->model('ValidationField_model','validateField');
    }
    
    public function add_category($category){
        
        //check for last category id
        $total_category = $this->mongo_db->order_by(array('_id' => -1))->get(TABLE_CATEGORY);
        if(empty($total_category)){
            $category['_id'] = new MongoInt32(1);
        }else {
            $category['_id'] = new MongoInt32($total_category[0]['_id'] + 1) ;
        }

        try{
            //format data before insert into database
            $format_data = $this->validateField->category($category);

            //insert format data into database
            $result = $this->mongo_db->insert(TABLE_CATEGORY, $format_data);

            if($result){
                //query to get that newly insert currency
                $get_category = $this->mongo_db->where(array('_id' => $result))->get(TABLE_CATEGORY);
                $get_category[0]['categoryId'] = $get_category[0]['_id'];
                //remove field createdDate and modifiedDate before send to client
                unset($get_category[0]['_id'], $get_category[0]['createdDate'], $get_category[0]['modifiedDate']);

                //must return an object of currency that just created as {code, data, message}
                return msg_success($get_category[0]);
            } else {
                return msg_error('Unable to create new category');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function get_all_categories(){

        try{
            $categories = $this->mongo_db->get(TABLE_CATEGORY);
            $result = [];

            foreach($categories as $obj){
                $obj['categoryId'] = $obj['_id'];
                unset($obj['_id'],$obj['createdDate'], $obj['modifiedDate']);
                array_push($result, $obj);
            }

            return msg_success($result);

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }

    }
    
    public function update_category($category_id, $category){

        try{
            $category['modifiedDate'] = date('Y-m-d H:m:s A');

            //update format data in database
            $result = $this->mongo_db->where(array('_id' => new MongoInt32($category_id)))->set($category)->update(TABLE_CATEGORY);

            //query to get that newly update category
            if($result){
                $get_category = $this->mongo_db->where(array('_id' => new MongoInt32($category_id)))->get(TABLE_CATEGORY);
                $get_category[0]['categoryId'] = $get_category[0]['_id'];
                //remove field createdDate and modifiedDate before send to client
                unset($get_category[0]['_id'], $get_category[0]['createdDate'], $get_category[0]['modifiedDate']);
                return msg_success($get_category[0]);
            }else {
                return msg_error('Unable to update currency');
            }

        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
        
    }
    
    public function delete_category($category_id){

        try{
            $result = $this->mongo_db->where(array('_id' => new MongoInt32($category_id)))->delete(TABLE_CATEGORY);

            if(!empty($result)){
                $result = $this->mongo_db->delete(TABLE_CATEGORY);
                return msg_success($result);
            }else {
                return msg_error('Unable to delete category');

            }
        }catch(Exception $e){
            return msg_exception($e->getMessage());
        }
    }
   
}

