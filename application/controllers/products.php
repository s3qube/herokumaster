<?php
class Products extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'products';
    	
    	if ($this->config->item('debugMode') == true)
    		$this->output->enable_profiler(TRUE);
    		
    }
    
    function view($id,$tabname = 'summary')
   	{
   	
   		// make sure we have permission to view this product!
   		$this->opm->checkProductViewPerms($id);
   	
    	if ($tabname)
    		$data['tabname'] = $tabname;
    	else
    		$data['tabname'] = 'summary';	
    
    	$data['product'] = $this->products_model->fetchProductInfo($id);
    	$data['bcDrop'] = $this->products_model->fetchProducts(false,null,null,$data['product']->propertyid,0,$data['product']->categoryid);
     	
     	// determine if we show "new comments since last view", etc.
     
     	if ($data['product']->latestForum) { // make sure there actually is a latest forum entry
     		
     		if ($data['product']->latestForum->timestamp > $data['product']->lastview)
     			$data['newComments'] = true;
     	
     	}
     	
     	
     	if($data['product']->latestHistory) { // make sure there actually is a latest history entry
     	
     		if ($data['product']->latestHistory->timestamp > $data['product']->lastview)
     			$data['newHistory'] = true;
     	
     	}
     	
     
     	$template['content'] = '';
     	
     	$template['headInclude'] = $this->load->view('product/loadContentJS',$data,true);
       
        $template['page_title'] = "View Product - " . $data['product']->property . " - " . $data['product']->productname;
        $template['bigheader'] = $this->load->view('bigheader/product',$data,true);
        $template['nav2'] = $this->load->view('product/breadcrumbs',$data,true);
        $template['contentNav'] = $this->load->view('product/contentNav',$data,true);
        $template['rightNav'] = $this->load->view('product/rightNav',$data,true);
        
       	$arrJS['scripts'] = array('jquery-1.6.2.min','opm_scripts','shadowbox3','dropdown','chosen.jquery.min','tinymce3/tiny_mce','plupload','jquery.plupload.queue','plupload.html5','datepicker_jq','eye','utils','layout');
        //$arrJS['scripts'] = array('jquery-1.3.2.min','opm_scripts');
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        // insert view record
       	
       	$this->opm->setViewTimestamp($id);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function regenerateSkus() {
    
    	if (checkPerms('can_build_skus',true)) {
    
	    	if (!$opid = $this->input->post("opm_productid")) {
	    	
	    		$this->opm->displayError("No Product ID Found!");
				return false;
			
			}
	    	
	    	$this->opm->buildSkusForProduct($opid);
	    	
	    	$this->opm->displayAlert("SKUs regenerated!","/products/view/" . $opid . "/billing");

 		}
    
    }
    
   function productXML() {
   
   		$this->output->enable_profiler(FALSE);
	
		header ("Content-Type:text/xml"); 
	
		if (checkPerms('can_export_products',true)) {
		
			$prods = $this->opm->fetchProductsForExport();
			
			$xmlProducts = ""; 
			
			foreach ($prods as $p) {
			
				//$data['p'] = $this->products_model->fetchProductInfo($p->opm_productid);
				
				$data['p'] = $p;
				$xmlProducts .= $this->load->view('xml/product',$data,true);
				
			}
			
			echo "<Products>";
    			echo $xmlProducts;
    		echo "</Products>";
			
			
		
		}
	
	}
    
    function purchaseDialog($opm_productid,$accountid) {
    
    	$this->output->enable_profiler(FALSE);
    
    	// figure out what widths are avail.
    	$this->load->model('accounts_model');
    	
    	$data['account'] = $this->accounts_model->fetchAccount($accountid);
    	$data['purchaseTypes'] = $this->accounts_model->fetchPurchaseTypes();
    	$data['opm_productid'] = $opm_productid;
    	$data['accountid'] = $accountid;

		$data['mode'] = 'entry';

    	$this->load->view('product/purchaseDialog',$data);
    
    }
    
    function saveSampleDates() {
	    
	   /* echo "<pre>";
	    print_r($_POST);
	    die();*/
	    
	    $data = $this->input->post();
	    
	    
	    if (isset($data['samplesentdate'])) {
		    
		    $arrDate = explode("/", $data['samplesentdate']);
		    
		    $data['tsSentDate'] = mktime(0, 0, 0, $arrDate[0], $arrDate[1], $arrDate[2]);
		    
		   
	    }
	    
	    if (isset($data['samplerecdate'])) {
		    
		    $arrDate = explode("/", $data['samplerecdate']);
		    
		    $data['tsRecDate'] = mktime(0, 0, 0, $arrDate[0], $arrDate[1], $arrDate[2]);
		    
	    }
	    
	    if ($returnDate = $this->products_model->saveSampleDates($data)) {
	    
	    	if (isset($data['samplesentdate'])) {
		    	
		    	$message = "Sample Sent Date changed to " . $data['samplesentdate'] . " by " . $this->userinfo->username;
		    	$this->opm->addHistoryItem($data['opm_productid'], $message);
		    	
	    	}
	    	
	    	if (isset($data['samplerecdate'])) {
		    	
		    	
		    	$message = "Sample Received Date changed to " . $data['samplerecdate'] . " by " . $this->userinfo->username;
		    	$this->opm->addHistoryItem($data['opm_productid'], $message);
		    	
		    	
	    	}
	    
	    	die ($returnDate);
	    
	    } else {
		    
		    die("ERROR");
		    
	    }
	    	    
	    
	    
	    
	    
    }
    
    function saveSampleNotes() {
	    
	   /* echo "<pre>";
	    print_r($_POST);
	    die();*/
	    
	    
	    
	    $data = $this->input->post();
	    	 	    
	    if ($this->products_model->saveSampleNotes($data)) {
	    
	    	die(nl2br($data['notes']));	 	    
	    	 	    
	    } else {
		    
		    die("ERROR");
		    
	    }
	    	    
	    
	    
    }
    
    /*
    function savexxPurchase() {
    
    	$this->output->enable_profiler(FALSE);
    
    	$this->load->model('accounts_model');
		
		$postdata['opm_productid'] = $this->input->post('opm_productid');
		$postdata['accountid'] = $this->input->post('accountid');
		$postdata['purchasetypeid'] = $this->input->post('purchasetypeid');    
		$postdata['enddate'] = $this->input->post('enddate');    
		$postdata['isexclusive'] = $this->input->post('isexclusive');
		
		if ($postdata['enddate']) {
			
				$splitDate = explode("-", $postdata['enddate']);
				
				if (is_array($splitDate))
					$postdata['enddate'] = @mktime(1,0,0,$splitDate[0],$splitDate[1],$splitDate[2]);
			
		}
		
		
		if ($data['account'] = $this->accounts_model->savePurchase($postdata)) {
		
			// record history entry
			
			if ($postdata['isexclusive'] == 1)
	 			$exclusiveText = "(EXCLUSIVE)";
	 		else
	 			$exclusiveText = "(NON-EXCLUSIVE)";
	 			
	 		// get purchase type text
	 		
	 		$pt = $this->accounts_model->fetchPurchaseType($postdata['purchasetypeid']);
			
			// get account info
			
			$account = $this->accounts_model->fetchAccount($postdata['accountid']);
			
			$message = "Product ".$pt->pt_pasttense." by ".$account->account. " " . $exclusiveText;
			
			$this->opm->addHistoryItem($postdata['opm_productid'],$message);
			
			$data['mode'] = "redirect"; // tells the view to redirect parent page
			$this->load->view('product/purchaseDialog',$data);
		
		} else {
		
			$this->opm->displayError("Purchase could not be saved");
			return false;
		
		}
		
		
		
		    
		
    }
    
    */
    
    // 20111217 mark
    function savePurchase() {
    
    	$this->output->enable_profiler(FALSE);
    
    	$this->load->model('accounts_model');

    	// sanitize input
    	$postdata['opm_productid'] = intval($this->input->post('opm_productid'));
		$postdata['accountid'] = intval($this->input->post('accountid'));
		$postdata['purchasetypeid'] = intval($this->input->post('purchasetypeid'));
		
		$postdata['isexclusive'] = $this->input->post('isexclusive');
		
		$postdata['enddate'] = mysql_real_escape_string($this->input->post('enddate'));
				
		// if we have an enddate, convert it to a unix timestamp
		if(!empty($postdata['enddate'])) {

			// convert dashes to slashes for strtotime
			$postdata['enddate'] = str_replace('-', '/', $postdata['enddate']);
			
			// the unix epoch is always in utc
			$timezone_before = date_default_timezone_get();
			date_default_timezone_set('UTC');
			
			if($unixtime = strtotime($postdata['enddate'])) {
				
				$postdata['enddate'] = $unixtime;
			}
			
			else {
				
				$data['errors'][] = "Invalid end date.";
			}
			
			// back to whatever tz
			date_default_timezone_set($timezone_before);
		}

		else {
			
			$data['errors'][] = 'End date required.';
		}

		// if there was no problem with the enddate, 
		if(!isset($data['errors'])) {
		
			// try saving the purchase.
			if($data['account'] = $this->accounts_model->savePurchase($postdata)) {
			
				// purchase saved ok because it did not return false
			
				// record history entry			
				if($postdata['isexclusive'] == 1) {
					
					$exclusiveText = '(EXCLUSIVE)';
				}
		 		
				else {
		 			
					$exclusiveText = '(NON-EXCLUSIVE)';
				}
		 		
				// get purchase type text
		 		$pt = $this->accounts_model->fetchPurchaseType($postdata['purchasetypeid']);
				
				// get account info
				$account = $this->accounts_model->fetchAccount($postdata['accountid']);
	
				// form message for the history
				$message = strtoupper($pt->pt_pasttense) . " by $account->account $exclusiveText set up by " . $this->userinfo->username;
	
				// add the history message
				$this->opm->addHistoryItem($postdata['opm_productid'], $message);
				
				// tells the view to redirect parent page
				$data['mode'] = 'redirect'; 
			}
			
			else {

				// tells the purchase dialog that the save didn't work
				$data['errors'][] = 'Purchase could not be saved (accounts_model->savePurchase returned false)';
			}
		}

		if(isset($data['errors'])) {
			
			// tells the view to outline problems with the input
			$data['mode'] = 'error';
			
			// send along necessary info to redo the dialog
			$data['account'] = $this->accounts_model->fetchAccount($postdata['accountid']);
			$data['purchaseTypes'] = $this->accounts_model->fetchPurchaseTypes();
			$data['opm_productid'] = $postdata['opm_productid'];
			$data['accountid'] = $postdata['accountid'];
		}
		
		// we're headed back to the purchase dialog no matter what
		$this->load->view('product/purchaseDialog', $data);
    }
    // 20111217 mark
    
    // 20111220 mark
    function releasePurchase() {

    	$this->output->enable_profiler(FALSE);
    
    	$this->load->model('accounts_model');
    	
    	// ensure we have a purchase id (opm_products_accounts.id)
    	$postdata['id'] = intval($this->input->post('id'));

		header("Content-type: application/json");
		
    	if(!isset($postdata['id']) || empty($postdata['id']) || !is_int($postdata['id'])) {

    		// we don't need to provide any detailed error messages. just don't update the row if
    		// things aren't right.
    		$for_json['release_response']['type'] = 'error';
    		$for_json['release_response']['message'] = 'Purchase ID required.';
    		exit(json_encode($for_json));
    	}
    	
    	// open this purchase
    	$purchase = $this->accounts_model->fetchPurchase($postdata);

    	if(!isset($purchase) || !$purchase || empty($purchase)) {
    		
    		$for_json['release_response']['type'] = 'error';
    		$for_json['release_response']['message'] = 'Invalid purchase ID.';
    		exit(json_encode($for_json));
    	}
    	
    	// try releasing. this, as tim writes, should just set the enddate timestamp to the current time
    	
    	if ($enddate = $this->accounts_model->releasePurchase($postdata)) {

    		// releasePurchase returned true

    		// form message about the release for the product's history
    		
    		if ($purchase->purchasetypeid != 0) {
 	 		
    			$pt = $this->accounts_model->fetchPurchaseType($purchase->purchasetypeid);
 	 			$pt_type = $pt->purchasetype;
    		
    		} else {
    		
    			$pt_type = "hold/purchase/re-order";
    		
    		}
	    	
    		$account = $this->accounts_model->fetchAccount($purchase->account_id);    		
    		$message = "$pt_type by $account->account released by " . $this->userinfo->username;
    		
    		// add the history message
    		
    		$this->opm->addHistoryItem($purchase->opm_productid, $message);

    		// let the UI know that the release was successful and to update the purchase row
    		
    		$for_json['release_response']['type'] = 'success';
    		$for_json['release_response']['message'] = date('m-d-Y', $enddate);
    		
    		exit(json_encode($for_json));
    	
    	} else {

    		// let the UI know there was an error with the release. no row removal.
    		$for_json['release_response']['type'] = 'error';
    		$for_json['release_response']['message'] = 'There was an error releasing this purchase.';
    		exit(json_encode($for_json));
    	
    	}
   
    }
    // 20111220 mark
	
	
    
    function edit($id = 0)
   	{
   		
   		$this->load->model('articles_model');
   		$this->load->model('categories_model');
   		$this->load->model('bodystyles_model');
   		$this->load->model('productlines_model');
   		$this->load->model('designers_model');
   		$this->load->model('users_model');
   		
   		/*echo "<pre>";
		print_r($this->userinfo);   	
   		die();*/
   	
   		if (checkPerms('can_add_products_any_property')) {
   		
   			$data['properties'] = $this->properties_model->fetchProperties(false,true,false,null,0,null,null,true,false);
   		
   		} elseif ($this->userinfo->usePropAssignments) {
   			
   			$data['properties'] = $this->properties_model->fetchProperties(false,true,false,null,0,null,null,false,true);
   		
   		} else {
   		
   			$data['properties'] = $this->properties_model->fetchProperties(false,true);
   		
   		}
   		
   		$data['bodystyles'] = $this->bodystyles_model->fetchBodystyles();
   		$data['articles'] = $this->articles_model->fetchArticles();
   		$data['categories'] = categoryArray2Select($this->categories_model->fetchCategoriesArray());
   		//$data['categories'] = $this->categories_model->fetchCategories();    
    
    	if ($id != 0) { // we are in edit mode!
    	
    		$data['mode'] = "edit";	
    		$data['product'] = $this->products_model->fetchProductInfo($id);
    		$template['page_title'] = "Edit Product - " . $data['product']->property . " - " . $data['product']->productname;
    		$template['bigheader'] = $this->load->view('bigheader/product',$data,true);
    		$template['nav2'] = "Edit Product&nbsp;&nbsp;&gt;&nbsp;&nbsp;" . $data['product']->property . "&nbsp;&nbsp;&gt;&nbsp;&nbsp;" . $data['product']->productname;    		
    	
    		$data['productLines'] = $this->productlines_model->fetchProductLines($data['product']->propertyid,true,$data['product']->opm_productid);
    		$data['designers'] = $this->users_model->fetchDesigners(false,$data['product']->opm_productid);
			$data['licensees'] = $this->usergroups_model->fetchLicensees(false,$data['product']->opm_productid);
			
			if ($data['product']->islocked && !checkPerms('can_edit_locked_products')) {
				
				$this->opm->displayError("This product is locked and cannot be edited.");
				return true;
				
			}
			
    	} else {  // we are in add mode!
    	
    		$data['mode'] = "add";	
    		$data['product'] = new stdClass();
    		$data['product']->opm_productid = 0;
    		$data['product']->propertyid = 0;
    		$data['product']->categoryid = 0; 
    		$data['product']->bodystyleid = 0;
    		$data['product']->articleid = 0;
    		$data['product']->substageid = 0;
    		$data['product']->productname = ""; 
    		$data['product']->productcode = "";
    		$data['product']->licenseecode = "";
    		$data['product']->filmlocations = "";
    		$data['product']->filmnumber = "";
    		$data['product']->copyrightaddendums = "";
    		$data['product']->artworkcharges = "";
    		$data['product']->presentationstyles = "";
    		$data['product']->productdesc = "";
    		$data['product']->shortname = "";
    		$data['product']->duedate = 0;
    		$data['product']->numprints = "";
    		$data['product']->designcode_islocked = 0;
    		$data['product']->designcode = "";
    		$data['product']->numprints = "";
    		$data['productLines'] = 0;
    		
    		$template['page_title'] = "Add Product";
    		$template['bigheader'] = "Add New Product";
    		$template['nav2'] = "Add New Product";
    		
    		$data['designers'] = $this->users_model->fetchDesigners(false,0);
			$data['licensees'] = $this->usergroups_model->fetchLicensees(false,$data['product']->opm_productid);
    		$data['productLines'] = $this->productlines_model->fetchProductLines(0,true,$data['product']->opm_productid);

    	
    	}
    	     
     	
     	$template['content'] = $this->load->view('product/edit',$data,true);
       
        $arrJS['scripts'] = array('jquery-1.6.2.min','jquery.validate-1.8.1.min','opm_scripts','datepicker','chosen.jquery.min'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');
  	 	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
   	 	header('Cache-Control: post-check=0, pre-check=0',false);
    	session_cache_limiter('must-revalidate');
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function save() {
    	
    	/*echo "<pre>";
    	print_r($_POST);
    	die();*/
    	
    	// ARE WE LOCKED?
    	
    	$postdata['opm_productid'] = $this->input->post('opm_productid');
    	
    	// ARE WE LOCKED?
    	
    	if ($postdata['opm_productid']) { // make sure we are adding.
    	
	    	$data['product'] = $this->products_model->fetchProductInfo($postdata['opm_productid']);
	    	
	    	if ($data['product']->islocked && !checkPerms('can_edit_locked_products')) {
					
				$this->opm->displayError("This product is locked and cannot be edited.");
				return true;
				
			}
		
		}

    
    	$this->load->model('categories_model');
    	
    	if (checkPerms('can_delete_products') && $this->input->post('delete_product')) {
    	
    		if ($this->products_model->deleteProduct($this->input->post('opm_productid'))) {
				
				$message = "Product deleted by " . $this->userinfo->username;
			
				$this->opm->addHistoryItem($this->input->post('opm_productid'),$message);
			
				$this->opm->displayAlert("Product has been deleted!","/search/doSearch");
				return true;	
			
			}
    	
    	}
    
    	if (checkPerms('can_add_products') || checkPerms('can_edit_products')) {
   		
   			$postdata['designcode'] = $this->input->post('designcode');
   		
			$errors = "";
			
			if ($postdata['designcode'] && !is_numeric($postdata['designcode']))
				$errors .= "Navision Design Code must be a number!<br />";
				
			if (!$postdata['propertyid'] = $this->input->post('propertyid'))
				$errors .= "Product Must have a Property!<br />";
			
			if (!$postdata['productname'] = $this->input->post('productname')) 
				$errors .= "Product Must have a Name!<br />";
			
			if (checkPerms('prodEdit_can_choose_category')) {
			
				if (!$postdata['categoryid'] = $this->input->post('categoryid')) 
					$errors .= "Product Must have a Category!<br />";
				
			} else {
			
				if (!$postdata['opm_productid'])
					$postdata['categoryid'] = 0;
			
			}
			
			if (checkPerms('prodEdit_can_enter_pginfo')) {
			
				if (!$postdata['filmlocations'] = $this->input->post('filmlocations')) 
					$errors .= "Product Must have a Print + Garment Info!<br />";
			
			} else {
			
				$postdata['filmlocations'] = $this->input->post('filmlocations');
			
			}
			
			if (checkPerms('prodEdit_can_choose_product_line')) {
		
				if (!$postdata['productLineIDs'] = $this->input->post('productLineIDs')) 
					$errors .= "Product must have at least one product line!<br />";
			
			} else {
			
				$postdata['productLineIDs'] = $this->input->post('productLineIDs');
			
			}
			
			
			if (checkPerms('prodEdit_can_choose_product_line')) {

				
				if (!$postdata['numprints'] = $this->input->post('numprints')) {
				
					//if ($postdata['numprints'] != '0')
					//	$errors .= "# of prints is required and must be a number!<br />";
				
				}
			
			} else {
			
				$postdata['numprints'] = $this->input->post('numprints');
			
			}
			
			// we need to make sure we are adding when we run below
			/*$checkDuplicate = $this->products_model->checkForDuplicateProducts($postdata['productname'],$postdata['propertyid']);
			
			if ($checkDuplicate > 0) {
			
				$errors .= "Product name is already taken!<br />";
			
			}	
			
			*/
			
			if ($postdata['designcode']) { // make sure design code is not duplicate!
				
				/*if ($this->products_model->checkIfDuplicateDesignCode($postdata['designcode'],$postdata['opm_productid'],$postdata['propertyid'])) {
				
					$errors .= "Design code is already in use for this property<br />";
				
				}*/
			
			}
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
	
			$postdata['productcode'] = $this->input->post('productcode');
			$postdata['licenseecode'] = $this->input->post('licenseecode');
			$postdata['bodystyleid'] = $this->input->post('bodystyleid');
			$postdata['filmlocations'] = $this->input->post('filmlocations');
			$postdata['filmnumber'] = $this->input->post('filmnumber');
			$postdata['artworkcharges'] = $this->input->post('artworkcharges');
			$postdata['presentationstyles'] = $this->input->post('presentationstyles');
			$postdata['copyrightaddendums'] = $this->input->post('copyrightaddendums');
			$postdata['designerIDs'] = $this->input->post('designerIDs');
			$postdata['licenseeIDs'] = $this->input->post('licenseeIDs');
			$postdata['duedate'] = $this->input->post('duedate');
			$postdata['productdesc'] = $this->input->post('productdesc');
			$postdata['shortname'] = $this->input->post('shortname');
			$postdata['numprints'] = $this->input->post('numprints');
			
			// convert duedate to a timestamp
			
			if ($postdata['duedate']) {
			
				$splitDate = explode("-", $postdata['duedate']);
				
				if (is_array($splitDate))
					$postdata['duedate'] = @mktime(1,0,0,$splitDate[0],$splitDate[1],$splitDate[2]);
			
			}
			
			// get pre-save info about product, to determine if any changes were made
			
			if ($postdata['opm_productid']) { // make sure we are editing, not adding.
			
				$pi = get_object_vars($this->products_model->fetchProductInfo($postdata['opm_productid'],true));
				
				// fetch new names of prop and cat, so we can compare...
				
				$property = $this->properties_model->fetchPropertyInfo($postdata['propertyid']);
				$postdata['property'] = $property->property;
				
				if (checkPerms('prodEdit_can_choose_category')) {
						
					$category = $this->categories_model->fetchCategory($postdata['categoryid']);
					$postdata['category'] = $category->category;
				
				}
	
				$npi = $postdata;
				
				// if we don't have permission to change a certain var, fill data with existing prod info, and remove var so changes aren't documented...
				
				if (!checkPerms('can_edit_designcode')) {
					$postdata['designcode'] = $pi['designcode'];
					unset($npi['designcode']);
				}
					
				if (!checkPerms('prodEdit_can_edit_productcode')) {
					$postdata['productcode'] = $pi['productcode'];
					unset($npi['productcode']);
				}	
				
				if (!checkPerms('prodEdit_can_edit_licenseecode')) {
					$postdata['licenseecode'] = $pi['licenseecode'];
					unset($npi['licenseecode']);
				}	
				
				if (!checkPerms('prodEdit_can_choose_bodystyle')) {
					$postdata['bodystyleid'] = $pi['bodystyleid'];
					unset($npi['bodystyleid']);
				}
					
				if (!checkPerms('prodEdit_can_enter_pginfo')) {
					$postdata['filmlocations'] = $pi['filmlocations'];
					unset($npi['filmlocations']);
				}
				
				if (!checkPerms('prodEdit_can_enter_due_date')) {
					$postdata['duedate'] = $pi['duedate'];
					unset($npi['duedate']);
				}
								
				if (!checkPerms('prodEdit_can_enter_numprints')) {
					$postdata['numprints'] = $pi['numprints'];
					unset($npi['numprints']);
				}
				
				if (!checkPerms('prodEdit_can_enter_filmnumber')) {
					$postdata['filmnumber'] = $pi['filmnumber'];
					unset($npi['filmnumber']);
				}
					
				if (!checkPerms('prodEdit_can_enter_artwork_charges')) {
					$postdata['artworkcharges'] = $pi['artworkcharges'];
					unset($npi['artworkcharges']);
				}
					
				if (!checkPerms('prodEdit_can_enter_presentation_styles')) {
					$postdata['presentationstyles'] = $pi['presentationstyles'];
					unset($npi['presentationstyles']);
				}
					
				if (!checkPerms('prodEdit_can_enter_cr_addendums')) {
					$postdata['copyrightaddendums'] = $pi['copyrightaddendums'];
					unset($npi['copyrightaddendums']);
				
				}
					
					
				if (!checkPerms('prodEdit_can_choose_category')) {
					$postdata['categoryid'] = $pi['categoryid'];
				}
				
				// unset useless vars
				
				
				unset($pi['opm_productid'],$pi['old_productlineid'],$pi['statusid'],$pi['designerid'],$pi['isactive'],$pi['design_completed'],$pi['verballyapproved'],$pi['createdby'],$pi['lastmodified'],$pi['default_imageid'],$pi['id'],$pi['timestamp'],$pi['approvalstatus'],$pi['propertyid'],$pi['approval_methodid'],$pi['copyright'],$pi['productline'],$pi['categoryid'],$pi['bodystyleid']);
				unset($npi['designerIDs'],$npi['licenseeIDs'],$npi['productLineIDs'],$npi['opm_productid'],$npi['categoryid'],$npi['propertyid'],$npi['bodystyleid']);
				
				
					
				
				// now we compare arrays, to check for changes.
				
				$arrDiff = array_diff_assoc($npi,$pi);
			
				foreach ($arrDiff as $key => $value) {
					
					if ($key == 'category') {
						$message = "Category was changed to " . $npi['category'] . " by " . $this->userinfo->username;
					} elseif ($key == 'bodystyle') {
						$message = "Body Style was changed to " . $npi['bodystyle'] . " by " . $this->userinfo->username;
					} elseif ($key == 'productname') {
						$message = "Product Name was changed to " . $npi['productname'] . " by " . $this->userinfo->username;
					} elseif ($key == 'property') {
						$message = "Property was changed to " . $npi['property'] . " by " . $this->userinfo->username;
					} elseif ($key == 'duedate') {
						$message = "Due Date was changed to " . date("m/d/y h:i a",$npi['duedate']) . " by " . $this->userinfo->username;
					} elseif ($key == 'filmlocations') {
						$message = "Print + Garment Info was changed to " . $npi['filmlocations'] . " by " . $this->userinfo->username;
					} elseif ($key == 'filmnumber') {
						$message = "Film Number was changed to " . $npi['filmnumber'] . " by " . $this->userinfo->username;
					} elseif ($key == 'artworkcharges') {
						$message = "Artwork Charges was changed to " . $npi['artworkcharges'] . " by " . $this->userinfo->username;
					} elseif ($key == 'presentationstyles') {
						$message = "Presentation Styles was changed to " . $npi['presentationstyles'] . " by " . $this->userinfo->username;
					} elseif ($key == 'copyrightaddendums') {
						$message = "Copyright addendums was changed to ". $npi['copyrightaddendums'] ." by " . $this->userinfo->username;
					} elseif ($key == 'productdesc') {
						$message = "Product Description was changed to ". $npi['productdesc'] ." by " . $this->userinfo->username;
					} elseif ($key == 'shortname') {
						$message = "Short Name was changed to ". $npi['shortname'] ." by " . $this->userinfo->username;
					} elseif ($key == 'numprints') {
						$message = "Number Of Prints was changed to ". $npi['numprints'] ." by " . $this->userinfo->username;
					} elseif ($key == 'licenseecode') {
						$message = "Licensee Code was changed to ". $npi['licenseecode'] ." by " . $this->userinfo->username;
					} else {
						$message = $key . " was changed to " . addslashes($value) . " by " . $this->userinfo->username;
					} 
					
					//echo $message . "<br><br>";
					
					$this->opm->addHistoryItem($postdata['opm_productid'],$message);
					
				}
			}
	
			if ($opm_productid = $this->products_model->saveProduct($postdata)) {
			
				$this->opm->updateAvailableTerritories($opm_productid);
				$this->opm->setLastModified($opm_productid);
				
				if ($this->input->post('save_add_another'))
					$this->opm->displayAlert("Product has been saved!","/products/edit/");
				else
					$this->opm->displayAlert("Product has been saved!","/products/view/" . $opm_productid);
				
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the product!","back");
				return true;
			}
		
		} else {
		
			$this->opm->displayError("You do not have permission to do that.");
			return true;
		
		}
			
	}
	
	function saveForumPost() {
	
	
		if (checkPerms('can_post_to_forums',true)) {
   		
			$errors = "";
			$postdata['opm_productid'] = $this->input->post('opm_productid');
			
			if (!$postdata['post_title'] = $this->input->post('post_title'))
				$errors .= "Post was missing Title<br />";
			
			if (!$postdata['post_text'] = $this->input->post('post_text')) 
				$errors .= "Post was missing content<br />";
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
	
			
			$this->load->model('forum_model');
			
			if ($this->forum_model->addForumEntry($postdata['opm_productid'],$this->userinfo->userid,$postdata['post_title'],$postdata['post_text'])) {
			
				// send email
				
				$arrData['username'] =  $this->userinfo->username;
				$arrData['commentSubject'] = $postdata['post_title'];
				$arrData['commentBody'] = $postdata['post_text'];
				
				$this->opm->sendProductEmail($postdata['opm_productid'],"new_comment_posted",$arrData);
			
				$this->opm->setLastModified($postdata['opm_productid']);
				$this->opm->displayAlert("Message succesfully posted.","/products/view/" . $postdata['opm_productid']);
				
				return true;	
			
			
			} else {
			
				$this->opm->displayError("Error saving forum post","back");
				return true;
			}
		
		}
		
			
	}
	
	function saveImage() {
	
		$this->load->model('images_model');
	
		if (checkPerms('can_upload_images',true)) {
		
			if (is_uploaded_file($_FILES['imageFile']['tmp_name'])) { // we have a file upload!
	
				$sizeInfo = getimagesize($_FILES['imageFile']['tmp_name']);
				
				//$postdata['image'] = addslashes(fread(fopen($_FILES['imageFile']['tmp_name'], "r"), filesize($_FILES['imageFile']['tmp_name'])));
				$postdata['filename'] = $_FILES['imageFile']['name'];
				$postdata['image_type'] = $_FILES['imageFile']['type'];
				$postdata['opm_productid'] = $this->input->post('opm_productid');
				$postdata['image_label'] = $this->input->post('label');

				if (!checkPerms('can_upload_any_size_images')) {

					if ($sizeInfo[0] != $this->config->item('visualWidth') || $sizeInfo[1] != $this->config->item('visualHeight')) {
					
						$this->opm->displayError("Image is not ". $this->config->item('visualWidth')."x". $this->config->item('visualHeight'),"back");
						return true;
					
					}
				
				}
						
				if ($imageId = $this->images_model->saveImage($postdata)) {
					
					$fileSavePath = $this->config->item('fileUploadPath') . "visuals/" . $imageId;
					$imageResized = false;
					$imageSaved = false;
					
					if (checkPerms('can_upload_any_size_images') && $sizeInfo[0] > $this->config->item('visualWidth')) {

							
						//die("helloo");
						
						// resize image
						
						$config['image_library'] = 'gd2';
						$config['source_image']	= $_FILES['imageFile']['tmp_name'];
						$config['maintain_ratio'] = TRUE;
						$config['width']	 = 1100;
						$config['height']	= 1100;
						//$config['new_image'] = $fileSavePath;
						$this->load->library('image_lib', $config); 
						
						if ($this->image_lib->resize()) {
							
							//$imageResized = true;
							//$imageSaved = true;
							
						} else {
							
							die($this->image_lib->display_errors());
						}
						

					}
					
					if (!$imageResized) {
						
						if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $fileSavePath)) {
							
							$imageSaved = true;
						}
						
					}
					
					if ($imageSaved) {
					
						// prepare data and send email
						
						$arrData['imageInfo'] = $postdata;
						$arrData['username'] = $this->userinfo->username;
						$this->opm->sendProductEmail($postdata['opm_productid'],"new_image_uploaded",$arrData);
						
						$this->opm->setLastModified($postdata['opm_productid']);
						$this->opm->addHistoryItem($postdata['opm_productid'],$postdata['filename']." uploaded by " . $this->userinfo->username); 
						$this->opm->displayAlert("Image succesfully uploaded.","/products/view/" . $postdata['opm_productid']."/images");
						return true;
						
					} else {
					
						$this->opm->displayError("Error saving image file","/products/view/" . $postdata['opm_productid']."/images");
						return true;
					
					}
				
				
				} else {
				
					$this->opm->displayError("Error saving image","/products/view/" . $postdata['opm_productid']."/images");
					return true;
					
				}

				unlink($_FILES['imageFile']['tmp_name']);
		
			} else {
			
				$this->opm->displayError("The uploaded image appears to be invalid.","/products/view/" . $postdata['opm_productid']."/images");
				return true;
			
			}
			

		
		}
		
			
	}
    
    function loadContent($id,$tabName)
    {
    
    	$this->output->enable_profiler(FALSE);
    
    	if ($tabName == 'summary' && checkPerms('view_summary_tab')) {
    		
    		
    		$data['now'] = time();
    		
			$this->load->model('users_model');
			$this->load->model('usergroups_model');
			$this->load->model('images_model');
			$this->load->model('colors_model');
			$this->load->model('sizes_model');
			
			$data['usergroups'] = $this->usergroups_model->fetchUsergroups($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
    		$data['images'] = $this->images_model->fetchImages($id);
    		$data['colors'] = $this->colors_model->fetchColors();
    		$data['sizes'] = $this->sizes_model->fetchSizes();
    		
    		// create size string
    		
    		$data['sizeString'] = "";
    		
    		foreach ($data['product']->sizes as $s)
    			$data['sizeString'] .= $s['size'] . ", ";
    			
    		$data['sizeString'] = substr($data['sizeString'],0,strlen($data['sizeString'])-2);
			
			echo $this->load->view('product/'.$tabName,$data,true);
    
    	} else if ($tabName == 'involvement' && checkPerms('view_involvement_tab')) {
    		
			$this->load->model('users_model');
			$this->load->model('usergroups_model');
			$this->load->model('accounts_model');
			
			$data['accounts'] = $this->accounts_model->fetchAccounts();
			$data['usergroups'] = $this->usergroups_model->fetchUsergroups($id);
			$data['selectUGs'] = $this->usergroups_model->fetchUsergroups($id,null,true);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			
			echo $this->load->view('product/'.$tabName,$data,true);
 	
    	} else if ($tabName == 'images' && checkPerms('view_images_tab')) {
    	
    		$this->load->model('images_model');
    		$this->load->model('files_model');
    		
    		$data['images'] = $this->images_model->fetchImages($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
    		$data['masterfiles'] = $this->files_model->fetchMasterFiles($id);
    		$data['separations'] = $this->files_model->fetchSeparations($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else if ($tabName == 'billing' && checkPerms('view_billing_tab')) {
    		
    		$this->load->model('invoices_model');
    		
    		$data['userInvoices'] = $this->invoices_model->fetchInvoices(false,null,null,$this->userinfo->userid,1); // get all 'inprogress' invoices for current user.
    		$data['invoiceItems'] = $this->invoices_model->fetchInvoiceDetailByProduct($id); //$this->invoices_model->fetchInvoices(false,null,null,null,"2,3,4,5,6,7,8,9", null, null, null,$id); // get all invoices for current product.
    		$data['p'] = $this->products_model->fetchProductInfo($id);
			$data['id'] = $id;
			
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else if ($tabName == 'forum' && checkPerms('view_forum_tab')) {
    		
    		$this->load->model('forum_model');
    		$data['forum'] = $this->forum_model->fetchForumEntries($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else if ($tabName == 'history' && checkPerms('view_history_tab')) {
    		
    		$this->load->model('history_model');
    		$data['history'] = $this->history_model->fetchHistory($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			
			echo $this->load->view('product/'.$tabName,$data,true);
			
		} else if ($tabName == 'wholesale' && checkPerms('view_wholesale_tab')) {
    		
    		$this->load->model('sitebrands_model');
    		$this->load->model('sizes_model');
    		
    		$data['p'] = $this->products_model->fetchProductInfo($id,false,true);
    		$data['sb'] = $this->sitebrands_model->fetchSitebrands();
    		$data['sizes'] = $this->sizes_model->fetchSizes();
    		
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else {
    	
    		$this->opm->displayError("Cannot load tab content.");
			return true;
    	
    	}
	
    
    }
    
    function expireProduct($opm_productid,$onoff) { // onoff = 1 for expire, 2 for un-expire
    	
    	$this->load->model('approvalstatus_model');
    	
		if($this->approvalstatus_model->expireProduct($opm_productid,$onoff)) {
		
			if($onoff == 1) {
			
				$this->opm->displayAlert("Product Successfully Expired!","/products/view/" . $opm_productid);
				return true;
			
			} else {
			
			
				$this->approvalstatus_model->updateApprovalStatus($opm_productid);
				
				$this->opm->displayAlert("Product Successfully Un-Expired!","/products/view/" . $opm_productid);
				return true;
				
			
			}
		
		}
		
    
    }
    
    
    function updateApprovalStatus($opm_productid) {
    	
    	$this->load->model('approvalstatus_model');
    	
		if($id = $this->approvalstatus_model->updateApprovalStatus($opm_productid))
			echo "success. appstatus id changed to " . $id;
    
    }
    
    function changeConceptApprovalStatus($opm_productid,$action) { 
    
    	if (checkPerms('can_approve_concepts')) {
    
	    	$this->load->model('approvalstatus_model');
	    	$this->load->model('forum_model');
	    	
	    	$this->opm->setLastModified($opm_productid);
		
			$product = $this->products_model->fetchProductInfo($opm_productid);

			if ($action == 'approveconcept') {
			
				if ($this->approvalstatus_model->changeConceptApprovalStatus($opm_productid,0)) {
				
					$this->opm->addHistoryItem($opm_productid,"Concept Approved by " . $this->userinfo->username);				
					
					$this->opm->displayAlert("Concept successfully Approved!","/products/view/" . $opm_productid);
					return true;
				
				}
				
			} 				
					
		}
		
					
	}
	

    
    function changeApprovalStatus($opm_productid,$action,$userid = 0) { // user id submitted for verbal approval
    
    	$this->load->model('approvalstatus_model');
    	
    	$this->opm->setLastModified($opm_productid);
	
		// first check that user has approval/rejection rights on this product.
		
		$product = $this->products_model->fetchProductInfo($opm_productid);
		
		$canApprove = false;
		
		foreach ($product->approvalInfo as $ai) {
		
			if ($ai->userid == $this->userinfo->userid)
				$canApprove = true;
		
		}
		
		if ($canApprove || checkPerms('can_verbally_approve') || checkPerms('can_verbally_reject')) {
		
			if ($action == 'approve') {
			
				if($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,$this->config->item('appStatusApproved'))) {
				
					$this->opm->displayAlert("Product successfully Approved!","/products/view/" . $opm_productid);
					return true;
				
				}
				
			} else if ($action == 'reject') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,$this->config->item('appStatusRejected'))) {
				
					$this->opm->displayAlert("Rejection Successful!","/products/view/" . $opm_productid);
					return true;
					
				}
				
							
			} else if ($action == 'verballyApprove') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($userid,$opm_productid,$this->config->item('appStatusApproved'),true)) {
				
					$this->opm->displayAlert("Verbal Approval Successful!","/products/view/" . $opm_productid);
					return true;
					
				}
				
			} else if ($action == 'verballyReject') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($userid,$opm_productid,$this->config->item('appStatusRejected'),true)) {
				
					$this->opm->displayAlert("Verbal Rejection Successful!","/products/view/" . $opm_productid);
					return true;
					
				}
			
			} else if ($action == 'undo') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($userid,$opm_productid,0)) {
				
					$this->opm->displayAlert("Reversal Success!","/products/view/" . $opm_productid);
					return true;
					
				}
				
			} else if ($action == 'approvewrevisions') {
				
				$this->load->model('forum_model');
				
				$postdata['revisions'] = $this->input->post('revisions');
				
				if ($postdata['revisions'] && ($postdata['revisions'] != 'Enter Revisions Here...') && ($postdata['revisions'] != 'If you have any revisions, please enter them here...')) { // set status to approve w/ revisions.
				
					$this->forum_model->addForumEntry($opm_productid,$this->userinfo->userid,"REVISIONS",$postdata['revisions']);
					
					if ($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,2,false,$postdata['revisions'])) {
					
						$this->opm->displayAlert("Approval Success!","/products/view/" . $opm_productid);
						return true;
						
					}
				
				} else { // no revisions were sent, simply approve.
				
				
					if($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,1)) {
					
						$this->opm->displayAlert("Product successfully Approved!","/products/view/" . $opm_productid);
						return true;
				
					}
				
				
				}
				
			} else if ($action == 'resubmitwrevisions') {
				
				$this->products_model->setActiveResubmit($opm_productid,1);
				$this->approvalstatus_model->updateApprovalStatus($opm_productid);
				
				$this->load->model('forum_model');
				
				$this->opm->addHistoryItem($opm_productid, $this->userinfo->username . " resubmitted this product with revisions");
				
				$postdata['revisions'] = $this->input->post('revisions');
				
				$this->forum_model->addForumEntry($opm_productid,$this->userinfo->userid,"REVISIONS",$postdata['revisions']);
				
				// send email!
				
				$arrData['username'] =  $this->userinfo->username;
				$arrData['productInfo'] = $this->products_model->fetchProductInfo($opm_productid);
				$arrData['revisions'] = $postdata['revisions'];
				$arrData['approvalstatus'] = "Resubmitted With Revisions";
				
				$this->opm->sendProductEmail($opm_productid,"approval_status_changed",$arrData);
				
				// alert user
								
				$this->opm->displayAlert("Revisions have been submitted!","/products/view/" . $opm_productid);
			
			
			
			} else if ($action == 'revisionscomplete') {
				
				
				
				$this->products_model->setActiveResubmit($opm_productid,0);
				$this->approvalstatus_model->updateApprovalStatus($opm_productid);

				$this->opm->addHistoryItem($opm_productid,"Revisions Completed by " . $this->userinfo->username);				
				
				$arrData['username'] =  $this->userinfo->username;
				$arrData['productInfo'] = $this->products_model->fetchProductInfo($opm_productid);
				//$arrData['revisions'] = $postdata['revisions'];
				$arrData['approvalstatus'] = "set status to Revisions Complete";
				
				$this->opm->sendProductEmail($opm_productid,"approval_status_changed",$arrData);


				$this->opm->displayAlert("Revisions have been completed!","/products/view/" . $opm_productid);
			
			
			
			}
		
		} else {
		
		
		}
		
					
	}
	
	
	function setSampAppStatus($opm_productid,$onoff) { // user id submitted for verbal approval

		$this->load->model('approvalstatus_model');

		if (checkPerms('can_edit_samp_approval',true)) {
		
			if ($onoff) {
			
				$statusid = 1;
				$message = "Sample approval status was changed to Approved by " . $this->userinfo->username;
				$arrData['approvalstatus'] = "Approved";
			
			} else {
			
				$statusid = 0;
				$message = "Sample approval status was changed to Pending by " . $this->userinfo->username;
				$arrData['approvalstatus'] = "set to Pending";
				
			}
			
			
			if ($this->products_model->setSampAppStatus($opm_productid,$statusid)) {
				
				// send email
				
				$arrData['username'] =  $this->userinfo->username;
				$arrData['productInfo'] = $this->products_model->fetchProductInfo($opm_productid);
				
				$this->opm->sendProductEmail($opm_productid,"sample_approval_status_changed",$arrData);
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->approvalstatus_model->updateApprovalStatus($opm_productid);
				
				$this->opm->displayAlert("Sample approval status has been changed.","/products/view/" . $opm_productid);
				return true;
				
			} else {
			
				$this->opm->displayError("Error saving status","/products/view/" . $opm_productid);
				return true;
			
			}
			
		
		}

    }
    
    function setExploitStatus($opm_productid,$statusid) { // user id submitted for verbal approval

		$this->load->model('approvalstatus_model');
	
		if (checkPerms('can_edit_exploit_status',true)) {
		
			// fetch status name
			
			if ($statusName = $this->approvalstatus_model->fetchExploitStatus($statusid)) {
				
				$message = "Exploitation status was changed to ". $statusName ." by " . $this->userinfo->username;
				
				if ($this->products_model->setExploitStatus($opm_productid,$statusid)) {
				
					$this->opm->addHistoryItem($opm_productid,$message);
				
					$this->opm->displayAlert("Exploitation status has been changed.","/products/view/" . $opm_productid);
					return true;
				
				}
				
			} else {
				
				$this->opm->displayError("Error saving status","/products/view/" . $opm_productid);
				return true;
				
			}		
		
		}

    }
    
    function setUsageStatus($opm_productid,$statusid) { // user id submitted for verbal approval

		$this->load->model('approvalstatus_model');
	
		if (checkPerms('can_edit_usage_rights',true)) {
		
			// fetch status name
			
			if ($statusName = $this->approvalstatus_model->fetchUsageStatus($statusid)) {
				
				$message = "Usage rights was changed to ". $statusName ." by " . $this->userinfo->username;
				
				if ($this->products_model->setUsageStatus($opm_productid,$statusid)) {
				
					$this->opm->addHistoryItem($opm_productid,$message);
				
					$this->opm->displayAlert("Usage rights has been changed.","/products/view/" . $opm_productid);
					return true;
				
				}
				
			} else {
				
				$this->opm->displayError("Error saving status","/products/view/" . $opm_productid);
				return true;
				
			}		
		
		}

    }
    
    function markPurchased($opm_productid,$accountid) { // user id submitted for verbal approval
    
    	$this->load->model('accounts_model');

		if (checkPerms('can_mark_purchased',true)) {

			
			if ($this->accounts_model->markProductPurchased($opm_productid,$accountid)) {
				
				// fetch account name for history entry
				
				$account = $this->accounts_model->fetchAccount($accountid);
				
				$message = $account->account . " purchased this product (entered by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product marked Purchased by " . $account->account . ".","/products/view/" . $opm_productid);
				return true;
				
			} else {
			
				$this->opm->displayError("Error saving status","/products/view/" . $opm_productid);
				return true;
			
			}
		
		}

    }
    
    function territoryException($opm_productid,$territoryid) { 
    
    	$this->load->model('territories_model');

		if (checkPerms('can_change_product_territories',true)) {

			
			if ($this->territories_model->createException($opm_productid,$territoryid)) {
			
				$this->opm->updateAvailableTerritories($opm_productid);

				// fetch territory name for history entry
				
				$terr = $this->territories_model->fetchTerritory($territoryid);
				 
				$message =  "Product excluded from territory: " . $terr->territory . " (by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product excluded from " . $terr->territory,"/products/view/" . $opm_productid . "/involvement");
				return true;
				
			} else {
			
				$this->opm->displayError("Error created exception","/products/view/" . $opm_productid . "/involvement");
				return true;
			
			}
		
		}

    }
    
    function territoryExceptionCancel($opm_productid,$territoryid) { 
    
    	$this->load->model('territories_model');

		if (checkPerms('can_change_product_territories',true)) {

			
			if ($this->territories_model->cancelException($opm_productid,$territoryid)) {
			
				$this->opm->updateAvailableTerritories($opm_productid);

				// fetch territory name for history entry
				
				$terr = $this->territories_model->fetchTerritory($territoryid);
				 
				$message =  "Product exception from territory: " . $terr->territory . " cancelled (by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product exception from " . $terr->territory . " cancelled","/products/view/" . $opm_productid . "/involvement");
				return true;
				
			} else {
			
				$this->opm->displayError("Error created exception","/products/view/" . $opm_productid . "/involvement");
				return true;
			
			}
		
		}

    }
    
    function rightException($opm_productid,$rightid) { 
    
    	$this->load->model('rights_model');

		if (checkPerms('can_change_product_rights',true)) {

			
			if ($this->rights_model->createException($opm_productid,$rightid)) {
			
				$this->opm->updateAvailableRights($opm_productid);

				// fetch right name for history entry
				
				$right = $this->rights_model->fetchRight($rightid);
				 
				$message =  "Product excluded from: " . $right->right . " (by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product excluded from " . $right->right,"/products/view/" . $opm_productid . "/involvement");
				return true;
				
			} else {
			
				$this->opm->displayError("Error created exception","/products/view/" . $opm_productid . "/involvement");
				return true;
			
			}
		
		}

    }
    
    function channelException($opm_productid,$channelid) { 
    
    	$this->load->model('channels_model');

		if (checkPerms('can_change_product_channels',true)) {

			
			if ($this->channels_model->createException($opm_productid,$channelid)) {
			
				$this->opm->updateAvailableChannels($opm_productid);

				// fetch right name for history entry
				
				$right = $this->channels_model->fetchChannel($channelid);
				 
				$message =  "Product excluded from: " . $channel->channel . " (by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product excluded from " . $channel->channel,"/products/view/" . $opm_productid . "/involvement");
				return true;
				
			} else {
			
				$this->opm->displayError("Error created exception","/products/view/" . $opm_productid . "/involvement");
				return true;
			
			}
		
		}

    }
    
    function rightExceptionCancel($opm_productid,$rightid) { 
    
    	$this->load->model('rights_model');

		if (checkPerms('can_change_product_rights',true)) {

			
			if ($this->rights_model->cancelException($opm_productid,$rightid)) {
			
				$this->opm->updateAvailableRights($opm_productid);

				// fetch right name for history entry
				
				$right = $this->rights_model->fetchRight($rightid);
				 
				$message =  "Product exception from: " . $right->right . " cancelled (by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product exception from " . $right->right . " cancelled","/products/view/" . $opm_productid . "/involvement");
				return true;
				
			} else {
			
				$this->opm->displayError("Error created exception","/products/view/" . $opm_productid . "/involvement");
				return true;
			
			}
		
		}

    }
    
    function channelExceptionCancel($opm_productid,$channelid) { 
    
    	$this->load->model('channels_model');

		if (checkPerms('can_change_product_channels',true)) {

			
			if ($this->rights_model->cancelException($opm_productid,$channelid)) {
			
				$this->opm->updateAvailableChannels($opm_productid);

				// fetch right name for history entry
				
				$channel = $this->channels_model->fetchChannel($channelid);
				 
				$message =  "Product exception from: " . $channel->channel . " cancelled (by " . $this->userinfo->username . ")";
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Product exception from " . $channel->channel  . " cancelled","/products/view/" . $opm_productid . "/involvement");
				return true;
				
			} else {
			
				$this->opm->displayError("Error created exception","/products/view/" . $opm_productid . "/involvement");
				return true;
			
			}
		
		}

    }
    
    function changeLockStatus() {
	    
	    $opm_productid = $this->input->post("opm_productid");
	    
	    $status = $this->products_model->changeLockStatus($opm_productid);
	    
	    if ($status == 'locked') {
		    
		    $message =  "Product locked by " . $this->userinfo->username;
			$this->opm->addHistoryItem($opm_productid,$message);
		    
	    } else if ($status == 'unlocked') { 
		    
		    $message =  "Product unlocked by " . $this->userinfo->username;
			$this->opm->addHistoryItem($opm_productid,$message);
		    
	    }
	    
	    die($status);
	    
    }
    
    function saveWholesaleInfo() {
    
		$postdata['opm_productid'] = $this->input->post('opm_productid');
	    $postdata['isactive'] = $this->input->post('isactive');
	    $postdata['isfeatured'] = $this->input->post('isfeatured');
		$postdata['sitebrandid'] = $this->input->post('sitebrandid');
		$postdata['baseprice'] = $this->input->post('baseprice');
		$postdata['sku'] = $this->input->post('sku');    
		$postdata['isavail'] = $this->input->post('isavail');    
		$postdata['add_sizeid'] = $this->input->post('add_sizeid');
		$postdata['add_sku'] = $this->input->post('add_sku');
		
		if ($this->products_model->saveWholesaleInfo($postdata)) {
			
			$this->opm->displayAlert("Product saved successfully","/products/view/" . $postdata['opm_productid'] . "/wholesale");
			return true;
			
		} else {
			
			$this->opm->displayError("Could not save product info!","/products/view/" . $postdata['opm_productid'] . "/wholesale");
			return true;
			
		}

	    
    }
    
     function addLinkedProduct($opm_productid) {
    
    	$this->output->enable_profiler(FALSE);
		
		if (checkPerms("can_add_linked_products",true)) { 
		
			$data = array();
			$data['opm_productid'] = $opm_productid;
			
			$this->load->view('addLinkedProduct', $data);
			
			
			
		} 
		
		
	}
	
	
	function saveLinkedProduct() {
	
		$this->output->enable_profiler(FALSE);
		
		if (checkPerms("can_add_linked_products",true)) { 
		
			$data['opm_productid'] = $this->input->post('opm_productid');
			$data['opmIDToLink'] = $this->input->post('opmIDToLink');
			
			if ($this->products_model->checkOpmProductID($data['opmIDToLink'])) {
				
				$data['success'] = true;
				
				if ($this->products_model->createProductLink($data['opm_productid'],$data['opmIDToLink'])) {
				
					$message = "Product linked to <a href='".base_url()."products/view/".$data['opmIDToLink']."'>ID # " . $data['opmIDToLink'] . "</a> by " . $this->userinfo->username;
					$this->opm->addHistoryItem($data['opm_productid'], $message);
				
					$this->opm->setLastModified($data['opm_productid']);
					//$this->load->view('addLinkedProduct', $data);
				
				} else {
					
					$data['success'] = false;
					$data['error'] = "Error Creating Link";
					
				}
				
				
			
			} else {
			
				$data['success'] = false;
				$data['error'] = "Product Not Found";
			
			}
		
		
			$this->load->view('addLinkedProduct', $data);
			
		} 
		
		
	}
	
	function removeLink($opm_productid,$linkid,$linkedprodid) {
	
		$this->output->enable_profiler(FALSE);
		
		if (checkPerms("can_remove_product_links",true)) { 
		
						
			if ($this->products_model->removeProductLink($linkid)) {
				
				// do message for each way
				
				$message = "Link to <a href='".base_url()."products/view/".$linkedprodid."'>ID # " . $linkedprodid . "</a> removed by " . $this->userinfo->username;
				$this->opm->addHistoryItem($opm_productid, $message);
				
				$message = "Link to <a href='".base_url()."products/view/".$opm_productid."'>ID # " . $opm_productid . "</a> removed by " . $this->userinfo->username;
				$this->opm->addHistoryItem($linkedprodid, $message);
				
				$this->opm->displayAlert("Link Removed Successfully!","/products/view/" . $opm_productid);
				return true;
			
			
			} else {
			
				$this->opm->displayError("Could not remove product link!","/products/view/" . $opm_productid);
				return true;
			
			}
		
					
		} 
		
		
	}
    
    
 
    
}



?>