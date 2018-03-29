<?

class Orders_model extends CI_Model {

    function OrdersModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    
    function fetchOrders($returnTotal = false, $offset = 0, $perPage = null, $customerid = null, $statusid = null, $customerpo = null, $orderBy = null, $orderByAscDesc = null) {
    	
		$validOrderbys = array(	"id"=>"opm_ws_orders.id",
								"customername"=>"opm_ws_customers.name",
								"statusid"=>"opm_ws_orders.statusid",
								"orderdate"=>"opm_ws_orders.date");
    	
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT opm_ws_orders.*,users.username AS customername,opm_ws_statuses.status,opm_usergroups.usergroup as companyname, opm_currencies.currencysymbol";
    	else 
    		$sql = "SELECT opm_ws_orders.id";

    			
    	$sql .=	" FROM opm_ws_orders ";
    	
    	
    		$sql .= "
    	       	
    			LEFT JOIN opm_ws_statuses ON opm_ws_statuses.id = opm_ws_orders.statusid
    			LEFT JOIN users ON users.userid = opm_ws_orders.userid
    			LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
    			LEFT JOIN opm_currencies ON opm_currencies.id = opm_ws_orders.currencyid
    		
    			";
    	

    	$sql .= " WHERE opm_ws_orders.id <> 0 ";
    	
    	//if (USE_PERMISSION_QUERY)
    	//	$sql .= " AND canview.id IS NOT NULL";
    	
    
	    
	   
    			
    	if ($statusid) {
    		
    		if (is_numeric($statusid))
    			$sql .= " AND opm_ws_orders.statusid = '".$statusid."'";
    		else // status id is comma-delim list
    			$sql .= " AND opm_ws_orders.statusid IN (".$statusid.")";
    	
    	}
    	
       	$sql .=" GROUP BY opm_ws_orders.id";
    	
    	
    	if ($orderBy) {
    	
    		//die("orderby:".$orderBy);
    	
    		$sql .= " ORDER BY " . $validOrderbys[$orderBy];
    		
    		if ($orderByAscDesc == 'desc')
    			$sql .= " DESC";
    		else
    			$sql .= " ASC";
    	
    	
    	}
    	
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";


    	$query = $this->db->query($sql);
    	
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    /*function fetchChargeTypeDetail($propertyid,$chargetypeid) {
    	
    	$sql = "
    	
    		SELECT opm_invoice_detail.*, opm_products.default_imageid
			FROM opm_invoice_detail
			LEFT JOIN opm_invoices ON opm_invoices.id = opm_invoice_detail.invoiceid
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
			WHERE opm_invoice_detail.chargetypeid = ".$this->db->escape($chargetypeid). " "; 
			
    	if ($propertyid)
    		$sql .= " AND opm_products.propertyid = ".$this->db->escape($propertyid)." ";
    		
    	$query = $this->db->query($sql);

    	return $query;
    
    }*/
    
   
    
    function fetchInvoice($id) {
    
    
    	$sql = "SELECT opm_invoices.*, users.username, users.nv_customerid, users2.username AS owner, users3.username AS attention, opm_invoice_statuses.status, opm_currencies.currency, opm_currencies.currencysymbol, opm_currencies.currencycode, opm_companies.name AS companyname, opm_companies.billto
    			FROM opm_invoices
    			LEFT JOIN opm_currencies ON opm_currencies.id = opm_invoices.currencyid
    			LEFT JOIN opm_companies ON opm_companies.id = opm_invoices.companyid
    			LEFT JOIN users ON users.userid = opm_invoices.userid
    			LEFT JOIN users AS users2 ON users2.userid = opm_invoices.ownerid
    			LEFT JOIN users AS users3 ON users3.userid = opm_invoices.attentionid
    			LEFT JOIN opm_invoice_statuses ON opm_invoice_statuses.id = opm_invoices.statusid
    			WHERE opm_invoices.id = " . $this->db->escape($id);
    
    	$query = $this->db->query($sql);
    	
    	if (!$invoice = $query->row())
    		return false;
    		
    	// get items
    	
    	$sql = "SELECT opm_invoice_detail.*, opm_products.default_imageid, opm_products.productname, opm_products.designcode, properties.property, properties.nv_propid, categories.category, opm_invoice_charge_types.chargetype, opm_invoice_charge_types.glaccount
    			FROM opm_invoice_detail
    			LEFT JOIN opm_invoice_charge_types ON opm_invoice_charge_types.id = opm_invoice_detail.chargetypeid
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			LEFT JOIN categories ON categories.categoryid = opm_products.categoryid
    			WHERE invoiceid = " . $this->db->escape($id) . "
    			ORDER BY opm_invoice_detail.opm_productid";
    			
    	$query = $this->db->query($sql);
    	
    	$invoice->items = array();
    	
    	foreach ($query->result() as $i)
    		$invoice->items[] = $i;
    		
    	
    	// get ccs
    	
    	$sql = "SELECT users.userid,users.login 
    			FROM opm_invoice_cc
    			LEFT JOIN users ON users.userid = opm_invoice_cc.userid
    			WHERE opm_invoice_cc.invoiceid = " . $this->db->escape($id);
    			
    	$query = $this->db->query($sql);
    	
    	$invoice->cc = array();
    	
    	foreach ($query->result() as $i)
    		$invoice->cc[] = $i;
    		
    	// determine canEdit
    	
    	$CI =& get_instance();
    	
    	// if invoice status is in progress or submitted, allow editing, otherwise don't
    	
    	if (($invoice->statusid == $CI->config->item('invStatusInProgress')) || ($invoice->statusid == $CI->config->item('invStatusSubmitted'))) {
    	
	    	if (isset($CI->userinfo->perms['can_edit_submitted_invoices'])) {
	    		
	    		$invoice->canEdit = true;
	    	
	    	} else {
	    	
	    		if ($invoice->statusid == $CI->config->item('invStatusInProgress'))
	    			$invoice->canEdit = true;
	    		else
	    			$invoice->canEdit = false;
	    	
	    	}
	    	
	    } else {
	    
	    	$invoice->canEdit = false;
	    
	    }
    	
    	return $invoice;
    	
    
    }
    
    function fetchInvoiceItem($id) {
    	
    	
    	$sql = "SELECT opm_invoice_detail.*, opm_products.default_imageid, opm_products.productname, properties.property,categories.category,opm_invoice_charge_types.ishourly,opm_invoice_charge_types.chargetype,opm_invoice_charge_types.id AS chargetypeid
    			FROM opm_invoice_detail
    			LEFT JOIN opm_invoice_charge_types ON opm_invoice_charge_types.id = opm_invoice_detail.chargetypeid
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			LEFT JOIN categories ON categories.categoryid = opm_products.categoryid
    			WHERE opm_invoice_detail.id = " . $this->db->escape($id);
    			
    	$query = $this->db->query($sql);
    	
    	return $query->row();
    	
    
    }
    
    
    function updateTotal($id) {
    	
    	
    	$sql = "SELECT opm_invoice_detail.chargeamount
    			FROM opm_invoice_detail 
    			WHERE invoiceid = " . $this->db->escape($id);
    			
    	$query = $this->db->query($sql);
    	
    	$total = 0.00;
    	
    	foreach ($query->result() as $row)
    		$total += $row->chargeamount;
    		
    	$sql = "UPDATE opm_invoices SET total = " . $this->db->escape($total) . " WHERE id = " . $this->db->escape($id);
    	$this->db->query($sql);
    
    }
    
    
    function refreshBillingInfo($id) {
    
    	$sql = "SELECT opm_invoices.*
    			FROM opm_invoices 
    			WHERE opm_invoices.id = " . $this->db->escape($id);
    			
    	$query = $this->db->query($sql);	
    	$invoice = $query->row();
    
    	$CI =& get_instance();
    	$CI->load->model('users_model');
    	
    	$user = $CI->users_model->fetchUserInfo($invoice->userid);
    	
    	/*echo "<pre>";
    	print_r($user);
    	die();*/
    
    	$sql = "UPDATE opm_invoices 
    			SET staddress = ".$this->db->escape($user->staddress).",
    			staddress2 = ".$this->db->escape($user->staddress2).",
    			city = ".$this->db->escape($user->city).",
    			state = ".$this->db->escape($user->state).",
    			zip = ".$this->db->escape($user->zip).",
    			taxid = ".$this->db->escape($user->taxid).",
    			invoice_imagepath = ".$this->db->escape($user->invoiceimage_path).",
    			currencyid = ".$this->db->escape($user->currencyid)."
    			
    			WHERE id = " . $this->db->escape($id);

    	if ($this->db->query($sql))
    		return true;
    	else
    		return false;
    
    }
    
    function getInvoiceSize($id) {
    	
    	
    	$sql = "SELECT count(id) AS thecount FROM opm_invoice_detail WHERE invoiceid = " . $this->db->escape($id);
    			
    	$query = $this->db->query($sql);
    	$row = $query->row();
    	$count = $row->thecount;
    	
    	return $count;
    	
    
    }
    
    function fetchInvoiceHistory($id) {
    	
    	
    	$sql = "SELECT * FROM opm_invoice_history WHERE invoiceid = " . $this->db->escape($id) . " ORDER BY timestamp";
    			
    	$query = $this->db->query($sql);
		return $query;
    	
    
    }
    
     function fetchInvoiceNotes($id) {
    	
    	
    	$sql = "SELECT opm_invoice_notes.*, users.username 
    			FROM opm_invoice_notes
    			LEFT JOIN users ON users.userid = opm_invoice_notes.userid
    			WHERE invoiceid = " . $this->db->escape($id) . " 
    			ORDER BY timestamp";
    			
    	$query = $this->db->query($sql);
		return $query;
    	
    
    }
    
    function addInvoiceItem($invoiceid,$opm_productid) {
    
    	$sql = "INSERT INTO opm_invoice_detail (invoiceid,opm_productid)
    			VALUES (".$this->db->escape($invoiceid).",".$this->db->escape($opm_productid).")";
    
    	
    
    	if ($this->db->query($sql)) {
    	
    		$id = $this->db->insert_id();
    		
    		$sql = "UPDATE opm_invoices SET lastmodified = " . mktime() . " WHERE id = " . $this->db->escape($invoiceid);
    		
    		return $id;
    	
    	} else {
    	
    		return false;
    	
    	}
    		
    }
    
    function addCharge($postdata) {
    	
    	//print_r($postdata);
    	//die();
    	
    	// if it's hourly determine rate + total amount of charge
    
    	if ($postdata['hours']) {
    	
   			$postdata['chargeamount'] = $postdata['hours'] * $postdata['hourlyrate'];
    	
    	}
    	
    	if (!$postdata['opm_productid'] && !$postdata['chargeid'])
    		return false;
    	
		
		if ($postdata['chargeid']) { // we are editing an existing charge
		
		
			$sql = "UPDATE opm_invoice_detail SET
						
						chargetypeid = " . $this->db->escape($postdata['chargetypeid']).",
						hours = " . $this->db->escape($postdata['hours']).",
						hourlyrate = " . $this->db->escape($postdata['hourlyrate']).",
						chargeamount = " . $this->db->escape($postdata['chargeamount']).",
						chargedescription = " . $this->db->escape($postdata['chargedescription']).",
						notes = " . $this->db->escape($postdata['notes'])."
    				
    				WHERE id = " . $this->db->escape($postdata['chargeid']);
		
		
		} else { // we are adding a charge
		
		
			$sql = "INSERT INTO opm_invoice_detail (invoiceid,opm_productid,chargetypeid,hours,hourlyrate,chargeamount,chargedescription,notes)
    				VALUES (".$this->db->escape($postdata['invoiceid']).",".$this->db->escape($postdata['opm_productid']).",".$this->db->escape($postdata['chargetypeid']).",".$this->db->escape($postdata['hours']).",".$this->db->escape($postdata['hourlyrate']).",".$this->db->escape($postdata['chargeamount']).",".$this->db->escape($postdata['chargedescription']).",".$this->db->escape($postdata['notes']).")";
		
		}
    	
    	
    
    	if ($this->db->query($sql)) {
			
			return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
    
    function removeCharge($chargeid) {
    	
    	$sql = "DELETE FROM opm_invoice_detail WHERE id = " . $this->db->escape($chargeid);
    
    	if ($this->db->query($sql)) {
			
			return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
    
    function removeProduct($invoiceid,$opm_productid) {
    	
    	$sql = "DELETE FROM opm_invoice_detail WHERE invoiceid = " . $this->db->escape($invoiceid) . " AND opm_productid = " . $this->db->escape($opm_productid);
    
    	if ($this->db->query($sql)) {
			
			return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
    
    function addInvoiceNote($invoiceid,$note) {
    
    	$sql = "INSERT INTO opm_invoice_notes (invoiceid,note,userid,timestamp)
    			VALUES (".$this->db->escape($invoiceid).",".$this->db->escape($note).",".$this->userinfo->userid.",".mktime().")";
    
    	if ($this->db->query($sql)) {
			
			return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    		
    }
    
    function addInvoiceLINote($chargeid,$note) {
    
    	$sql = "UPDATE opm_invoice_detail
    			SET notes = ".$this->db->escape($note)."
    			WHERE id = " . $this->db->escape($chargeid);

    	if ($this->db->query($sql)) {
			
			return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    		
    }
    
    function removeItem($invoiceDetailID) {
    
    	$sql = "DELETE FROM opm_invoice_detail WHERE id = " . $this->db->escape($invoiceDetailID);
    	
    	if ($this->db->query($sql))
    		return true;
    	else
    		return false;
    		
    }
    
    function updateInvoice($invoiceid,$arrData) {
    	
    	if (isset($arrData['userid']) || isset($arrData['statusid']) || isset($arrData['title']) || isset($arrData['referencenumber']) || isset($arrData['ownerid']) || isset($arrData['ccUserIDs']) || isset($arrData['submitdate']) || isset($arrData['exportdate'])) {
    	
    		if (isset($arrData['ccUserIDs'])) {
    		
    			// cc stuff gets written to a different table, so we gotta deal w differently
    		
    			$nuData['ccUserIDs'] = $arrData['ccUserIDs'];
    			unset($arrData['ccUserIDs']);
    			
    			// first remove current ccs
    			
    			$sql = "DELETE FROM opm_invoice_cc WHERE invoiceid = " . $this->db->escape($invoiceid);
    			$this->db->query($sql);
    			
    			if (is_array($nuData['ccUserIDs'])) {
    			
	    			foreach ($nuData['ccUserIDs'] as $uid=>$onoff) {
	    			
	    				$sql = "INSERT INTO opm_invoice_cc (userid,invoiceid) VALUES (".$this->db->escape($uid).",".$this->db->escape($invoiceid).")";
	    				$this->db->query($sql);
	    			
	    			}
    			
    			
    			}
    			
    		
    		}
    	
    		$sql = "UPDATE opm_invoices
    				SET ";
    				
    		foreach ($arrData as $key=>$data) {
    			
    			$sql .= $key . " = " . $this->db->escape($data) . ", ";
    			
    			
    			// if we are approving invoice, set approvedate as well.
    			
    			if ($key == 'statusid' && $data == $this->config->item('invStatusApproved'))
    				$sql .= "approvedate = " . mktime() . ", ";	
    			
    		
    		}
    		
    		// remove last comma, space
    		
    		$sql = substr($sql, 0 , strlen($sql) - 2);
    				
    		$sql .=	" WHERE id = " . $this->db->escape($invoiceid);
    		
    		
    		// update userinfo if 
    		
    		
    		    		
    		if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    		
    	}
    
    }
    
    function updateInvoiceItem($invoiceDetailID,$arrData) {
    	
    	if ($arrData['opm_productid'] || $arrData['chargetypeid'] || $arrData['chargeamount'] || $arrData['notes']) {
    	
    		$sql = "UPDATE opm_invoice_detail
    				SET ";
    				
    		foreach ($arrData as $key=>$data) {
    			
    			$sql .= $key . " = " . $this->db->escape($data) . ", ";
    		
    		}
    		
    		// remove last comma, space
    		
    		$sql = substr($sql, 0 , strlen($sql) - 2);
    				
    		$sql .=	" WHERE id = " . $this->db->escape($invoiceDetailID);
    		
    		//die($sql);
    		
    		if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    		
    	}
    
    	
    
    }
    
    function createInvoice($postData) { // create invoice db entry and return ID
    
    	/*echo "<pre>";
    	print_r($postData);
    	echo "</pre>";
    	die();*/
    	
    	// update billing info + image.
    	
    	/*$sql = "UPDATE users SET 
    			
    			billinginfo = " . $this->db->escape($postData['billinginfo']) . ", 
    			taxid = " . $this->db->escape($postData['taxid']) . ", 
    			invoiceimage_path = " . $this->db->escape($postData['invoice_imagepath']) . "
    			
    			WHERE userid = " . $this->db->escape($postData['userid']);
    
    	$this->db->query($sql);*/
    	
    	// Get Billing info from userdata to put on invoice.
    	
    	$CI =& get_instance();
    	$CI->load->model('users_model');
    	
    	$user = $CI->users_model->fetchUserInfo($postData['userid']);
    	
    	/*echo "<pre>";
    	print_r($user);
    	die();*/
    	
    
    	$sql = "INSERT INTO opm_invoices (userid,statusid,companyid,ownerid,attentionid,referencenumber,staddress,staddress2,city,state,zip,taxid,vatnumber,invoice_imagepath,currencyid,createdby,createdate)
    			VALUES (".$this->db->escape($postData['userid']).",1,".$this->db->escape($postData['companyid']).",".$this->db->escape($postData['ownerid']).",".$this->db->escape($postData['ownerid']).",".$this->db->escape($postData['referencenumber']).",".$this->db->escape($user->staddress).",".$this->db->escape($user->staddress2).",".$this->db->escape($user->city).",".$this->db->escape($user->state).",".$this->db->escape($user->zip).",".$this->db->escape($user->taxid).",".$this->db->escape($user->vatnumber).",".$this->db->escape($postData['invoice_imagepath']).",".$this->db->escape($postData['currencyid']).",".$this->userinfo->userid.",".mktime().")";
    
    	$this->db->query($sql);
    	$invoiceid = $this->db->insert_id();
    	
    	// add CCs!
    	
    	if (is_array($postData['ccUserIDs'])) {
    	
    		foreach($postData['ccUserIDs'] as $id=>$onoff) {
    		
    			$sql = "INSERT INTO opm_invoice_cc (userid,invoiceid) VALUES (".$this->db->escape($id).",".$this->db->escape($invoiceid).")";
    			$this->db->query($sql);
    		}
    	
    	}
    	
    	// add user notes if present.
    	
    	if ($user->notes && $user->notestoinvoices) {
    	
    		$sql = "INSERT INTO opm_invoice_notes (invoiceid,userid,note,timestamp)
    				VALUES (".$this->db->escape($invoiceid).",".$this->db->escape($CI->userinfo->userid).",".$this->db->escape($user->notes).",".mktime().")";
    		
    		$this->db->query($sql);
    	
    	}
    	
    	return $invoiceid;
    
    }
    
    function fetchStatuses() // return an array of statuses
    {
    
		$sql = "SELECT opm_invoice_statuses.*
				FROM opm_invoice_statuses
				ORDER BY displayorder";

        	
        $query = $this->db->query($sql);
        
        $s = array();
        
        foreach ($query->result() as $r)
        	$s[$r->id] = $r->status;
        
        return $s;
        
    }
    
    function fetchChargeTypes() // return an array of charge types
    {
    
		$sql = "SELECT opm_invoice_charge_types.*, opm_invoice_charge_types.id AS chargetypeid
				FROM opm_invoice_charge_types
				ORDER BY displayorder";
				
		$query = $this->db->query($sql);
        	
		$s = array();
        
        foreach ($query->result() as $r)
        	$s[$r->id] = $r->chargetype;
        
        return $s;
        
    }
    
    function fetchChargeType($id) // return a single charge type
    {
    
		$sql = "SELECT opm_invoice_charge_types.*, opm_invoice_charge_types.id AS chargetypeid
				FROM opm_invoice_charge_types
				WHERE id = " . $this->db->escape($id);
				
		$query = $this->db->query($sql);
		return $query->row();
        
    }
    
    function fetchChannels($propertyid = 0) // return channels
    {
    	
    	if ($propertyid)
			$sql = "SELECT opm_invoice_channels.*, IFNULL(opm_invoice_channel_rates.rate,100) AS rate ";
		else
			$sql = "SELECT opm_invoice_channels.* ";

				
		$sql .=	"FROM opm_invoice_channels ";
				
		if ($propertyid)		
			$sql .= "LEFT JOIN opm_invoice_channel_rates ON opm_invoice_channel_rates.channelcode = opm_invoice_channels.channelcode AND opm_invoice_channel_rates.propertyid = " . $this->db->escape($propertyid);
				
		$sql .=	"ORDER BY channelcode ";
				
		$query = $this->db->query($sql);
        	
		return $query;
        
    }
    
    function fetchChannel($channelcode) // return a single channel
    {
    
		$sql = "SELECT opm_invoice_channels.*
				FROM opm_invoice_channels
				WHERE channelcode = " . $this->db->escape($id);
				
		$query = $this->db->query($sql);
		return $query->row();
        
    }
    
    function checkHarleyAssignment($chargeID) {
       	
       	$sql = "SELECT properties.isharley 
       			FROM opm_invoice_detail
       			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
       			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
       			WHERE opm_invoice_detail.id = " . $this->db->escape($chargeID);
       			
       	$query = $this->db->query($sql);
       	$row = $query->row();
       	
       	if ($row->isharley == '1')
       		return true;
       	else
       		return false;
    
    }
    
    function assignChannelCode($chargeID,$channelCode) {
    
    	$sql = "UPDATE opm_invoice_detail SET channelcode = " . $this->db->escape($channelCode) . " WHERE id = " . $this->db->escape($chargeID);
				
		if($this->db->query($sql)) {
			
			// send back relevant info about the charge for history, etc.
			
			$sql = "SELECT opm_invoice_detail.*, opm_products.productname, properties.property,opm_invoice_charge_types.chargetype
					FROM opm_invoice_detail
					LEFT JOIN opm_invoice_charge_types ON opm_invoice_charge_types.id = opm_invoice_detail.chargetypeid
					LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
					LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
					WHERE opm_invoice_detail.id = " . $this->db->escape($chargeID);
					
			$query = $this->db->query($sql);
			return $query->row();
			
		} else {
			
			return false;
    	
    	}
    
    }
    
    
    
    function fetchMissingInvoiceCodes($arrInvoiceIDs) {
    
    	$strInvoiceIDs = implode(",", $arrInvoiceIDs);
    
    	$sql = "";
    
    	/*$sql .= "
    	
    		SELECT DISTINCT opm_products.opm_productid, opm_products.productname AS productname, properties.propertyid as propertyid, properties.property AS property, 0 as userid, '' AS username 
			FROM opm_invoice_detail
			LEFT JOIN opm_invoices ON opm_invoices.id = opm_invoice_detail.invoiceid
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
			WHERE opm_invoices.statusid <> '".$this->config->item('invStatusDeleted')."' AND opm_products.designcode = 0 ";
			
		if ($strInvoiceIDs)
			$sql .= " AND opm_invoice_detail.invoiceid IN (".$strInvoiceIDs.") ";
			
		$sql .=" UNION ALL ";
		
		*/
		
		// MAKE SURE WE HAVE NAVISION PROPERTY CODES
		
		$sql .="
			
			SELECT DISTINCT 0 as opm_productid, '' AS productname, properties.propertyid, properties.property AS property, 0 as userid, '' AS username, 0 AS invoiceid, 0 invoicedetailid, 0 AS hasnobodystyle
			FROM opm_invoice_detail
			LEFT JOIN opm_invoices ON opm_invoices.id = opm_invoice_detail.invoiceid
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
			WHERE opm_invoices.statusid <> '".$this->config->item('invStatusDeleted')."' AND properties.nv_propid = 0 ";
			
		if ($strInvoiceIDs)
			$sql .= " AND opm_invoice_detail.invoiceid IN (".$strInvoiceIDs.") ";
			
			
		// MAKE SURE WE HAVE CUSTOMER (VENDOR) IDS
			
		
		$sql .=" UNION ALL
			
			SELECT DISTINCT 0 AS opm_productid, '' AS productname, 0 AS propertyid, '' AS property, users.userid, users.username, 0 AS invoiceid, 0 invoicedetailid, 0 AS hasnobodystyle
			FROM opm_invoices
			LEFT JOIN users ON users.userid = opm_invoices.userid
			WHERE opm_invoices.statusid <> '".$this->config->item('invStatusDeleted')."' AND (users.nv_customerid = '' OR users.nv_customerid = '0') ";
			
		if ($strInvoiceIDs)
			$sql .= " AND opm_invoices.id IN (".$strInvoiceIDs.") ";
		
		
		// MAKE SURE WE HAVE CHANNEL CODES
		
			
		$sql .=" UNION ALL
			
			SELECT DISTINCT opm_products.opm_productid, opm_products.productname, 0 AS propertyid, '' AS property, 0 AS userid, 0 AS username, opm_invoices.id AS invoiceid, opm_invoice_detail.id as invoicedetailid, 0 AS hasnobodystyle
			FROM opm_invoice_detail
			LEFT JOIN opm_invoices ON opm_invoices.id = opm_invoice_detail.invoiceid
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
			WHERE opm_invoices.statusid <> '".$this->config->item('invStatusDeleted')."' AND opm_invoice_detail.channelcode = '' ";
			
		if ($strInvoiceIDs)
			$sql .= " AND opm_invoices.id IN (".$strInvoiceIDs.") ";
			
		$sql .= "GROUP BY opm_invoices.id";
		
		
		// MAKE SURE WE HAVE BODY STYLES
		
		/*$sql .=" UNION ALL
			
			SELECT opm_products.opm_productid, opm_products.productname, properties.propertyid, properties.property, 0 AS userid, 0 AS username, 0 AS invoiceid, 0 as invoicedetailid, 1 AS hasnobodystyle
			FROM opm_invoice_detail
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_invoice_detail.opm_productid
			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
			WHERE opm_invoices.statusid <> '".$this->config->item('invStatusDeleted')."' AND opm_products.bodystyleid = 0 ";
			
		if ($strInvoiceIDs)
			$sql .= " AND opm_invoices.id IN (".$strInvoiceIDs.") ";
			
		$sql .= "GROUP BY opm_invoices.id";	*/
		
		$query = $this->db->query($sql);
		
		return $query;
    
    
    }
    
    function copyInvoice($id) {
    
    	$sql = "INSERT INTO opm_invoices (userid, statusid, attentionid, ownerid, companyid, title, total, currencyid, referencenumber, billinginfo, staddress, staddress2, city, state, zip, taxid, invoice_imagepath, checknumber, createdate, submitdate, approvedate, exportdate, paymentdate, createdby)
    			SELECT userid, statusid, attentionid, ownerid, companyid, title, total, currencyid, referencenumber, billinginfo, staddress, staddress2, city, state, zip, taxid, invoice_imagepath, checknumber, createdate, submitdate, approvedate, exportdate, paymentdate, createdby FROM opm_invoices
    			WHERE opm_invoices.id = " . $this->db->escape($id);

    	if ($query = $this->db->query($sql)) {
    	
    		$newId = $this->db->insert_id();
    	
    		// copy line items
    	
    		$sql = "INSERT INTO opm_invoice_detail (invoiceid, opm_productid, chargetypeid, hours, hourlyrate, chargeamount, chargedescription, channelcode, notes)
    				SELECT ".$newId." AS invoiceid, opm_productid, chargetypeid, hours, hourlyrate, chargeamount, chargedescription, channelcode, notes FROM opm_invoice_detail
    				WHERE opm_invoice_detail.invoiceid = " . $this->db->escape($id);
    				
    		$this->db->query($sql);
    		
    		// copy notes
    		
    		$sql = "INSERT INTO opm_invoice_notes (invoiceid, userid, note, timestamp, isdeleted)
    				SELECT ".$newId." AS invoiceid, userid, note, timestamp, isdeleted FROM opm_invoice_notes
    				WHERE opm_invoice_notes.invoiceid = " . $this->db->escape($id);
    				
    		$this->db->query($sql);
    	
    	} else {
    	
    		return false;
    	
    	}

    	return $newId;    	
    
    }
    
    function doesRefExist($referencenumber,$userid) {
	    
	    	$sql = "SELECT id FROM opm_invoices WHERE userid = " . $this->db->escape($userid) . " AND referencenumber = " . $this->db->escape($referencenumber);
	    	$query = $this->db->query($sql);
	    
	    	if ($query->num_rows() > 0)
	    		return true;
	    	else
	    		return false;
	    
    }
    
   
}

?>