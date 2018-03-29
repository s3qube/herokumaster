<?php
class Designerqueue extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->activeNav = '';
    	$this->load->model('users_model');
    	
    	checkPerms('view_designer_queue', true);
    		
    }

	function viewQueue($designerid = 0) // index will be the queue!
	{
	
		$data = array();

		$template['page_title'] = "Designer Queue";
        $template['bigheader'] = "Designer Queue";
        $template['nav2'] = "Designer Queue";
		
		$data['designerQueue'] = $this->users_model->fetchDesignerQueue();
		
		$template['content'] = $this->load->view('designerqueue/view',$data,true);
		$this->load->view('global/maintemplate', $template);
		
	}

}

?>