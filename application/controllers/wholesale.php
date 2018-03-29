<?php

class Wholesale extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('orders_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'wholesale';
    	
    	global $searchArgList;
    	    	
    	$searchArgList = array("customerid","statusid","customerpo","orderBy","orderByAscDesc","perPage","offset");
    	
    }
    
    function index() {
    
    	redirect("/wholesale/searchOrders");
    
    }
	
	function searchOrders($customerid = 0, $statusid = 0, $customerpo = 0, $orderBy = "id", $orderByAscDesc = "asc", $perPage = 0, $offset = 0) {
 
    	global $searchArgList;

    	
    	foreach ($searchArgList as $k=>$d) 
    		$data['args'][$d] = ${$d};   	
	
		
		//$template['rightNav'] = $this->load->view('invoices/rightNav',$data,true);
   		$template['page_title'] = "Search Orders";
   		$template['bigheader'] = "Search Orders";
	    $template['nav2'] = $this->load->view('wholesale/nav2',$data,true);

    	$data['totalOrders'] = $this->orders_model->fetchOrders(true,null,null,$customerid,$statusid,$customerpo,$orderBy,$orderByAscDesc);
    	
    	/*if ($propertyid == null) { // this is here because if base_url recieves a null, it leaves it off and messes up the pagination!
    	
    		$baseurlPropID = 0;
    		
    	} else {
    	
    		$baseurlPropID = $propertyid;
    	
    	}*/
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/wholesale/searchOrders/'.$customerid."/".$statusid."/".$customerpo."/".$orderBy."/".$orderByAscDesc."/".$perPage."/";
		$config['total_rows'] = $data['totalOrders'];
		
		// determine per page
		
		if ($perPage)
			$config['per_page'] = $perPage;
		else
			$config['per_page'] = $this->config->item('searchPerPage');


		$config['uri_segment'] = 16;


    	$this->pagination->initialize($config);
    					
    	$data['orders'] = $this->orders_model->fetchOrders(false,$offset,$config['per_page'],$customerid,$statusid,$customerpo,$orderBy,$orderByAscDesc);
	   /*	$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'));	
    	$data['users'] = $this->users_model->fetchInvoiceUsers();
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['statuses'] = $this->invoices_model->fetchStatuses();
    	$data['companies'] = $this->companies_model->fetchCompanies();*/
    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['orders']->num_rows() - 1);
    	
    	//$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
    	$template['searchArea'] = $this->load->view('orders/searchArea',$data,true);
    	$template['contentNav'] = $this->load->view('orders/searchNav',$data,true);
    	$template['content'] = $this->load->view('orders/search',$data,true);
    	
    	$arrJS['scripts'] = array('jquery-1.9.1','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    function products() {
 
    	global $searchArgList;

    	
    	foreach ($searchArgList as $k=>$d) 
    		$data['args'][$d] = ${$d};   	
	
		
		//$template['rightNav'] = $this->load->view('invoices/rightNav',$data,true);
   		$template['page_title'] = "Search Orders";
   		$template['bigheader'] = "Search Orders";
	    $template['nav2'] = $this->load->view('wholesale/nav2',$data,true);

    	$data['totalOrders'] = $this->orders_model->fetchOrders(true,null,null,$customerid,$statusid,$customerpo,$orderBy,$orderByAscDesc);
    	
    	/*if ($propertyid == null) { // this is here because if base_url recieves a null, it leaves it off and messes up the pagination!
    	
    		$baseurlPropID = 0;
    		
    	} else {
    	
    		$baseurlPropID = $propertyid;
    	
    	}*/
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/wholesale/searchOrders/'.$customerid."/".$statusid."/".$customerpo."/".$orderBy."/".$orderByAscDesc."/".$perPage."/";
		$config['total_rows'] = $data['totalOrders'];
		
		// determine per page
		
		if ($perPage)
			$config['per_page'] = $perPage;
		else
			$config['per_page'] = $this->config->item('searchPerPage');


		$config['uri_segment'] = 16;


    	$this->pagination->initialize($config);
    					
    	$data['orders'] = $this->orders_model->fetchOrders(false,$offset,$config['per_page'],$customerid,$statusid,$customerpo,$orderBy,$orderByAscDesc);
	   /*	$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'));	
    	$data['users'] = $this->users_model->fetchInvoiceUsers();
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['statuses'] = $this->invoices_model->fetchStatuses();
    	$data['companies'] = $this->companies_model->fetchCompanies();*/
    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['orders']->num_rows() - 1);
    	
    	//$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
    	$template['searchArea'] = $this->load->view('orders/searchArea',$data,true);
    	$template['contentNav'] = $this->load->view('orders/searchNav',$data,true);
    	$template['content'] = $this->load->view('orders/search',$data,true);
    	
    	$arrJS['scripts'] = array('jquery-1.9.1','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    
    
}


?>