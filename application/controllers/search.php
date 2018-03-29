<?php
class Search extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->opm->activeNav = 'products';
    	
    	global $searchArgList;
    	
    	$searchArgList = array("propertyid","productlineid","categoryid","usergroupid","approvalstatusid","sampappstatusid","designerid","creatorid","territoryid","filmlocations","filename","searchtext","productcode","orderBy","orderByAscDesc","perPage","offset","exportExcel","quickSearch");
    
    	if ($this->config->item('debugMode') == true || $this->userinfo->userid == 1 || $this->userinfo->userid == 307)
    		$this->output->enable_profiler(TRUE);
    }
    
    function index() {
    
    	redirect('/search/doSearch/');
    
    }
    
    function doSearch($propertyid = null,$productlineid = 0, $categoryid = 0, $usergroupid = 0, $approvalstatusid = '0', $sampappstatusid = '0', $designerid = 0, $creatorid = 0, $territoryid = '0', $filmlocations = '0', $filename = 0, $searchtext = 0, $productcode = 0,  $orderBy = "id", $orderByAscDesc = DEFAULT_SORT_DIRECTION, $perPage = 0, $offset = 0, $exportExcel = 0, $quickSearch = 0) {
 
    	global $searchArgList;
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->model('productlines_model');
		$this->load->model('categories_model');
		$this->load->model('approvalstatus_model');
		$this->load->model('territories_model');
		
		if ($orderByAscDesc != 'asc' && $orderByAscDesc != 'desc') {	
		
			$orderByAscDesc = DEFAULT_SORT_DIRECTION;
		
		}
		
		
		/*if (!checkPerms('can_view_unapproved_products'))
			$approvalstatusid = "1,2"; */
			
			
		$approvalStatusIDs = array();
		
		if (!checkPerms('can_view_unapproved_products')) {
		
			$approvalStatusIDs[] = "1";
			$approvalStatusIDs[] = "2";
			
			if (checkPerms('can_view_rejected_products')) {
			
				$approvalStatusIDs[] = "3";
			
			}
			
			if (checkPerms('can_view_awaiting_revisions_products')) {


				$approvalStatusIDs[] = "6";				
			
			}

		
		}
		
		
		
		if ($approvalStatusIDs) {
			
			$approvalstatusid = implode(",", $approvalStatusIDs);
			
		}	
			
		
		if (isset($this->userinfo->approvalProperties[0])) {
		
			// user is approval contact! default search page to first approval property. (greatly increases query speed);
		
			if ($propertyid == null) {
			
				$propertyid = $this->userinfo->approvalProperties[0];
			
			} 
		
		}
		
		if ($propertyid == null)
			$propertyid = 0;
			
		// url decode search strs
		
		$searchtext = urldecode($searchtext);
		$filename = urldecode($filename);
    	
    	foreach ($searchArgList as $k=>$d)
    		$data['args'][$d] = ${$d};   	
   
   		$template['page_title'] = "Search Products";
   		$template['bigheader'] = "Search Products";
    	$template['nav2'] = "Search Products";
    
    	$data['isWholesaleSearch'] = false;
    	
    	$data['totalProducts'] = $this->products_model->fetchProducts(true,null,null,$propertyid,$productlineid,$categoryid,$usergroupid,$approvalstatusid,$sampappstatusid,$searchtext,$productcode,false,$designerid,$creatorid,$territoryid,$filmlocations,$filename);
    
    	if ($propertyid == null) { // this is here because if base_url recieves a null, it leaves it off and messes up the pagination!
    	
    		$baseurlPropID = 0;
    		
    	} else {
    	
    		$baseurlPropID = $propertyid;
    	
    	}
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/search/doSearch/'.$baseurlPropID."/".$productlineid."/".$categoryid."/".$usergroupid."/".$approvalstatusid."/".$sampappstatusid."/".$designerid."/".$creatorid."/".$territoryid."/".$filmlocations."/".$filename."/".$searchtext."/".$productcode."/".$orderBy."/".$orderByAscDesc."/".$perPage."/";
		$config['total_rows'] = $data['totalProducts'];
		
		// determine per page
		
		if ($perPage)
			$config['per_page'] = $perPage;
		else
			$config['per_page'] = $this->config->item('searchPerPage');
		
		$config['uri_segment'] = 19;


    	$this->pagination->initialize($config);
    	
    	
    	if ($exportExcel) {
    		
    		$products = $this->products_model->fetchProducts(false,null,null,$propertyid,$productlineid,$categoryid,$usergroupid,$approvalstatusid,$sampappstatusid,$searchtext,$productcode,false,$designerid,$creatorid,$territoryid,$filmlocations,$filename);
    		$this->opm->createProductExcel($products);
			exit();
			
    	} else {
    	
    		$data['products'] = $this->products_model->fetchProducts(false,$offset,$config['per_page'],$propertyid,$productlineid,$categoryid,$usergroupid,$approvalstatusid,$sampappstatusid,$searchtext,$productcode,false,$designerid,$creatorid,$territoryid,$filmlocations,$filename,$orderBy,$orderByAscDesc);

    	}
    	
    	$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
    	$data['designers'] = $this->users_model->fetchDesigners(true,null,true,true);
    	$data['creators'] = $this->products_model->fetchProductCreators(true);
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['categories'] = $this->categories_model->fetchCategories();
    	$data['approvalStatuses'] = $this->approvalstatus_model->fetchApprovalStatuses();
    	$data['territories'] = $this->territories_model->fetchTerritories();
    	$data['licensees'] = $this->usergroups_model->fetchLicensees(false);
    	
    	if ($propertyid) // if we have a property id, get product lines.
    		$data['productLines'] = $this->productlines_model->fetchProductLines($propertyid);
    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['products']->num_rows() - 1);
    	
    	$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
    	
    	$data['showSort'] = true; // show sort on top of page!
    	$template['searchArea'] = $this->load->view('search/searchArea',$data,true);
    	$template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	$data['contentNavOmitTitle'] = true; // flag the template so that "showing products..." doesn't show up on the btm.
    	
    	$data['showSort'] = false; // don't show sort on bottom of page!
    	$template['contentNav2'] = $this->load->view('search/searchNav',$data,true);
    	$template['content'] = $this->load->view('search/search',$data,true);
    	
    	$arrJS['scripts'] = array('jquery-1.9.1','opm_scripts','chosen.jquery.min','jquery-ui-1.10.3.custom.min'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    function doWSSearch($propertyid = null,$productlineid = 0, $categoryid = 0, $usergroupid = 0, $approvalstatusid = '0', $sampappstatusid = '0', $designerid = 0, $creatorid = 0, $territoryid = '0', $filmlocations = '0', $filename = 0, $searchtext = 0, $productcode = 0,  $orderBy = "id", $orderByAscDesc = "asc", $perPage = 0, $offset = 0, $exportExcel = 0, $quickSearch = 0) {
 
    	global $searchArgList;
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->model('productlines_model');
		$this->load->model('categories_model');
		$this->load->model('approvalstatus_model');
		$this->load->model('territories_model');
		
		if (!checkPerms('can_view_unapproved_products'))
			$approvalstatusid = "1,2";
		
		if (isset($this->userinfo->approvalProperties[0])) {
		
			// user is approval contact! default search page to first approval property. (greatly increases query speed);
		
			if ($propertyid == null) {
			
				$propertyid = $this->userinfo->approvalProperties[0];
			
			} 
		
		}
		
		if ($propertyid == null)
			$propertyid = 0;
			
		// url decode search strs
		
		$searchtext = urldecode($searchtext);
		$filename = urldecode($filename);
    	
    	foreach ($searchArgList as $k=>$d)
    		$data['args'][$d] = ${$d};   	
   
   		$template['page_title'] = "Search Products";
   		$template['bigheader'] = "Search Products";
    	$template['nav2'] = "Search Products";
    	
    	$data['isWholesaleSearch'] = true;
    	
    	$data['totalProducts'] = $this->products_model->fetchProducts(true,null,null,$propertyid,$productlineid,$categoryid,$usergroupid,$approvalstatusid,$sampappstatusid,$searchtext,$productcode,false,$designerid,$creatorid,$territoryid,$filmlocations,$filename);
    
    	if ($propertyid == null) { // this is here because if base_url recieves a null, it leaves it off and messes up the pagination!
    	
    		$baseurlPropID = 0;
    		
    	} else {
    	
    		$baseurlPropID = $propertyid;
    	
    	}
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/search/doWSSearch/'.$baseurlPropID."/".$productlineid."/".$categoryid."/".$usergroupid."/".$approvalstatusid."/".$sampappstatusid."/".$designerid."/".$creatorid."/".$territoryid."/".$filmlocations."/".$filename."/".$searchtext."/".$productcode."/".$orderBy."/".$orderByAscDesc."/".$perPage."/";
		$config['total_rows'] = $data['totalProducts'];
		
		// determine per page
		
		if ($perPage)
			$config['per_page'] = $perPage;
		else
			$config['per_page'] = $this->config->item('searchPerPage');
		
		$config['uri_segment'] = 19;


    	$this->pagination->initialize($config);
    	
    	
    	if ($exportExcel) {
    		
    		$products = $this->products_model->fetchProducts(false,null,null,$propertyid,$productlineid,$categoryid,$usergroupid,$approvalstatusid,$sampappstatusid,$searchtext,$productcode,false,$designerid,$creatorid,$territoryid,$filmlocations,$filename);
    		$this->opm->createProductExcel($products);
			exit();
			
    	} else {
    	
    		$data['products'] = $this->products_model->fetchProducts(false,$offset,$config['per_page'],$propertyid,$productlineid,$categoryid,$usergroupid,$approvalstatusid,$sampappstatusid,$searchtext,$productcode,false,$designerid,$creatorid,$territoryid,$filmlocations,$filename,$orderBy,$orderByAscDesc);

    	}
    	
    	$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
    	$data['designers'] = $this->users_model->fetchDesigners(true,null,true,true);
    	$data['creators'] = $this->products_model->fetchProductCreators(true);
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['categories'] = $this->categories_model->fetchCategories();
    	$data['approvalStatuses'] = $this->approvalstatus_model->fetchApprovalStatuses();
    	$data['territories'] = $this->territories_model->fetchTerritories();
    	$data['licensees'] = $this->usergroups_model->fetchLicensees(false);
    	
    	if ($propertyid) // if we have a property id, get product lines.
    		$data['productLines'] = $this->productlines_model->fetchProductLines($propertyid);
    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['products']->num_rows() - 1);
    	
    	$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
    	
    	$data['showSort'] = true; // show sort on top of page!
    	$template['searchArea'] = $this->load->view('search/searchArea',$data,true);
    	$template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	$data['contentNavOmitTitle'] = true; // flag the template so that "showing products..." doesn't show up on the btm.
    	
    	$data['showSort'] = false; // don't show sort on bottom of page!
    	$template['contentNav2'] = $this->load->view('search/searchNav',$data,true);
    	$template['content'] = $this->load->view('search/wsSearch',$data,true);
    	
    	$arrJS['scripts'] = array('jquery-1.9.1','opm_scripts','chosen.jquery.min','jquery-ui-1.10.3.custom.min'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    
    function quickSearch($searchtext,$offset = 0) {

    	global $searchArgList;
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->model('productlines_model');
		$this->load->model('categories_model');
		$this->load->model('approvalstatus_model');
    		
    	$searchtext = urldecode($searchtext);
   
   		$template['isQuickSearch'] = true;
   		$data['isQuickSearch'] = true;
   		$template['searchtext'] = $searchtext;
   
   		$template['page_title'] = "Quick Search - " . $searchtext;
   		$template['bigheader'] = "Quick Search - " . $searchtext;
    	$template['nav2'] = "Quick Search - " . $searchtext;
    	
    	$data['totalProducts'] = $this->products_model->fetchProducts(true,null,null,0,0,0,0,0,0,$searchtext,0,true);
    	
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/search/quickSearch/'.$searchtext."/";
		$config['total_rows'] = $data['totalProducts'];
		$config['per_page'] = '20';
		$config['uri_segment'] = 4;


    	$this->pagination->initialize($config);
    	
    	$data['products'] = $this->products_model->fetchProducts(false,$offset,$config['per_page'],0,0,0,0,0,0,$searchtext,0,true);
    	
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['categories'] = $this->categories_model->fetchCategories();
    	$data['approvalStatuses'] = $this->approvalstatus_model->fetchApprovalStatuses();

    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['products']->num_rows() - 1);
    	
    	$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
    	$template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	$data['contentNavOmitTitle'] = true;  // flag the template so that "showing products..." doesn't show up on the btm.
    	$template['contentNav2'] = $this->load->view('search/searchNav',$data,true);
    	$template['content'] = $this->load->view('search/search',$data,true);
    	
    	$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','tipsx3'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    function myApprovals($propertyid = 0,$offset = 0) {

    	global $searchArgList;
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->model('productlines_model');
		$this->load->model('categories_model');
		$this->load->model('approvalstatus_model');
    	
   
   		$template['page_title'] = "My Pending Approvals";
   		$template['bigheader'] = "My Pending Approvals";
    	$template['nav2'] = "My Pending Approvals";
    	
    	$data['totalProducts'] = $this->products_model->fetchPendingApprovalProducts($this->userinfo->userid,true);
    	
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/search/myApprovals/'.$propertyid."/";
		$config['total_rows'] = $data['totalProducts'];
		$config['per_page'] = '20';
		$config['uri_segment'] = 4;


    	$this->pagination->initialize($config);
    	
    	$data['products'] = $this->products_model->fetchPendingApprovalProducts($this->userinfo->userid,false,$offset,$config['per_page']);
    	
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['categories'] = $this->categories_model->fetchCategories();
    	$data['approvalStatuses'] = $this->approvalstatus_model->fetchApprovalStatuses();
    	
    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['products']->num_rows() - 1);
    	
    	$template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	$template['content'] = $this->load->view('search/search',$data,true);
    	
    	$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','tipsx3'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    
    
    function submit() { // construct a search url from a form submission
		
		//print_r($_POST);
		//die();


    	global $searchArgList;
    	
    	if($this->input->post('searchQuery')) { // this is a quick search

    		redirect("/search/quickSearch/".$this->input->post('searchQuery'));
   	
    	
    	} else if($this->input->post('opmid')) { // redirect to OPM ID

    		
    		redirect("/products/view/".$this->input->post('opmid'));
   	
    	
    	} else {
    	    	
    
			foreach ($searchArgList as $key=>$data) {
				
				if ($this->input->post($data))
					$segments[$data] = $this->input->post($data);
				else
					$segments[$data] = 0;	
				
			}
			
			if ($this->input->post('isWholesaleSearch')) {
			
				$url = "/search/doWSSearch/";
				
			} else {
				
				$url = "/search/doSearch/";
			
			}
			
			foreach ($segments as $data)
				$url .= $data . "/";
				
			
			redirect($url);
			
		}
    
    }
    
}
?>