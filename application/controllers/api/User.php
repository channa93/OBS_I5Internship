<?php
defined('BASEPATH') OR exit('No direct script access allow');
require APPPATH.'/libraries/REST_Controller.php';

class User extends REST_Controller{

	function __construct()
	 {
	 	parent::__construct();
	 	$this->load->model('User_model','user');
	 	
	 }
	public function index_get(){
	   // $data = array('a','b','c');
	    $data =  $this->user->get_users();
	    $this->response($data);
	}
	public function get_users_get(){
	   // $data = array('a','b','c');
	    $data =  $this->user->get_users();
	    $this->response($data);
	}

	
}