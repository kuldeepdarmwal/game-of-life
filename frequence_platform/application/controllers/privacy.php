<?php
class privacy extends CI_Controller
{
   
    public function __construct()
    {
	    parent::__construct();
	    $this->load->helper('form');
	    $this->load->library('session');
    }
      
    public function index()
    {
	$this->load->view('privacy/privacy');
    }
}

?>