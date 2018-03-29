<?php

class Events extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('events_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    
    	global $searchArgList;
    	$searchArgList = array("userid","perPage","offset","year","month");
    	
    }

	function index() // index will be the property list.
	{
	
		redirect("/events/calendar");	

	}
	
	function calendar($userid = 0, $perPage = 25, $offset = 0, $year = null,$month = null) {
	
	
		//if (checkPerms('can_manage_bodystyles',true)) {
    		global $searchArgList;
			
			$data = array();
			
			$data['calData'] = array(
               3  => "1PM Danielle Bradbury TBD",
               7  => "Grace Potter + Nocturnals"
             );

            
            $data['calendar'] = $this->events_model->generateCalendar($year,$month,$data['calData']);
			$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'));	
            
            foreach ($searchArgList as $k=>$d) 
    			$data['args'][$d] = ${$d};
             
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
		
			$template['page_title'] = "Event Schedule";
			$template['bigheader'] = "Event Schedule (calendar view)";
			$template['searchArea'] = $this->load->view('events/calSearchArea',$data,true);
			//$template['rightNav'] = $this->load->view('bodystyles/rightNav',$data,true);
			//$template['contentNav'] = $this->load->view('bodystyles/searchNav',$data,true);			
			
			
			$template['content'] = $this->load->view('events/calendar', $data,true);
			
			
	        $arrJS['scripts'] = array('jquery-1.8.1.min','opm_scripts'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	        
			$this->load->view('global/maintemplate', $template);
			
		//}
	
	}
	
	function submit() { // construct a search url from a form submission
    
    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/bodystyles/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
			
	
    
    }
	
	
	
	function requestForm($id = 0,$type = null) {
    
		$this->load->model('categories_model');
		$this->load->model('properties_model');
		$this->load->model('colors_model');
		$this->load->model('sizes_model');
		
		$data = array();
		
		$data['formData'] = unserialize($this->session->userdata('formData'));
		
		
		$data['mode'] = "add";	
		
		$data['r'] = new stdClass();
		$data['r']->opm_productid = 0;
		$data['r']->propertyid = 0;
		$data['r']->arid = 0;
		$data['r']->prodid = 0;
		$data['r']->creativeid = 0;
		$data['r']->categoryid = 0; 
		$data['r']->bodystyleid = 0;
		$data['r']->articleid = 0;
		$data['r']->substageid = 0;
		$data['r']->tourStartDate = 0;
		$data['r']->reqNotes = '';

		//$data['b'] = $this->bodystyles_model->fetchBodystyle($id);
		
		$data['categories'] = categoryArray2Select($this->categories_model->fetchCategoriesArray());

		$data['properties'] = $this->properties_model->fetchProperties();
		$data['internalUsers'] = $this->users_model->fetchInternalUsers();
    	$data['colors'] = $this->colors_model->fetchColors();
    	$data['sizes'] = $this->sizes_model->fetchSizes();
    	$template['content'] = $this->load->view('requestForms/tour',$data,true);

        $template['nav2'] = "&nbsp;";
        $template['page_title'] = "Tour Request Form";
		$template['bigheader'] = "Tour Request Form";

		$arrJS['scripts'] = array('jquery-1.8.1.min','jquery.validate-1.8.1.min','opm_scripts','chosen.jquery.min','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		$this->load->view('global/maintemplate', $template);
    }
    
    
    function ajaxReqFormHandler($action="submit") {
	    
	    $this->load->model('colors_model');
	    $this->load->model('categories_model');
	    
	    $data = $this->input->post();
	    
	    opmLog(print_r($data,true));
	   
	    if ($data['removeItem'] != "") {
		    
		    opmLog("removing item # " . $data['removeItem']);
		    
	    }
	   
	   	// put garment post data in garments array, and load cat / color names.
	   	
	   	if (is_array($data['g_categoryid'])) {

		   	foreach ($data['g_categoryid'] as $key=>$xy) {
			   	
			   	if ($data['removeItem'] === '' || $data['removeItem'] != $key) {
	
				   	$data['garments'][$key]['categoryid'] = $data['g_categoryid'][$key];
				   	$data['garments'][$key]['colorid'] = $data['g_colorid'][$key];
				   	$data['garments'][$key]['qty'] = $data['g_qty'][$key];
				
				}
				
		   	} 
	   	
	   	}
	   		   
	    if ($data['addGarment']) {  // are we adding a garment? 
		  
		    $data['garments'][] = array("categoryid"=>$data['add_catid'],"colorid"=>$data['add_colorid'],"qty"=>$data['add_numgoods']);
		    
	    }

	    // fetch color and category names
	    
	    
	    
	    //$textData = serialize($data);
	    $textData = print_r($data,true);
	    //opmLog($textData);
	    
	    unset($data['add_catid'],$data['add_colorid'],$data['add_numgoods'],$data['addGarment'],$data['g_categoryid'],$data['g_colorid'],$data['g_qty']);
	    $sessData = serialize($data);
	    $this->session->set_userdata('formData', $sessData);
	    
    }
    
    function ajaxGarmentList() {
	    
	    $this->load->model('colors_model');
	    $this->load->model('categories_model');
	    
	    $data = $this->session->userdata('formData');
	    $data = unserialize($data);
	    
	   /* echo "<pre>";
	    print_r($data);
	    die();*/
	    
	    if (isset($data['garments']) && is_array($data['garments'])) {
	    
		    foreach ($data['garments'] as $key=>$g) {
			    
			    $data['garments'][$key]['category'] = $this->categories_model->fetchCategory($g['categoryid'])->category;
			    $data['garments'][$key]['color'] = $this->colors_model->fetchColor($g['colorid'])->color;
			    
		    }
		    
		    $this->load->view('events/ajaxGarmentList', $data);
	    
	    }
	    
	    
	    
    }
    
    function saveRequest() {
	    
	    $data = $this->input->post();
	    
	    echo "<pre>";
	    print_r($data);
	    die();
	    
    }
    
    function add() {
    
    	$this->load->model('categories_model');
    
    	if (checkPerms('can_manage_bodystyles',true)) {
		
			$data = array();
			
	    	$data['categories'] = $this->categories_model->fetchCategories();
	    	$template['content'] = $this->load->view('bodystyles/basicinfo',$data,true);
	    		    	
	        $template['nav2'] = $this->load->view('users/nav2',$data,true);
	        $template['page_title'] = "Add New Body Style";
			$template['bigheader'] = "Add New Body Style";
	
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	
			
			$this->load->view('global/maintemplate', $template);
		
		}
  
    }
    
    
    function save() {
	
    	
    	if (checkPerms('can_manage_bodystyles',true)) {
    	
    		$errors = "";
    	
			$postdata['bodystyleid'] = $this->input->post('bodystyleid');
			$postdata['categoryid'] = $this->input->post('categoryid');
			$postdata['code'] = $this->input->post('code');
						

			if (!$postdata['bodystyle'] = $this->input->post('bodystyle'))
				$errors .= "bodystyle Must have a Name!<br />";
			
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
			
			if ($bodystyleid = $this->bodystyles_model->saveBodystyle($postdata)) {
			
				
				$this->opm->displayAlert("Body Style Has Been Saved!","/bodystyles/search/");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the Body Style!");
				return true;
			}
		
		}
				
	}
	
}
//
?>