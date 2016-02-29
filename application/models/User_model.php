<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	public function __construct()
    {
        parent::__construct();
    } 


    public function get_users(){
        return $this->mongo_db->get(TABLE_USER);
    }

}

/* End of file User.php */
/* Location: ./application/models/User.php */