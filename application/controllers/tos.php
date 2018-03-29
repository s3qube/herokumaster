<?php
class Tos extends CI_Controller {

	function __construct() {
	
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('tos_model');
    	$this->load->model('usergroups_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = '';
    	
    	//if ($this->config->item('debugMode') == true)
    	//	$this->output->enable_profiler(TRUE);
    		
    	global $searchArgList;
    	
    	$searchArgList = array("usergroupid","includeparents","showonlyactive","orderBy","orderByAscDesc","perPage","offset");
    
    	//if ($this->config->item('debugMode') == true || $this->userinfo->userid == 1)
    	//	$this->output->enable_profiler(TRUE);
    		
    }
    
    function index() {
	
		$data = array();
		
		$tosQuery = $this->tos_model->fetchNeededTOS($this->userinfo->userid);
		
		if ($tosQuery->num_rows() == 0) { // no tos needed, redirect.
		
			redirect("/search/");
		
		}
		
	   
		$arrJS['scripts'] = array('jquery-1.6.2.min','opm_scripts');
		$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		$template['page_title'] = "Terms Of Service";
        $template['bigheader'] = "Terms Of Service";
        $template['nav2'] = "&nbsp;";
        
        $data['tosText'] = "";
        $data['tosIDs'] = array();
        
        foreach ($tosQuery->result() as $trow) {
        	        	
        	$data['tosIDs'][] = $trow->id;
        	$data['tosText'] .= nl2br($trow->tostext);
        	
        	$data['tosText'] .= "<br><Br>";
        
        }
       	
       	$data['strTosIDs'] = implode(",",$data['tosIDs']);
       	   		
		$template['content'] = $this->load->view('tos/agree',$data,true);
				
		$this->load->view('global/maintemplate', $template);
		
	}
    
    function handle() {
    
    	/*echo "<pre>";
    	print_r($_POST);
    	die();*/
    	
    	$userid = $this->userinfo->userid;
    	$tosIDs = $this->input->post('tosids');
    	
    	if ($this->tos_model->recordAgreements($userid,$tosIDs)) {
    	
    		if ($this->session->userdata('tosRedirect')) {
				
				redirect($this->session->userdata('tosRedirect'), 'location');
			
			} else {
				
				redirect($this->config->item('startPage'), 'location');
			
			}
    	
    	} else {
    	
    		$this->opm->displayError("Could not record TOS agreement. Please contact your Bravado rep.");
			return false;
    	
    	}
    	
    
    }
    
    function search($usergroupid = 0, $includeparents = 0, $showonlyactive = 0, $orderBy = "id", $orderByAscDesc = "asc", $perPage = 0, $offset = 0) {
	
		if (checkPerms('can_manage_terms_of_service',true)) {
	
			$this->opm->activeNav = 'administration';	
	
	    	global $searchArgList;
			
	    	
	    	foreach ($searchArgList as $k=>$d) 
	    		$data['args'][$d] = ${$d};   	
		
			
			$template['rightNav'] = $this->load->view('tos/rightNav',$data,true);
	   		$template['page_title'] = "Search TOS";
	   		$template['bigheader'] = "Search TOS";
		    $template['nav2'] = $this->load->view('users/nav2',$data,true);
	    	
	    	$data['totalTos'] = $this->tos_model->fetchTosList(true,null,null,$usergroupid,$includeparents,$showonlyactive);
	    	
	    	
	    	$this->load->library('pagination');
			$config['base_url'] = base_url().'/tos/search/'.$usergroupid."/".$includeparents."/".$showonlyactive."/".$orderBy."/".$orderByAscDesc."/".$perPage."/";
			$config['total_rows'] = $data['totalTos'];
			
			// determine per page
			
			if ($perPage)
				$config['per_page'] = $perPage;
			else
				$config['per_page'] = $this->config->item('searchPerPage');
	
	
			$config['uri_segment'] = 15;
	
	
	    	$this->pagination->initialize($config);
	
	    	$data['tosList'] = $this->tos_model->fetchTosList(false,$offset,$config['per_page'],$usergroupid,$includeparents,$showonlyactive,$orderBy,$orderByAscDesc);
			$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
	
	    	
	    	$data['prodStart'] = $offset + 1;
	    	$data['prodEnd'] = $data['prodStart'] + ($data['tosList']->num_rows() - 1);
	    	
	    	//$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
	    	$template['searchArea'] = $this->load->view('tos/searchArea',$data,true);
	    	$template['contentNav'] = $this->load->view('tos/searchNav',$data,true);
	    	$template['content'] = $this->load->view('tos/search',$data,true);
	    	
	    	$arrJS['scripts'] = array('jquery-1.6.2.min','opm_scripts'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	    		
	    	$this->load->view('global/maintemplate', $template);
	    
    	}
    
    }

   function edit($id = 0) { // if id = 0, we are adding.
    	
    	if (checkPerms('can_manage_terms_of_service',true)) {
    	
	    	$this->opm->activeNav = 'administration';
	    	
	    	$data['id'] = $id;
	    	
	    	if ($id) {
	    	
	    		$data['tos'] = $this->tos_model->fetchTOS($id);
	    	
	    	}
	    	
	    	$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
	    	
	    	$template['content'] = $this->load->view('tos/edit',$data,true);
	        
	        if (!$id) {
	        
	        	$template['page_title'] = "Add TOS";
		        $template['bigheader'] = "Add TOS";
		       	$template['nav2'] = "Add TOS";
	        
	        } else {
	        
	        	$template['page_title'] = "Edit TOS";
		        $template['bigheader'] = "Edit TOS";
		       	$template['nav2'] = "Edit TOS";
	        
	        }
	        
	        
	        
	        $arrJS['scripts'] = array('jquery-1.6.2.min','jquery.validate-1.8.1.min','opm_scripts','tiny_mce/tiny_mce'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
			$template['headInclude'] = $this->load->view('tos/tinyMceInit',$data,true);
			
			
			
			$this->load->view('global/maintemplate', $template);
			
		}
		
    
    }
    
		
	function submit() { // construct a search url from a form submission

    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/invoices/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
    
    }
    
    function save() {
    	
    	$errors = "";
    	
    	//print_r($_POST);
    	//die();
    	
    	if ($this->input->post('id'))
			$postdata['id'] = $this->input->post('id');
		else
			$mode = "add";
			
		if (!$postdata['tosname'] = $this->input->post('tosname'))
			$errors .= "Name Must have a value!<br />";
			
		if (!$postdata['tostext'] = $this->input->post('tostext'))
			$errors .= "Text Must have a value!<br />";
			
			
		if ($errors) {
		
			$this->opm->displayError($errors);
			return false;
			
		}
		
		$postdata['usergroupid'] = $this->input->post('usergroupid');
			
		$postdata['isactive'] = $this->input->post('isactive');
		
		$postdata['effectivedate'] = $this->input->post('effectivedate');
		
		if ($postdata['effectivedate']) {
			
			$splitDate = explode("-", $postdata['effectivedate']);
				
			if (is_array($splitDate))
				$postdata['effectivedate'] = @mktime(1,0,0,$splitDate[0],$splitDate[1],$splitDate[2]);
			
		}
		
		
		if ($this->tos_model->saveTOS($postdata)) {
		
			$this->opm->displayAlert("TOS entry saved!","/tos/search");
			return true;
		
		}    	
    
    
    }

	 
    
}



?>