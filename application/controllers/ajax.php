<?php
class Ajax extends CI_Controller {

	function __construct()
    {
    
    	
    	parent::__construct();
    	
    	$this->output->enable_profiler(FALSE);
    	
   // 	$this->load->model('users_model');
    }

	function index() 
	{

	}
	
	function changeGroup() {
	
	
		$this->opm->checkLogin();
				
		$this->load->model('usergroups_model');
		$this->load->model('history_model');
	
		$opm_productid = $this->input->post('opm_productid');
		$usergroupid = $this->input->post('usergroupid');
		$onoff = $this->input->post('onoff');
		
 		
		if ($onoff) { // we are activating this usergroup for this product!
		
			if ($this->usergroups_model->addUsergroupToProduct($opm_productid,$usergroupid)) {
			 	
			 	$usergroup = $this->usergroups_model->getUsergroupName($usergroupid);
			 	$this->opm->addHistoryItem($opm_productid,"Product made visible to " . $usergroup . " by " . $this->userinfo->username); 
				echo 'activated';
				return true;
			
			} 
			
				
		} else { // we are deactivating
		
			if ($this->usergroups_model->removeUsergroupFromProduct($opm_productid,$usergroupid)) {
			 
			 	$usergroup = $this->usergroups_model->getUsergroupName($usergroupid);
			 	$this->opm->addHistoryItem($opm_productid,"Product made hidden to " . $usergroup . " by " . $this->userinfo->username); 
				echo 'deactivated';
				return true;
			
			}
				
		}
		
						
	}
	
	function updateUsergroups() {
	
		//print_r($_POST);
		//die();
	
		$this->opm->checkLogin();
				
		$this->load->model('usergroups_model');
		$this->load->model('history_model');
	
		$opm_productid = $this->input->post('opm_productid');
		$arrUGids = $this->input->post('usergroupids');
		$parentid = $this->input->post('parentid');
		
		if ($arrUGids == "null" || !$arrUGids) {

			$arrUGids = array();
			
		}
		
		if ($opm_productid && is_array($arrUGids)) {
		
			if ($this->usergroups_model->updateProductUsergroups($opm_productid,$arrUGids,$parentid)) {
			 
				die("SUCCESS");
				
			} else {
			
				die("ERROR");
			
			}
			
		} else {
		
			die("ERROR");
		
		}
		
		
		
						
	}
	

	function changeTerritory() {
		
		
		$this->opm->checkLogin();
		
		$this->load->model('territories_model');
	
		$opm_productid = $this->input->post('opm_productid');
		$territoryid = $this->input->post('territoryid');
		$onoff = $this->input->post('onoff');
		
		
		if ($onoff) { // we are activating this territory for this product!
		
			$sql = "INSERT INTO opm_products_territories (opm_productid,territoryid) VALUES (".addslashes($opm_productid).", ".addslashes($territoryid).")";
			
			if ($this->db->query($sql)) {
				
				$t = $this->territories_model->fetchTerritory($territoryid);
				$this->opm->addHistoryItem($opm_productid, $t->territory . " added to territories by " . $this->userinfo->username); 

				
				if ($this->opm->updateAvailableTerritories($opm_productid))
					echo "activated";
			
			}
				
				
		} else { // we are deactivating
		
			$sql = "DELETE FROM opm_products_territories WHERE opm_productid = ".addslashes($opm_productid)." AND territoryid = ".addslashes($territoryid);
			
			if ($this->db->query($sql)) {
			
				$t = $this->territories_model->fetchTerritory($territoryid);
				$this->opm->addHistoryItem($opm_productid, $t->territory . " removed from territories by " . $this->userinfo->username);
			
				if ($this->opm->updateAvailableTerritories($opm_productid))
					echo "deactivated";
					
			}
				
				
		}
		
	}
	
	function changeRight() {
		
		
		$this->opm->checkLogin();
		
		$this->load->model('rights_model');
	
		$opm_productid = $this->input->post('opm_productid');
		$rightid = $this->input->post('rightid');
		$onoff = $this->input->post('onoff');
		
		
		if ($onoff) { // we are activating this right for this product!
		
			$sql = "INSERT INTO opm_products_rights (opm_productid,rightid) VALUES (".addslashes($opm_productid).", ".addslashes($rightid).")";
			
			if ($this->db->query($sql)) {
				
				$t = $this->rights_model->fetchRight($rightid);
				$this->opm->addHistoryItem($opm_productid, $t->right . " added to rights by " . $this->userinfo->username); 

				
				if ($this->opm->updateAvailableRights($opm_productid))
					echo "activated";
			
			}
				
				
		} else { // we are deactivating
		
			$sql = "DELETE FROM opm_products_rights WHERE opm_productid = ".addslashes($opm_productid)." AND rightid = ".addslashes($rightid);
			
			if ($this->db->query($sql)) {
			
				$t = $this->rights_model->fetchRight($rightid);
				$this->opm->addHistoryItem($opm_productid, $t->right . " removed from rights by " . $this->userinfo->username);
			
				if ($this->opm->updateAvailableRights($opm_productid))
					echo "deactivated";
					
			}
				
				
		}
		
	}
		
	
	function changePropertyTerritory() {
	
		$propertyid = $this->input->post('propertyid');
		$territoryid = $this->input->post('territoryid');
		$onoff = $this->input->post('onoff');
		
		
		if ($onoff) { // we are activating this territory for this product!
		
			$sql = "INSERT INTO opm_property_territories (propertyid,territoryid) VALUES (".addslashes($propertyid).", ".addslashes($territoryid).")";
			
			
			
			if ($this->db->query($sql)) {
			
				if ($this->opm->updateAvailableTerritoriesProp($propertyid))
					echo "activated";
			
			}
				
				
		} else { // we are deactivating
		
			$sql = "DELETE FROM opm_property_territories WHERE propertyid = ".addslashes($propertyid)." AND territoryid = ".addslashes($territoryid);
			
			if ($this->db->query($sql)) {
				
				if ($this->opm->updateAvailableTerritoriesProp($propertyid))
					echo "deactivated";
			
			}
				
				
		}
		
	}
	
	function changeOfficeTerritory() {
	
		$officeid = $this->input->post('officeid');
		$territoryid = $this->input->post('territoryid');
		$onoff = $this->input->post('onoff');
		
		
		if ($onoff) { // we are activating this territory for this product!
		
			$sql = "INSERT INTO opm_office_territories (officeid,territoryid) VALUES (".addslashes($officeid).", ".addslashes($territoryid).")";
			
			
			
			if ($this->db->query($sql)) {
			
				echo "activated";
			
			}
				
				
		} else { // we are deactivating
		
			$sql = "DELETE FROM opm_office_territories WHERE officeid = ".addslashes($officeid)." AND territoryid = ".addslashes($territoryid);
			
			if ($this->db->query($sql)) {
				
				echo "deactivated";
			
			}
				
				
		}
		
	}
	
	function changePropertyRight() {
	
	
		$propertyid = $this->input->post('propertyid');
		$rightid = $this->input->post('rightid');
		$onoff = $this->input->post('onoff');
		
		
		if ($onoff) { // we are activating this right for this product!
		
			$sql = "INSERT INTO opm_property_rights (propertyid,rightid) VALUES (".addslashes($propertyid).", ".addslashes($rightid).")";
			
			
			
			if ($this->db->query($sql)) {
			
				if ($this->opm->updateAvailableRightsProp($propertyid))
					echo "activated";
			
			}
				
				
		} else { // we are deactivating
		
			$sql = "DELETE FROM opm_property_rights WHERE propertyid = ".addslashes($propertyid)." AND rightid = ".addslashes($rightid);
			
			if ($this->db->query($sql)) {
				
				if ($this->opm->updateAvailableRightsProp($propertyid))
					echo "deactivated";
			
			}
				
				
		}
		
	}
	
	
	function changePropertyChannel() {
	
	
		$propertyid = $this->input->post('propertyid');
		$channelid = $this->input->post('channelid');
		$onoff = $this->input->post('onoff');
		
		
		if ($onoff) { // we are activating this right for this product!
		
			$sql = "INSERT INTO opm_property_channels (propertyid,channelid) VALUES (".addslashes($propertyid).", ".addslashes($channelid).")";
			
			
			
			if ($this->db->query($sql)) {
			
				if ($this->opm->updateAvailableChannelsProp($propertyid))
					echo "activated";
			
			}
				
				
		} else { // we are deactivating
		
			$sql = "DELETE FROM opm_property_channels WHERE propertyid = ".addslashes($propertyid)." AND channelid = ".addslashes($channelid);
			
			if ($this->db->query($sql)) {
				
				if ($this->opm->updateAvailableChannelsProp($propertyid))
					echo "deactivated";
			
			}
				
				
		}
		
	}

	
	
	function fetchDefaultProductDescription() {
	
		$propertyid = $this->input->post("propertyid");
	
		$this->load->model('properties_model');
		
		$property = $this->properties_model->fetchPropertyInfo($propertyid);
		
		die($property->default_productdesc);
	
	}
	
	
	function fetchProductLineSelect($propertyid,$opm_productid = 0,$random_str = '') {
	
		$this->load->model('productlines_model');
	
		//$propertyid = $this->input->post('propertyid');
		
		$data['productLines'] = $this->productlines_model->fetchProductLines($propertyid,true,$opm_productid);
		$data['numProductLines'] = $data['productLines']->num_rows();
		$this->load->view('product/productLineSelect', $data);
		
		
	}
	
	function fetchProductLineJson($propertyid,$opm_productid = 0,$random_str = '') {
	
	
		$this->load->model('productlines_model');
			
		$data = array();
		
		$query = $this->productlines_model->fetchProductLines($propertyid,true,$opm_productid);
		
		foreach ($query->result() as $row) {
		
			$data[] = $row;
		
		}
		
		//$data['numProductLines'] = $query->num_rows();
		
		
		
		$json = json_encode($data);	
		
		//echo "<pre>";
		die($json);
		
	}
	
	function invoiceQuery($query = "0") { // "get" mode is for testing
		
		$this->opm->checkLogin();
		$this->opm->opmInit();
		
		$this->load->model('products_model');
		
		$data = array();
		
		if ($query != "0") {
			$data['query'] = $query;
			$this->output->enable_profiler(TRUE);
		} else {
			$data['query'] = $this->input->post("query");
		}
		
		$products = $this->products_model->quickFetchProducts($data['query']);
		
		// create 'suggestions' string
		
		$suggestions = "";
		
		foreach ($products->result() as $p) {
				
			$suggestions .= "'".$p->opm_productid." // ".$p->property." // ".$p->productname."',";
			
		}
		
		// remove last comma from suggestions string, put in data array
			
		$data['suggestions'] = substr($suggestions, 0, strlen($suggestions)-1);
	
		
		$this->load->view('invoices/ajaxReturnProducts', $data);

	
	}
	
	
	function addInvoiceItem() { // returns a div containing the item or 
	
	//	print_r($_POST);
	//	die();
	
		
		$strItem = $this->input->post("searchProducts");
		$invoiceid = $this->input->post("invoiceid");
		
		$arrSplitItem = split("//",$strItem);
		$opm_productid = trim($arrSplitItem[0]);
		
		// MAKE SURE WE HAVE PERMS TO ADD THIS PRODUCT!!
		
		$this->load->model('products_model');
		$this->load->model('invoices_model');
		
		if (!$p = $this->products_model->fetchProductInfo($opm_productid,true)) // use litemode to minimize server looooad
			die("error");
		
		$data['locked'] = false;
		
		// add item to DB
		
		$id = $this->invoices_model->addInvoiceItem($invoiceid,$p->opm_productid);
		
		$data['i'] = $this->invoices_model->fetchInvoiceItem($id);
		
		$data['chargeTypes'] = $this->invoices_model->fetchChargeTypes();
		
		//$data['i'] = $arrProduct;
		
		// we need to determine if $altRow should be on or not.
		
		$invSize = $this->invoices_model->getInvoiceSize($invoiceid);
		
		if ($invSize % 2) // number is odd
			$data['altRow'] = 0;
		else
			$data['altRow'] = 1;
 		
		
		$itemHTML = $this->load->view('invoices/lineItem', $data, true);
	
		die($itemHTML);
	
	}
	
	function addInvoiceNote() { // returns a div containing the item or 
	
	//	print_r($_POST);
	//	die();
	
		$this->opm->checkLogin();
		
		$strNote = $this->input->post("note");
		$invoiceid = $this->input->post("invoiceid");

		$this->load->model('invoices_model');

		
		if ($id = $this->invoices_model->addInvoiceNote($invoiceid,$strNote))
			die("success");
		else
			die("error");
		
			
	}
	
	function getBillingInfo($userid) {
	
		$this->load->model('users_model');
		
		if (!$data['u'] = $this->users_model->fetchUserInfo($userid)) {
			
			die("error");
		
		} else {
		
			$html = $this->load->view('invoices/billingInfo', $data, true);
			die($html);
		
		}
		
	}
	
	function invoiceHistoryView($id) {
	
		$this->load->model('invoices_model');
		$data['history'] = $this->invoices_model->fetchInvoiceHistory($id);
		
		$html = $this->load->view('invoices/ajaxHistory', $data, true);
		die($html);
		
	
	}
	
	function getInvoiceNotes($id) {
	
		$html = $this->opm->getInvoiceNotes($id);
		
		echo $html;
	
	}
	
	function getInvoiceBillingInfo($userid) {
	
		$this->load->model('users_model');
		$data['user'] = $this->users_model->fetchUserInfo($userid);
		
		$html = $this->load->view('invoices/ajax/getBillingInfo', $data, true);
		die($html);
	
	}
	
	
	function getInvoiceContents($id,$mode = '') { 
	
		$this->opm->checkLogin();
		
		$html = $this->opm->getInvoiceContents($id,$mode);
		
		echo $html;
		
		
		/*
		
		$this->load->model('invoices_model');
		
		$this->invoices_model->updateTotal($id);

		
		$invoice = $this->invoices_model->fetchInvoice($id);
		$data['invoice'] = $invoice;
		$data['mode'] = $mode;
		
		$pid = 0;
		
		$sortedItems = array();
		
		foreach ($invoice->items as $i) {
		
			if ($i->opm_productid != $pid) { // create distinct prod entry
			
				$sortedItems[$i->opm_productid] = array (
					"opm_productid"=>$i->opm_productid,
					"default_imageid"=>$i->default_imageid,
					"property"=>$i->property,
					"productname"=>$i->productname,
					"category"=>$i->category
				);
				
				$sortedItems[$i->opm_productid]['totalCharges'] = 0;
			
			}
			
			// regardless, add charge info to charges sub array
			
			$sortedItems[$i->opm_productid]["charges"][] = array (
				"id"=>$i->id,
				"chargetypeid"=>$i->chargetypeid,
				"chargeamount"=>$i->chargeamount,
				"chargetype"=>$i->chargetype,
				"opm_productid"=>$i->opm_productid,
				"hours"=>$i->hours,
				"hourlyrate"=>$i->hourlyrate,
				"channelcode"=>$i->channelcode,
				"notes"=>$i->notes
			
			);
			
			// get total charges for products
			
			$sortedItems[$i->opm_productid]['totalCharges'] += $i->chargeamount;
			
			$pid = $i->opm_productid;
		
		}
		
		$data['sortedItems'] = $sortedItems;
		
		if ($invoice->statusid != '1') {
    		$data['locked'] = true;
    		$data['formDisabled'] = true;
    	} else {
    		$data['locked'] = false;
    	}

		$data['chargeTypes'] = $this->invoices_model->fetchChargeTypes();
		$data['statuses'] = $this->invoices_model->fetchStatuses();
		
		$html = "";
		
		$data['altRow'] = 0; // for alt row colors

		foreach ($sortedItems as $i) {

			$i['productText'] = $i['property'] . " - " . $i['productname'] . " - " . $i['category'];
			
			$maxLength = 50;
			
			if (strlen($i['productText']) > $maxLength) {
			
				$i['productText'] = substr($i['productText'],0,$maxLength) . "...";
			
			}
			
			$data['i'] = $i;
			$html .= $this->load->view('invoices/lineItem', $data, true);
			
			if ($data['altRow'] == 0)
				$data['altRow'] = 1;
			else
				$data['altRow'] = 0;
		
		}
		
		// print total
		
		$html .= $this->load->view('invoices/ajax/invTotal', $data, true);
			
		
		echo $html;
				
		die();
		
		*/
		
	
	}
	
}
?>