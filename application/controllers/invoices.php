<?php

class Invoices extends CI_Controller {

	function __construct() {
	
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('products_model');
    	$this->load->model('invoices_model');
    	$this->load->model('properties_model');
    	$this->load->model('currencies_model');
    	$this->load->model('companies_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'billing';
    	
    	//if ($this->config->item('debugMode') == true)
    	//	$this->output->enable_profiler(TRUE);
    		
    	global $searchArgList, $reportArgList, $royaltyReportArgList;
    	    	
    	$searchArgList = array("userid","statusid","title","referencenumber","propertyid","opm_productid","ownerid","companyid","attentionid","showdeleted","orderBy","orderByAscDesc","perPage","offset");
    	$reportArgList = array("userids","statusids","propertyids","opm_productids","ownerids","attentionids","startdate","enddate","groupByProperty");
        $royaltyReportArgList = array("propertyid","startdate","enddate");
    	
    	//if ($this->config->item('debugMode') == true || $this->userinfo->userid == 1)
    	//	$this->output->enable_profiler(TRUE);
    		
    }
    
    function index() {
    
    	redirect("invoices/search");
    
    }
    
    function search($userid = 0, $statusid = 0, $title = 0, $referencenumber = 0, $propertyid = 0, $opm_productid = 0, $ownerid = 0, $companyid = 0, $attentionid = 0, $showdeleted = 0, $orderBy = "id", $orderByAscDesc = "asc", $perPage = 0, $offset = 0) {
  
    	global $searchArgList;
		
		if (checkPerms('can_view_all_invoices')) {
			
			
			$data['users'] = $this->users_model->fetchInvoiceUsers();
			$titleText = "Search Invoices";
		
		} else {
			
			// we are not an admin user, so we can only view our own invoices.
			$userid = $this->userinfo->userid;
			$titleText = "My Invoices";
		
		}

    	
    	foreach ($searchArgList as $k=>$d) 
    		$data['args'][$d] = ${$d};   	
	
		
		$template['rightNav'] = $this->load->view('invoices/rightNav',$data,true);
   		$template['page_title'] = $titleText;
   		$template['bigheader'] = $titleText;
	    $template['nav2'] = $this->load->view('invoices/nav2search',$data,true);
    	
    	$data['totalInvoices'] = $this->invoices_model->fetchInvoices(true,null,null,$userid,$statusid,$title,$referencenumber,$propertyid,$opm_productid,$ownerid,$companyid,$attentionid,$showdeleted);
    	
    	if ($propertyid == null) { // this is here because if base_url recieves a null, it leaves it off and messes up the pagination!
    	
    		$baseurlPropID = 0;
    		
    	} else {
    	
    		$baseurlPropID = $propertyid;
    	
    	}
    	
    	$this->load->library('pagination');
		$config['base_url'] = base_url().'/invoices/search/'.$userid."/".$statusid."/".$title."/".$referencenumber."/".$propertyid."/".$opm_productid."/".$ownerid."/".$companyid."/".$attentionid."/".$showdeleted."/".$orderBy."/".$orderByAscDesc."/".$perPage."/";
		$config['total_rows'] = $data['totalInvoices'];
		
		// determine per page
		
		if ($perPage)
			$config['per_page'] = $perPage;
		else
			$config['per_page'] = $this->config->item('searchPerPage');


		$config['uri_segment'] = 16;


    	$this->pagination->initialize($config);
    	
    	$data['invoices'] = $this->invoices_model->fetchInvoices(false,$offset,$config['per_page'],$userid,$statusid,$title,$referencenumber,$propertyid,$opm_productid,$ownerid,$companyid,$attentionid,$showdeleted,$orderBy,$orderByAscDesc);
	   	$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'));	
    	$data['users'] = $this->users_model->fetchInvoiceUsers();
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['statuses'] = $this->invoices_model->fetchStatuses();
    	$data['companies'] = $this->companies_model->fetchCompanies();
    	
    	$data['prodStart'] = $offset + 1;
    	$data['prodEnd'] = $data['prodStart'] + ($data['invoices']->num_rows() - 1);
    	
    	//$template['rightNav'] = $this->load->view('search/rightNav',$data,true);
    	$template['searchArea'] = $this->load->view('invoices/searchArea',$data,true);
    	$template['contentNav'] = $this->load->view('invoices/searchNav',$data,true);
    	$template['content'] = $this->load->view('invoices/search',$data,true);
    	
    	$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','tipsx3'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
    
    }
    
    function generateReport($userids = 0, $statusids = 0, $propertyids = 0, $opm_productids = 0, $ownerids = 0, $attentionids = 0, $startdate = 0, $enddate = 0, $groupByProperty = 0, $exportexcel = 0) {
 
 		$this->output->enable_profiler(TRUE);
 
    	global $reportArgList;
		
		$data = array();
		
		if (checkPerms('can_generate_invoice_reports',true)) {
	    	
	    	foreach ($reportArgList as $k=>$d) {
	    		
	    		if ($d == 'groupByProperty' || $d == 'exportexcel' || $d == 'startdate' || $d == 'enddate') {
	    		
	    			$data['args'][$d] = ${$d};   	
	    		
	    		} else {
	    		
	    			$data['args'][$d] = explode(",",${$d});   	
	    		
	    		}	
			}
			
			//$template['rightNav'] = $this->load->view('invoices/rightNav',$data,true);
	   		$template['page_title'] = "Generate Billing Reports";
	   		$template['bigheader'] = "Generate Billing Reports";
	    	$template['nav2'] = $this->load->view('invoices/nav2search',$data,true);
	
	    	$data['reportData'] = $this->invoices_model->fetchReport($userids,$statusids,$propertyids,$opm_productids,$ownerids,$attentionids,$startdate,$enddate,$groupByProperty);
		   	$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'));	
	    	$data['users'] = $this->users_model->fetchInvoiceUsers();
	    	$data['properties'] = $this->properties_model->fetchProperties();
	    	$data['statuses'] = $this->invoices_model->fetchStatuses();
	    	
	    	$template['searchArea'] = $this->load->view('invoices/reportSearchArea',$data,true);
	    //	$template['contentNav'] = $this->load->view('invoices/searchNav',$data,true);
	    	$template['content'] = $this->load->view('invoices/report',$data,true);
	    	
	    	$arrJS['scripts'] = array('jquery-1.6.2.min','jquery.tools.min','opm_scripts','datepicker'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	    		
	    	$this->load->view('global/maintemplate', $template);
    	
    	}
    }
    
    
    function royaltyReport($propertyid = 0, $startdate = 0, $enddate = 0) {
 
 		$this->output->enable_profiler(TRUE);
 
    	global $royaltyReportArgList;
		
		$data = array();
		
		if (checkPerms('can_generate_invoice_reports',true)) {
	    	
	    	foreach ($royaltyReportArgList as $k=>$d) {
	    		
	    		$data['args'][$d] = ${$d};   		

			}
			
			//$template['rightNav'] = $this->load->view('invoices/rightNav',$data,true);
	   		$template['page_title'] = "Recoupable Artwork Charges Report";
	   		$template['bigheader'] = "Recoupable Artwork Charges Report";
	    	$template['nav2'] = $this->load->view('invoices/nav2search',$data,true);
			
			if ($propertyid) {
	    		
	    		$data['prop'] = $this->properties_model->fetchPropertyInfo($propertyid);
	    		//$data['charges'] = $this->invoices_model->fetchReport(null,null,$propertyid,null,null,null,$startdate,$enddate,false,true);
	    		$data['reportData'] = $this->invoices_model->fetchRoyaltyReport($propertyid,$startdate,$enddate);
		 		$data['reportTotals'] = $this->opm->getRoyaltyTotals($data['reportData'],$propertyid);
		 		$data['startDate'] = date("m/d/Y",$startdate);
		 		$data['endDate'] = date("m/d/Y",$enddate);
		 	
		 	}
		 
		   	$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'));	
	    	$data['users'] = $this->users_model->fetchInvoiceUsers();
	    	$data['properties'] = $this->properties_model->fetchProperties();
	    	$data['statuses'] = $this->invoices_model->fetchStatuses();
	    	
	    	$template['searchArea'] = $this->load->view('invoices/royaltyReportSearchArea',$data,true);
	    //	$template['contentNav'] = $this->load->view('invoices/searchNav',$data,true);
	    	
	    	if (isset($data['reportData']))
	    		$template['content'] = $this->load->view('invoices/royaltyReport',$data,true);
	    	
	    	$arrJS['scripts'] = array('jquery-1.6.2.min','jquery.tools.min','opm_scripts','datepicker'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	    		
	    	$this->load->view('global/maintemplate', $template);
    	
    	}
    	
    }
    
    function editInfo($userid = 0) {
    
		$data = array();
		
		$data['currencies'] = $this->currencies_model->fetchCurrencies();
		$data['referer'] = "editMyInfo";
			
		//$template['rightNav'] = $this->load->view('invoices/rightNav',$data,true);
   		$template['page_title'] = "Edit My Billing Information";
   		$template['bigheader'] = "Edit My Billing Information";
    	$template['nav2'] = "Edit My Billing Information";
		
		if ($userid != 0) { // we are editing a different use (other than self)... fetch that user's info
    		
    		if (!checkPerms('can_change_invoice_user')) {
	    	
	    		$this->opm->displayError("You do not have permission to edit info for this user.");
	    		return false;
	    	
			}
    		
    		$data['user'] = $this->users_model->fetchUserInfo($userid);

	 	
	 	} else {
	 	
	 		
	 		$data['user'] = $this->userinfo;
	 	
	 
	 	}

    	$template['content'] = $this->load->view('users/invoicing',$data,true);
    	
    	$arrJS['scripts'] = array('jquery-1.6.2.min','jquery.tools.min','opm_scripts','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
    		
    	$this->load->view('global/maintemplate', $template);
    	
   
    }
    
    function chargeTypeDetail($propertyid,$chargetypeid) {

    	$data = array();
    	$args = $this->input->post('args');
    	
    	$userids = implode(",",$this->input->post('userids'));
		$statusids = implode(",",$this->input->post('statusids'));
		$propertyids = implode(",",$this->input->post('propertyids'));
		$opm_productids = implode(",",$this->input->post('opm_productids'));
    	$ownerids = implode(",",$this->input->post('ownerids'));
    	$attentionids = implode(",",$this->input->post('attentionids'));
    	$startdate = $this->input->post('startdate');
    	$enddate = $this->input->post('enddate');


    	$data['charges'] = $this->invoices_model->fetchReport($userids,$statusids,$propertyid,$opm_productids,$ownerids,$attentionids,$startdate,$enddate,false,true,$chargetypeid);
    	
    	//$data['charges'] = $this->invoices_model->fetchChargeTypeDetail($propertyid,$chargetypeid);
    	
    	$this->load->view('invoices/chargeTypeDetail', $data);
    
    }

	function exportInvoices($mode = 'file', $checkCodes = true) { // mode can be 'file' or 'screen', checkCodes makes sure we have necessary navision codes for properties, users, etc.
				
		if ($this->input->post('printMultipleBtn')) {
		
			// we are printing, not exporting. Redirect.
			
			$invoiceList = implode(",",$this->input->post('invoiceList'));
			
			$url = "/invoices/showPrintable/" . $invoiceList;
			
			redirect($url);
			
			die();
			
		}
				
		$invData['statusid'] = $this->config->item('invStatusSentToNavision');
		$invData['exportdate'] = mktime();
		
		if (checkPerms('can_export_invoices')) {
		
			$invoiceList = $this->input->post("invoiceList");
			
			if (!$invoiceList) {
				
				$this->opm->displayError("No invoices were selected for export!");
				return false;
				
			}
			
			if ($checkCodes) {
			
				$errors = $this->opm->checkInvoiceCodes($invoiceList);
				
				if ($errors) {
				
					$errText = "<strong>The export could not complete due to the following errors:</strong><br><br>";
					
					foreach ($errors as $err) {
					
						$errText .= "<li>".$err."</li>";
					
					}
				
					$this->opm->displayError($errText);
					return false;
				
				}
				
			
			}
			
			//$invoiceList = array(1);
						
			$xmlInvoices = "<Invoices>"; 
			
			foreach ($invoiceList as $invoiceid) {
			
				$data['i'] = $this->invoices_model->fetchInvoice($invoiceid);
				
				/*echo "<pre>";
				print_r($data['i']);
				die();*/
				
				if ($data['i']->companyid == $this->config->item('BravadoCompanyID')) {
				
					$xmlInvoices .= $this->load->view('xml/invoice',$data,true);
					
					// change invoice status + export date
						
					$this->invoices_model->updateInvoice($invoiceid,$invData);
				
				} else { // this is a zion invoice.. skip and add id to zion pool
					
					$arrZionIDs[] = $invoiceid;
					
				}
			
			}
			
			$xmlInvoices .= "</Invoices>"; 
			
			if (isset($arrZionIDs)) { // we have zion invoices, create separate file.
				
				$xmlZionInvoices = "<Invoices>";
				
				foreach ($arrZionIDs as $invoiceid) {
			
					$data['i'] = $this->invoices_model->fetchInvoice($invoiceid);
					
					$xmlZionInvoices .= $this->load->view('xml/invoice',$data,true);
						
					// change invoice status + export date
							
					$this->invoices_model->updateInvoice($invoiceid,$invData);

				}
				
				$xmlZionInvoices .= "</Invoices>";
				
				
				
			}
			
			$bravInvFileExported = 0;
			$zionInvFileExported = 0;
			
			if ($mode == 'screen') {
				
				header ("Content-Type:text/xml"); 
    				echo $xmlInvoices;
			
			} elseif ($mode == 'file') {
			
				if ($xmlInvoices != "<Invoices></Invoices>") {
					$bravInvoices = 1;	
				} else {
					$bravInvoices = 0;
				}
				
				$fileName1 = "Invoices_" . mktime() . ".xml";
				$filePath = $this->config->item('ftpPath') . $fileName1;

				
				if ($bravInvoices && file_put_contents($filePath, $xmlInvoices)) {
				
					$bravInvFileExported = 1;
				
				}
				
				if (isset($xmlZionInvoices)) {
				
					$fileName2 = "ZRInvoices_" . mktime() . ".xml";
					$filePath = $this->config->item('ftpPath') . $fileName2;
				
					if (file_put_contents($filePath, $xmlZionInvoices)) {
				
						$zionInvFileExported = 1;
				
					}
				
				}
				
				if ($bravInvFileExported && !$zionInvFileExported) {
					
					$this->opm->displayAlert("Invoices successfully exported to file ".$fileName1, "/invoices/search");
					return true;
					
				} else if (!$bravInvFileExported && $zionInvFileExported) {
					
					$this->opm->displayAlert("Invoices successfully exported to file " . $fileName2, "/invoices/search");
					return true;
					
					
				} else if ($bravInvFileExported && $zionInvFileExported) {
					
					$this->opm->displayAlert("Invoices successfully exported to file ".$fileName1." and " . $fileName2, "/invoices/search");
					return true;	
					
				} else {
					
					$this->opm->displayAlert("Error: No files exported. Please contact OPM tech support.", "/invoices/search");
					return true;
				
				}
				
				
					
				
					
				
			
			
			}
			
			
			
			
		
		}
	
	}

	function showData($id) {

    	$data['invoice'] = $this->invoices_model->fetchInvoice($id);
    	
    	echo "<pre>";
    	print_r($data['invoice']);
    	echo "</pre>";
    	
    	die();
    	
    }
    
	function edit($id) {

    	$data = array();
    	
    	if ($id == 'addProduct') { // we are adding a product...derr
    	
    		$id = $this->input->post("invoiceid");
    		$data['addProductID'] = $this->input->post("opm_productid");
    		
    	}
    	

    	if(!$data['invoice'] = $this->invoices_model->fetchInvoice($id)) {
    		
    		if ($id == 'add') {
    		
    			// check if we have billing info entered, if not, redirect to binfo page
    				
    			if (!checkPerms('can_change_invoice_user') && $this->userinfo->staddress == '') {
    			
    				$this->opm->displayAlert("Please enter your billing info below to continue.","/invoices/editinfo/");
					return true;
    			
    			}
    		
				$id = "TBD";
				
				// check and make sure user is set up in system
				
				if (!$this->userinfo->caninvoice && !checkPerms('can_change_invoice_user')) { // && (!checkPerms('can_change_invoice_user'))
				
					$this->opm->displayError("You are not set up in the OPM invoicing system, please contact Bravado for more info.");
					return false;
				
				}
				    		
    		} else {
    		
    			$this->opm->displayError("Invoice Not Found");
    		
    		}
    			
    	} else {
    	
    		// make sure we can either view all invoices, or this is our invoice.
    	
    		if (!checkPerms('can_view_all_invoices') && ($data['invoice']->userid != $this->userinfo->userid)) {
    		
    			$this->opm->displayError("You do not have permission to view this invoice.");
				return false;
    		
    		}
    	
    	}
    	

    	
    	if ($id == 'TBD')
    		$data['mode'] = 'add';
    	else
    		$data['mode'] = "edit";	
    		
    	$data['channels'] = $this->invoices_model->fetchChannels();
    		
    	//$data['product'] = $this->products_model->fetchProductInfo($id);
    	$template['page_title'] = "Edit Invoice # " . $id;
    	$template['bigheader'] = "Edit Invoice # " . $id;
    	$template['nav2'] = $this->load->view('invoices/nav2',$data,true);
    	$template['rightNav'] = $this->load->view('invoices/rightNavEdit',$data,true);   		

     	$data['productManagers'] = $this->users_model->fetchUsers(false,0,null,$this->config->item('productManagersGroupID'),null,null,false,null,null,null,"username");
     	
     	$template['content'] = $this->load->view('invoices/edit2',$data,true);
       
        $arrJS['scripts'] = array('jquery-1.6.2.min','jquery.tools.min','jquery.autocomplete','jquery.animate-colors-min','opm_scripts','shadowbox3'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
    	
    	header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		header("Pragma: no-cache"); 
	
		$this->load->view('global/maintemplate', $template);
	   	
    
    }
    
    function approveForward() {
    
    	$invoiceid = $this->input->post('invoiceid');
    	$preapproveBtn = $this->input->post('preapproveBtn');
    	$approveBtn = $this->input->post('approveBtn');
    	$forwardBtn = $this->input->post('forwardBtn');
    	$resubmitBtn = $this->input->post('resubmitBtn');
    	$reverseBtn = $this->input->post('reverseApproveBtn');
    	
    	if ($reverseBtn) {
    		    	
    		// update invoice to new status
    		
    		$statData['statusid'] = $this->config->item('invStatusSubmitted');
    		//$statData['submitdate'] = mktime();
    		$this->invoices_model->updateInvoice($invoiceid,$statData);
    	    	
			$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Status Changed back to Submitted by " . $this->userinfo->username);
			
			// display alert
			
			$this->opm->displayAlert("Invoice has been changed!","/invoices/search");
			return true;
    	
    	}
    	
    	if ($resubmitBtn) {
    		    	
    		// update invoice to new status
    		
    		$statData['statusid'] = $this->config->item('invStatusApproved');
    		$statData['submitdate'] = mktime();
    		$this->invoices_model->updateInvoice($invoiceid,$statData);
    	
    		// get username of owner
    	
    		//$ownerInfo = $this->users_model->fetchUserInfo($curInvoice->ownerid,true);
			$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Status Changed back to Approved by " . $this->userinfo->username);
			
			// display alert
			
			$this->opm->displayAlert("Invoice has been changed!","/invoices/search");
			return true;
    	
    	}
    	
    	if ($preapproveBtn && checkPerms('can_use_pre_approve_forward_button')) { // this is a pre-approval

    		/*$invData['ownerid'] = $this->input->post('forwardToID1');
    		$invData['statusid'] = $this->config->item('invStatusPreapproved');
					
			if ($this->invoices_model->updateInvoice($invoiceid,$invData)) {
			    				    
			    $ownerInfo = $this->users_model->fetchUserInfo($invData['ownerid'],true);
				
				$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Pre-Approved and Forwarded To " . $ownerInfo->username . " by " . $this->userinfo->username);
				
				$arrData = array("forwarder"=>$this->userinfo->username);
				$this->opm->sendInvoiceEmail($invoiceid,"invoice_forwarded",$arrData);
				
			}*/
    	
    	
    	} elseif ($approveBtn && checkPerms('can_use_approve_forward_button')) { // this is an approval
    
    		//$invData['ownerid'] = $this->input->post('forwardToID2');
    		
    		// check if all channel codes are filled out.
    		
    		if (!$this->invoices_model->checkChannelCodes($invoiceid)) {
	    			    		
	    		$this->opm->displayError("All channel codes must be filled out before approving an invoice.");
	    		return false;
	    		
    		}
    		
    		$invData['statusid'] = $this->config->item('invStatusApproved');
					
			if ($this->invoices_model->updateInvoice($invoiceid,$invData)) {
			    				    
			    //$ownerInfo = $this->users_model->fetchUserInfo($invData['ownerid'],true);
				
				//$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Approved and Forwarded To " . $ownerInfo->username . " by " . $this->userinfo->username);
				$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Approved by " . $this->userinfo->username);
				
			//	$arrData = array("forwarder"=>$this->userinfo->username);
			//	$this->opm->sendInvoiceEmail($invoiceid,"invoice_forwarded",$arrData);
				
			
			}
			
		} elseif ($forwardBtn && checkPerms('can_use_invoice_forward_button')) { // this is a forward
    
    		$invData['ownerid'] = $this->input->post('forwardToID3');
					
			if ($this->invoices_model->updateInvoice($invoiceid,$invData)) {
			    				    
			    $ownerInfo = $this->users_model->fetchUserInfo($invData['ownerid'],true);
				
				$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Forwarded To " . $ownerInfo->username . " by " . $this->userinfo->username);
				
				$arrData = array("forwarder"=>$this->userinfo->username);
				$this->opm->sendInvoiceEmail($invoiceid,"invoice_forwarded",$arrData);
				
			
			}
			

    	} else { // error
    	
    		
    		$this->opm->displayError("There was either a permission error, or insufficent data was found. Please contact OPM tech support.");
			return false;
    	
    	
    	}
    	
    	$this->opm->displayAlert("Action Successful!","/invoices/edit/" . $invoiceid);
		return true;
    	
    }
    
     function showPrintable($ids) { // id can be string of comma-delimited ids
     
     	//$this->load->library('Wkpdf');
     	
     	$invoiceIds = explode(",",$ids);
     	
     	$html = "";
     	
     	foreach ($invoiceIds as $id) {
     	
     		$data = array();

	    	if(!$data['invoice'] = $this->invoices_model->fetchInvoice($id)) {
	    		
	    		$this->opm->displayError("One or more Invoices you are trying to print were not found.");
	    			
	    	} 
	    	
	    	
	    	// make sure we can either view all invoices, or this is our invoice.
    	
    		if (!checkPerms('can_view_all_invoices') && ($data['invoice']->userid != $this->userinfo->userid)) {
    		
    			$this->opm->displayError("You lack permission to view one or more invoices you are trying to print.");
				return false;
    		
    		}
	    	
	    	    	
	    	/*echo "<pre>";
	    	print_r($data);
	    	echo "</pre>";
	    	die();*/
	
	    	$data['mode'] = 'print';
	
	    	//$data['product'] = $this->products_model->fetchProductInfo($id);
	    	$template['page_title'] = "Edit Invoice # " . $id;
	    	$template['bigheader'] = "Edit Invoice # " . $id;
	    	$template['nav2'] = $this->load->view('invoices/nav2',$data,true);
	    	$template['rightNav'] = $this->load->view('invoices/rightNavEdit',$data,true);   		
	     
	     	$data['invoiceContents'] = $this->opm->getInvoiceContents($id,$mode = 'print');
	     	$data['invoiceNotes'] = $this->opm->getInvoiceNotes($id);     	
	     	
	     	$template['content'] = $this->load->view('invoices/edit2',$data,true);
	       
	       // $arrJS['scripts'] = array('jquery-1.3.2.min','jquery.autocomplete','jquery.animate-colors-min','opm_scripts','shadowbox3'); // 
	       // $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	        
	        header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');
	  	 	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	   	 	header('Cache-Control: post-check=0, pre-check=0',false);
	    	session_cache_limiter('must-revalidate');
		
			$this->opm->addInvoiceHistoryItem($id, "Invoice Printed by " . $this->userinfo->username);
		
			$html .= $this->load->view('global/printtemplate', $template,true);
	     	
     	
     	}

    	
		
		//die($html);
		
		$filename="Bravado_Invoices_".mktime().".pdf";
 
	    // Run wkhtmltopdf
	    $descriptorspec = array(
	        0 => array('pipe', 'r'), // stdin
	        1 => array('pipe', 'w'), // stdout
	        2 => array('pipe', 'w'), // stderr
	    );
	 
	   // $process = proc_open($this->config->item('webrootPath') . 'application/libraries/wkhtmltopdf-amd64 --margin-top 20mm --image-quality 100 -q - -', $descriptorspec, $pipes);	 
	    $process = proc_open('wkhtmltopdf --margin-top 20mm --image-quality 100 -q - -', $descriptorspec, $pipes);	 
	    // Send the HTML on stdin
	    fwrite($pipes[0], $html);
	    fclose($pipes[0]);
	 
	    // Read the outputs
	    $pdf = stream_get_contents($pipes[1]);
	    $errors = 0;//stream_get_contents($pipes[2]);
	 
	    // Close the process
	    fclose($pipes[1]);
	    $return_value = proc_close($process);
	 
	    // Output the results
	    if ($errors) {
	        // Note: On a live site you should probably log the error and give a
	        // more generic error message, for security
	        echo 'PDF GENERATOR ERROR:<br />' . nl2br(htmlspecialchars($errors));
	    } else {

	       	header('Content-Type: application/pdf');
	        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
	        header('Pragma: public');
	        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	        header('Last-Modified: ' . gmdate('D, d M Y H:i:s').' GMT');
	        header('Content-Length: ' . strlen($pdf));
	        header('Content-Disposition: inline; filename="' . $filename . '";');
	        echo $pdf;
	    }	
		
	
    }
    
    
    function emptyInvoice() {
    
    	
    	$this->session->unset_userdata('activeInvoiceID');
    	redirect('/invoices/edit', 'location');
    	
    }
        
    function save() {
	
				
	
		$invoiceid = $this->input->post("invoiceid");

		$curInvoice = $this->invoices_model->fetchInvoice($invoiceid);
	
    
    	if ($this->input->post("submitBtn")) {
    	
    		// update invoice to new status
    		
    		$statData['statusid'] = 2;
    		$statData['submitdate'] = mktime();
    		$this->invoices_model->updateInvoice($invoiceid,$statData);
    	
    		// get username of owner
    	
    		$ownerInfo = $this->users_model->fetchUserInfo($curInvoice->ownerid,true);
			$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice Submitted To " . $ownerInfo->username);
				
			// email owner
			
			$arrData = array();
			$this->opm->sendInvoiceEmail($invoiceid,"new_invoice_submitted",$arrData);
			
			// display alert
			
			$this->opm->displayAlert("Invoice has been submitted!","/invoices/search");
			return true;
    	
    	}
    	    	
    	if ($this->input->post("deleteMe") && checkPerms('can_delete_invoices', true)) { // this is a deletion
    
    		$invData['statusid'] = $this->config->item('invStatusDeleted');
					
			if ($this->invoices_model->updateInvoice($invoiceid,$invData)) {
			    				    
				$this->opm->addInvoiceHistoryItem($invoiceid, "Invoice deleted by " . $this->userinfo->username);
			
				$this->opm->displayAlert("Invoice has been deleted!","/invoices/search");
				return true;
			
			}
			
		}
    	    	
	}
	
	function testSendEmail() {
	
		$this->opm->sendInvoiceEmail(16,"new_invoice_submitted");
	
	}
	
	function createSaveNote($invoiceid) {
	
	
		if (!$invoice = $this->invoices_model->fetchInvoice($invoiceid)) {
		
			$this->opm->displayError("Invoice Cannot Be Found.");
			return true;		
		
		}
			
		$data['invoiceid'] = $invoice->id;
		
		if (isset($_POST['note'])) {
		
			$strNote = $this->input->post('note');
		
			if ($id = $this->invoices_model->addInvoiceNote($data['invoiceid'],$strNote)) {
			
				$data['result'] = true;
				$data['resultText'] = "Note Added Successfully";
				
			} else {
			
				$data['result'] = false;
				$data['resultText'] = "Note Error";
			
			}
		
		}
		
		$this->load->view('invoices/createNote', $data);
	
	}
	
	function createSaveLINote($chargeid) {
	
	
		if (!$item = $this->invoices_model->fetchInvoiceItem($chargeid)) {
		
			$this->opm->displayError("Invoice Item Cannot Be Found.");
			return true;		
		
		}
			
		$data['chargeid'] = $item->id;
		$data['notes'] = $item->notes;
		
		if (isset($_POST['note'])) {
		
			$strNote = $this->input->post('note');
		
			if ($id = $this->invoices_model->addInvoiceLINote($data['chargeid'],$strNote)) {
			
				$data['result'] = true;
				$data['resultText'] = "Note Added Successfully";
				
			} else {
			
				$data['result'] = false;
				$data['resultText'] = "Note Error";
			
			}
		
		}
		
		$this->load->view('invoices/createLINote', $data);
	
	}
	
	function addProduct($invoiceid) {
	
		$this->output->enable_profiler(FALSE);

	
		$data['properties'] = $this->properties_model->fetchProperties();
	
		if (!$invoice = $this->invoices_model->fetchInvoice($invoiceid)) {
		
			$this->opm->displayError("Invoice Cannot Be Found.");
			return true;		
		
		}
			
		$data['invoiceid'] = $invoice->id;
			
		$this->load->view('invoices/addProduct', $data);
	
	}
	
	function removeProduct($invoiceid,$opm_productid) {
	
		if (!$invoice = $this->invoices_model->removeProduct($invoiceid,$opm_productid)) {
		
			$this->opm->displayError("Product could not be deleted.");
			return true;		
		
		} else {
		
			// fetch product info for history
			
			$p = $this->products_model->fetchProductInfo($opm_productid, true);
		
			$this->opm->addInvoiceHistoryItem($invoiceid, "Product " . $p->property . " - " . $p->productname . " removed from invoice by " . $this->userinfo->username);
		
			$this->opm->displayAlert("Product has been deleted!","/invoices/edit/".$invoiceid);
			return true;	
			
		
		}
	
	}
	
	
	function addEditCharge($invoiceid, $chargeid = null, $opm_productid = null) {
		
		$this->output->enable_profiler(FALSE);
				
		$this->load->model('files_model');
		
		
		if ($this->input->post('opm_productid'))
			$data['opm_productid'] = $this->input->post('opm_productid');
		else
			$data['opm_productid'] = $opm_productid; // from GET
			
		$data['chargeTypes'] = $this->invoices_model->fetchChargeTypes();
		
		if (!$invoice = $this->invoices_model->fetchInvoice($invoiceid)) {
		
			$this->opm->displayError("Invoice Cannot Be Found.");
			return true;		
		
		} else {
		
			$data['invoice'] = $invoice;
		
		}
		
		if ($chargeid) {
		
			$data['c'] = $this->invoices_model->fetchInvoiceItem($chargeid);		
			$data['chargeDetail'] = $this->load->view('invoices/ajax/getChargeDetail', $data, true);
		
		}
		
		
		if (isset($_POST['addThisCharge']) || isset($_POST['addThisChargeAddAnother']) || isset($_POST['addAnotherProduct']) || isset($_POST['saveCharge']) || isset($_POST['removeCharge'])) {
		
			// Add the charge to the database
			
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			die();*/
			
			$postdata['invoiceid'] = $this->input->post('invoiceid');
			$postdata['hours'] = $this->input->post('hours');
			$postdata['opm_productid'] = $this->input->post('opm_productid');
			$postdata['chargetypeid'] = $this->input->post('chargetypeid');
			$postdata['chargeamount'] = $this->input->post('chargeamount');
			$postdata['chargedescription'] = $this->input->post('chargedescription');
			$postdata['hourlyrate'] = $this->input->post('hourlyrate');
			$postdata['chargeid'] = $this->input->post('chargeid');
			$postdata['notes'] = "";
			
			if (!isset($_POST['removeCharge'])) { // we are adding or editing a charge
			
				// first we have to check if this is for separations or design approval
				// and then make sure appropriate files have been uploaded...
				
				if ($postdata['chargetypeid'] == $this->config->item('invCTDesignApproval')) {
				
					
					if ($this->files_model->fetchMasterFiles($postdata['opm_productid'])->num_rows() == 0) {
					
						$data['result'] = 0;
						$data['resultText'] = "Error: Master files have not yet been uploaded for this product. Please upload your layered hi-res master files in order to bill your approval fee";
					
					}
				
				
				} else if ($postdata['chargetypeid'] == $this->config->item('invCTSeparations')) {
				
					
					if ($this->files_model->fetchSeparations($postdata['opm_productid'])->num_rows() == 0) {
					
						$data['result'] = 0;
						$data['resultText'] = "Error: Separations have not yet been uploaded for this product. Please upload your layered hi-res separations in order to bill your approval fee";
					
					}
					
				
				}
				
				if (!isset($data['result'])) { // no errors, add charge!
				
				
					if ($this->invoices_model->addCharge($postdata)) {
						
						// load info about product and charge, for history + add history item
						
						$p = $this->products_model->fetchProductInfo($postdata['opm_productid'],true);
						$ct = $this->invoices_model->fetchChargeType($postdata['chargetypeid']);
						
						$historyText = "Charge: " . $p->property . " - " . $p->productname . " - " . $ct->chargetype . " - $" . number_format($postdata['chargeamount'],2) . " ";
						
						if ($postdata['chargeid'])
							$historyText  .= "edited by " . $this->userinfo->username;
						else
							$historyText  .= "added by " . $this->userinfo->username;
						
						$this->opm->addInvoiceHistoryItem($invoiceid, $historyText);
						
						$data['result'] = 1;
						$data['resultText'] = "Charge Added Successfully!";
				
						
					} else {
					
						$data['result'] = 0;
						$data['resultText'] = "Error: Could not add charge.";
					
					}

					
				
				}
			
							
			
			} else { // we are editing or removing a charge
			
				
				if (isset($_POST['removeCharge'])) { // we are removing
				
					
					if ($this->invoices_model->removeCharge($postdata['chargeid'])) {
					
						// load info about product and charge, for history + add history item
					
						$p = $this->products_model->fetchProductInfo($postdata['opm_productid'],true);
						$ct = $this->invoices_model->fetchChargeType($postdata['chargetypeid']);
						
						$historyText = "Charge: " . $p->property . " - " . $p->productname . " - " . $ct->chargetype . " - $" . number_format($postdata['chargeamount'],2) . " removed by " . $this->userinfo->username;
						$this->opm->addInvoiceHistoryItem($invoiceid, $historyText);
					
						$data['result'] = 1;
						$data['resultText'] = "Charge Removed Successfully!";
				
					} else {
					
						$data['result'] = 0;
						$data['resultText'] = "Charge Could Not Be Removed!";
						
					}
					
				
				} 
			
			
			}
			
			
		
		}
			
		$data['invoiceid'] = $invoice->id;
		
		if (isset($_POST['addThisChargeAddAnother'])) {
		
			$data['url'] = "invoices/addEditCharge";
		
		} elseif (isset($_POST['addAnotherProduct'])) {
		
			$data['url'] = "invoices/addProduct";
		
		} else {
		
			$data['url'] = "DONE";
		
		}
		
		$this->load->view('invoices/addCharges', $data);
	
	}
	
	function initInvoice() {
		
		$this->output->enable_profiler(FALSE);
		
		$data = array();
		
		$data['mode'] = "add";
		
		$data['userid'] = $this->userinfo->userid;
		
		$data['user'] = $this->users_model->fetchUserInfo($data['userid']);
		
		
		

		$data['productManagers'] = $this->users_model->fetchInvoiceOwners(false);	
	   	$data['productManagersCC'] = $this->users_model->fetchInvoiceOwners(false);
	   	$data['designers'] = $this->users_model->fetchInvoiceUsers();
	   	$data['companies'] = $this->companies_model->fetchCompanies();
				
		if (isset($_POST['createInvoice'])) {
		
			/*print_r($_POST);
			die();*/
		
			// Add the invoice to the database
			
			$postdata['userid'] = $this->input->post('userid');
			$postdata['referencenumber'] = $this->input->post('referencenumber');
			$postdata['companyid'] = $this->input->post('companyid');
			$postdata['ownerid'] = $this->input->post('ownerid');
			$postdata['attentionid'] = $this->input->post('ownerid');
			$postdata['ccUserIDs'] = $this->input->post('ccUserIDs');
			
			$postdata['billinginfo'] = $this->input->post('billinginfo'); //$data['user']->billinginfo;
			$postdata['taxid'] = $this->input->post('taxid'); //$data['user']->taxid;
			$postdata['invoice_imagepath'] = $this->input->post('invoice_imagepath'); //$data['user']->taxid;
			
			$postdata['currencyid'] = $data['user']->currencyid;
			
			if ($postdata['userid'] != $this->userinfo->userid && $data['user']->staddress == '') {
		
				//die("There is no billing info for this user. You will be redirected to their user page.");
		
			}
			
			if (isset($_FILES['invoiceImage']) && is_uploaded_file($_FILES['invoiceImage']['tmp_name'])) { // we have a file upload!
			
				// upload invoice_image
				
				$config['upload_path'] = $this->config->item('fileUploadPath') . "invoiceImages/";// . $userid . "/";
				//die($config['upload_path']);
				$config['allowed_types'] = 'gif|jpg|png';
				//$config['max_size'] = '2000';
				$config['max_width'] = '400';
				$config['max_height'] = '150';
				$config['encrypt_name'] = true;
		
				$this->load->library('upload', $config);
				
				if ($this->upload->do_upload('invoiceImage')) {
				
					$arrUpload = $this->upload->data();
					$postdata['invoice_imagepath'] = $arrUpload['file_name'];
				
				} else {
				
					//die($this->upload->display_errors());
				}
			
			
			}
			
			
			// get billing info, invoice image and store it in invoices table
			
			if ($id = $this->invoices_model->createInvoice($postdata)) {
				
				$data['result'] = 1;
				$data['resultText'] = "Invoice Created Successfully!";
				$data['id'] = $id;
				
				$this->opm->addInvoiceHistoryItem($id, "Invoice Created by " . $this->userinfo->username);

				
			} else {
			
				$data['result'] = 0;
				$data['resultText'] = "Error: Could not add charge.";
			
			}
		
		
		}
					
		$this->load->view('invoices/initInvoice', $data);
	
	}
	
	function editInvoice($id) {
		
		$data = array();
				
		$data['mode'] = "edit";
		
		if(!$data['invoice'] = $this->invoices_model->fetchInvoice($id))
    		$this->opm->displayError("Invoice Not Found");

		$data['userid'] = $data['invoice']->userid;
		$data['user'] = $this->users_model->fetchUserInfo($data['userid']);

		$data['statuses'] = $this->invoices_model->fetchStatuses();
		$data['productManagers'] = $this->users_model->fetchInvoiceOwners(false);	
	   	$data['productManagersCC'] = $this->users_model->fetchInvoiceOwners(false,$id);
	   	$data['companies'] = $this->companies_model->fetchCompanies();
				
		if ($_POST) {
		
			if ($this->input->post("changeStatus")) {
		
				if (checkPerms('can_change_invoice_status')) {
				
					$invData['statusid'] = $this->input->post("statusid");
					
					if ($this->invoices_model->updateInvoice($id,$invData)) {
					    				    
					    $statusInfo = $this->invoices_model->fetchInvoice($id);
						
						$this->opm->addInvoiceHistoryItem($id, "Invoice Status Changed To " . $statusInfo->status . " by " . $this->userinfo->username);
						
						$data['result'] = 1;
						$data['resultText'] = "Status Changed Successfully!";
						$data['id'] = $id;
					
					} else {
					
						die("didnt work");
					
					}
				
				}
				
			
			} elseif ($this->input->post("forward")) {

		
				if (checkPerms('can_forward_invoices')) {
				
					$invData['ownerid'] = $this->input->post("ownerid");
					
					if ($this->invoices_model->updateInvoice($id,$invData)) {
					    				    
					    $ownerInfo = $this->users_model->fetchUserInfo($invData['ownerid'],true);
						
						$this->opm->addInvoiceHistoryItem($id, "Invoice Forwarded To " . $ownerInfo->username . " by " . $this->userinfo->username);
						
						$arrData = array("forwarder"=>$this->userinfo->username);
						$this->opm->sendInvoiceEmail($id,"invoice_forwarded",$arrData);
						
						$data['result'] = 1;
						$data['resultText'] = "Invoice Forwarded Successfully!";
						$data['id'] = $id;
					
					}
					
				}
				
			
			} else { // not forwarding or changing status, just updating...

		
				$postdata['referencenumber'] = $this->input->post('referencenumber');
				$postdata['ccUserIDs'] = $this->input->post('ccUserIDs');
				$postdata['billinginfo'] = $this->input->post('billinginfo');
				$postdata['taxid'] = $this->input->post('taxid');
				$postdata['companyid'] = $this->input->post('companyid');
				
				//
				
				if (isset($_FILES['invoiceImage']) && is_uploaded_file($_FILES['invoiceImage']['tmp_name'])) { // we have a file upload!
			
					// upload invoice_image
					
					$config['upload_path'] = $this->config->item('fileUploadPath') . "invoiceImages/";// . $userid . "/";
					//die($config['upload_path']);
					$config['allowed_types'] = 'gif|jpg|png';
					//$config['max_size'] = '2000';
					$config['max_width'] = '400';
					$config['max_height'] = '150';
					$config['encrypt_name'] = true;
			
					$this->load->library('upload', $config);
					
					if ($this->upload->do_upload('invoiceImage')) {
					
						$arrUpload = $this->upload->data();
						$postdata['invoice_imagepath'] = $arrUpload['file_name'];
					
					} else {
					
						//die($this->upload->display_errors());
					}
				
				
				}
		
				// get billing info, invoice image and store it in invoices table
				
				if ($this->invoices_model->updateInvoice($id,$postdata)) {
					
					$data['result'] = 1;
					$data['resultText'] = "Invoice Updated Successfully!";
					$data['id'] = $id;
					
				} else {
				
					$data['result'] = 0;
					$data['resultText'] = "Error: Could not update.";
				
				}
			
			}
		
		}
					
		$this->load->view('invoices/initInvoice', $data);
	
	}
	
	function submit() { // construct a search url from a form submission

    	global $searchArgList;
    	
    	// if we entered an invoice #, redirect to that invoice.
    	
    	if ($invoiceid = $this->input->post('invoiceid')) {
    	
    		if ($this->invoices_model->fetchInvoice($invoiceid)) {
    		
    			redirect("/invoices/edit/" . $invoiceid);
    		
    		} else {
    		
    			$this->opm->displayError("Invoice # ".$invoiceid." was not found.");
    		
    		}
    	
    	}

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
    
    function submitRoyaltyReport() { // construct a search url from a form submission
		
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";*/
		
    	global $royaltyReportArgList;

		foreach ($royaltyReportArgList as $key=>$data) {
			
			if ($data == 'startdate' || $data == 'enddate') { // if it's a date, convert to timestamp (for startdate, enddate)
				
				$dateString = $this->input->post($data);
				
				if ($dateString) {
				
					$splitDate = explode("-", $dateString);
					
					if (is_array($splitDate))
						$segments[$data] = @mktime(1,0,0,$splitDate[0],$splitDate[1],$splitDate[2]);
					else
						$segments[$data] = 0;
						
				} else {
				
					$segments[$data] = 0;
				
				}
			
			} else if ($this->input->post($data)) {
			
				$segments[$data] = $this->input->post($data);
			
			} else {
			
				$segments[$data] = 0;	
			
			}
			
		}
		
		$url = "/invoices/royaltyReport/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		//echo "<br><br>" . $url;
		//die();
		
		redirect($url);
    
    }
    
    function submitReport() { // construct a search url from a form submission
		
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";*/
		
    	global $reportArgList;

		foreach ($reportArgList as $key=>$data) {
			
			if (is_array($this->input->post($data)))  {
			
				$segments[$data] = implode(",",$this->input->post($data));
			
			} else if ($data == 'startdate' || $data == 'enddate') { // if it's a date, convert to timestamp (for startdate, enddate)
				
				$dateString = $this->input->post($data);
				
				if ($dateString) {
				
					$splitDate = explode("-", $dateString);
					
					if (is_array($splitDate))
						$segments[$data] = @mktime(1,0,0,$splitDate[0],$splitDate[1],$splitDate[2]);
					else
						$segments[$data] = 0;
						
				} else {
				
					$segments[$data] = 0;
				
				}
			
			} else if ($this->input->post($data)) {
			
				$segments[$data] = $this->input->post($data);
			
			} else {
			
				$segments[$data] = 0;	
			
			}
			
		}
		
		$url = "/invoices/generateReport/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		//echo "<br><br>" . $url;
		//die();
		
		redirect($url);
    
    }
    
    function copyInvoice($id) {
    
    	if (checkPerms('can_copy_invoices',true)) {
    	
    		// copy invoice and insert notes.
    		
    		if ($newId = $this->invoices_model->copyInvoice($id)) {
    		
    			$this->opm->addInvoiceHistoryItem($newId, "Invoice Created by " . $this->userinfo->username .", copied from Invoice # " . $id);

    			$this->opm->displayAlert("Invoice copied successfully to ID # " . $newId, "/invoices/edit/" . $newId);
				return true;
    		
    		} else {
    		
    			$this->opm->displayError("Could not copy invoice.");
    			return false;
    			
    		
    		}
    	
    	}
    
    }
    
    function refreshBillingInfo($id) {
    
    	if (checkPerms('can_refresh_billing_info',true)) {
    	
    		// copy invoice and insert notes.
    		
    		if ($this->invoices_model->refreshBillingInfo($id)) {
    		
    			$this->opm->addInvoiceHistoryItem($id, "Invoice Billing Info refreshed by " . $this->userinfo->username);

    			$this->opm->displayAlert("Invoice info refreshed successfully!", "/invoices/edit/" . $id);
				return true;
    		
    		} else {
    		
    			$this->opm->displayError("Could not refresh info.");
    			return false;
    			
    		
    		}
    	
    	}
    
    }
    
    function importPayments() {
	    
	    $ftpPath = $this->config->item('ftpPath');
	    $inputDir = $ftpPath . "Output/";
	    $errors = "";
	    
	    if ($handle = opendir($inputDir)) {
	    
		    while (false !== ($entry = readdir($handle))) {

		    
		    	if (substr($entry,0,9) == "Payments_") {
			    	
			    	$payXml = simplexml_load_file($inputDir . $entry);
			    	
			    	echo "<pre>";
			    	//print_r($payXml);
			    	
			    	
			    	foreach ($payXml as $p) {
				    	
				    	echo "---PAYMENT---";
				    	//print_r($p);
				    	//die();
				    	
				    	$payArray = array();
				    				    	
				    	$payArray['id'] = (integer)$p->Id;
				    	
				    	$sql = "SELECT * FROM opm_invoices WHERE id = " . $this->db->escape($payArray['id']);
				    	$query = $this->db->query($sql);
				    	
				    	if (1==1 || $query->num_rows() > 0) { // we have a valid invoice
					    	
					    	if ($i = $query->row())
					    		$payArray['total'] = $i->total;
					    	
					    	// check that payment amount matches invoice total!
					    	$payArray['paymentamount'] = (float)str_replace(",", "", $p->PaymentAmount);
					    	
					    	if ((float)$payArray['total'] != $payArray['paymentamount'])
					    		$errors .= "\n Invoice # " . $p->Id . " from file " . $entry . " was not found in OPM!";
					    	
					    	$payArray['checknumber'] = (integer)$p->CheckNo;
					    	$payArray['nav_invnumber'] = (string)$p->NAVInvoice;
					    	$payArray['paymentdate'] = strtotime((string)$p->PaymentDate);
					    	
					    	
					    	print_r($payArray);
					    	die($errors);
					    	
				    	} else {
					    	
					    	$errors .= "\n Invoice # " . $p->Id . " from file " . $entry . " was not found in OPM!";
					    	
				    	}
				    	
				    	// lets validate the data first.
				    	
			    	}
			    	
			    	
			    	// actions for each file
			    	
			    	die($errors);
			    	
		    	}
		    
		    	
		  
		    }
		    
		    // actions for whole batch
		    
		}
	    
	   // $payXml = simplexml_load_file($path . 'songs.xml');
	    
    }
    
    // AJAX FUNCTIONS...
    
    function checkRef() {
	   
	   
	    $referenceNum = $this->input->post('referencenum');
	    $userID = $this->input->post('userid');
	    
	   // die("You sent me a reference num of:" . $referenceNum . " with userid of:" . $userID );

	    
	    if ($this->invoices_model->doesRefExist($referenceNum,$userID))
	    	die("1");
	    else
	    	die("0");
	    
	    
	    
    }
    
    function ajaxGetProducts($propertyid) {
    
    	$this->output->enable_profiler(FALSE);
        		
		$data['products'] = $this->products_model->fetchProducts(false,null,null,$propertyid);

		$this->load->view('invoices/ajax/getProducts', $data);
		
    
    }
    
    function ajaxGetImage($opm_productid) {
    
    	$this->output->enable_profiler(FALSE);
        		
		if (!$data['p'] = $this->products_model->fetchProductInfo($opm_productid))
			die("error");

		$this->load->view('invoices/ajax/getImage', $data);
		
    
    }
    
    function ajaxGetChargeDetails($invoiceid,$chargetypeid) {
        		
        $this->output->enable_profiler(FALSE);
		
		if (!$data['c'] = $this->invoices_model->fetchChargeType($chargetypeid))
			die("error");
			
		if(!$data['invoice'] = $this->invoices_model->fetchInvoice($invoiceid))
    		$this->opm->displayError("Invoice Not Found");
			
		$data['user'] = $this->users_model->fetchUserInfo($data['invoice']->userid);


		$this->load->view('invoices/ajax/getChargeDetail', $data);
		
    
    }
    
    
    function ajaxAssignChannelCode() {    
    
    	$this->output->enable_profiler(FALSE);
        
        $channelCode = $this->input->post('channelcode');
        $chargeID = $this->input->post('chargeid');
        $all = $this->input->post('all');
 
        
        /*if ($channelCode == '700') { // make sure we are assigning harley channel code to legit harley prop
        
        	if (!$this->invoices_model->checkHarleyAssignment($chargeID))
        		die("noharley");
        
        }*/
        
        if ($all) {
        
        	if ($chargeInfo = $this->invoices_model->assignAllChannelCodes($chargeID,$channelCode)) {
			
				$this->opm->addInvoiceHistoryItem($chargeInfo->invoiceid, "Channel Code " . $channelCode . " assigned to all charges by " . $this->userinfo->username);
				die('allchanged');
			
			} else { 
			
				die('error');
				
			}
        
    	} else if ($chargeInfo = $this->invoices_model->assignChannelCode($chargeID,$channelCode)) {
       	
			$this->opm->addInvoiceHistoryItem($chargeInfo->invoiceid, "Channel Code " . $channelCode . " assigned to charge " . $chargeInfo->property . " - " . $chargeInfo->productname . " - " . $chargeInfo->chargetype . " - $" . number_format($chargeInfo->chargeamount,2) . " by " . $this->userinfo->username);
       	
       		die($channelCode);
       	
       	} else {
       	
       		die('error');
       	
       	}
        
       
       
        
	       	
       	
        
        
        print_r($_POST);
        die();
        
    }
	 
    
}



?>