<?php

class Welcome extends CI_Controller {

	function Welcome()
	{
    	parent::__construct();
	}
	
	function index()
	{
		mail("tim@studio211.us", "test mail", "this is an email");
		$this->load->view('welcome_message');
	}
}
?>