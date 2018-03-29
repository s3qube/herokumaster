<?php

class Support extends CI_Controller {

	function __construct()
    {
    
    	parent::__construct();
    	$this->opm->checkLogin();
    	
    	$this->load->helper('text');
    	$this->opm->opmInit();
		
		$this->load->model('users_model');
    	$this->opm->activeNav = "";
    }

    function index() 
	{
		
		$data = array();
		/*$log_userid=$this->session->userdata('userid');
		$user_group_id=$this->users_model->checkUserGroup($log_userid);
		$data['ulogingroup']=$this->users_model->checkgroupparent($user_group_id);		*/
		

		$template['page_title'] = "Help";
        $template['bigheader'] = "Help";
        $template['nav2'] = "Help";
     

        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
		
		$template['content'] = $this->load->view('support/support',$data,true);
		$this->load->view('global/maintemplate', $template);
		
		
	}

	function view($file)
	{
	
		$file=base_url().'user_manuals/'.$file.'';
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename='.$file.'');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		@readfile($file);

	}


}
?>