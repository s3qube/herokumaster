<?php
class Recipients extends CI_Controller {

	function __construct() {
    
    	parent::__construct();
    	
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	
    	$this->load->model('recipients_model');
    
    }
    
    function getRecipients() {

		$recipients = $this->recipients_model->fetchRecipients(opm_productid);
		$this->load->view('recipientspicker/recipientsview',$recipients);
	
	}
	
}