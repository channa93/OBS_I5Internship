<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * @Copy Right Borama Consulting
 * 
 * @channa
 * 
 * @3/3/2016
 */

class Report_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('ValidationField_model','validateField');
        $this->load->model('Profile_model','profile');
    }

    public function get_report_product_by_id($id)
    {
        try {
             $report = $this->mongo_db
                             ->where(array(
                                '_id' => new MongoId($id),
                                'type' => 1  // 1:product, 2:user
                            ))
                            ->get(TABLE_REPORT);
            $report = $report[0];
            $report['reportId'] =  $report['_id']->{'$id'};
            unset($report['_id']);
            return msg_success($report);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

    public function add_report_product($input)
    {
        try {
            
            $data = $this->validateField->report($input);
            $id = $this->mongo_db->insert(TABLE_REPORT,$data);
            $report = $this->get_report_product_by_id($id->{'$id'});
            return msg_success($report['data']);
        } catch (Exception $e) {
            return msg_exception($e->getMessage());
        }
    }

}
