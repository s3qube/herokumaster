<?

class Products_model extends CI_Model {

    function ProductsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }

    function fetchProducts($returnTotal = false, $offset = 0, $perPage = null, $propertyid = null, $productlineid = null, $categoryid = null, $usergroupid = null, $approvalstatusid = null, $sampappstatusid = null, $searchtext = null, $productcode = null, $isQuickSearch = false, $designerid = null,  $creatorid = null, $territoryid = null, $filmlocations = null, $filename = null, $orderBy = null, $orderByAscDesc = null) {
    	
		$validOrderbys = array(	"id"=>"opm_products.opm_productid",
								"productname"=>"opm_products.productname",
								"propertyname"=>"properties.property",
								"category"=>"opm_categories.category",
								"appstatus"=>"approvalstatus",
								"sampappstatus"=>"opm_products.sampleappstatusid",
								"lastactivity"=>"opm_products.lastmodified",
								"createdate"=>"opm_products.timestamp",
								"numMasterFiles"=>"numMasterFiles",
								"numSeparations"=>"numSeparations");
    	
    	// first we must query to get the total # of results

    
    	if (!$returnTotal) {
    		
    		$sql = "SELECT opm_products.*,IFNULL(opm_approvalstatuses.approvalstatus,'Pending Approval') AS approvalstatus,
    				IFNULL(appstatuses2.approvalstatus,'Pending Approval') AS sampleappstatus,
    				properties.property,opm_categories.category,
    				GROUP_CONCAT(DISTINCT opm_productlines.productline SEPARATOR ', ') as productlines, 
    				COUNT(DISTINCT opm_masterfiles.fileid) AS numMasterFiles, 
    				COUNT(DISTINCT opm_separations.fileid) AS numSeparations, 
    				opm_accounts.account AS lastpurchase_account, 
    				opm_products_accounts.timestamp AS lastpurchase_timestamp,
    				opm_products_accounts.isexclusive AS lastpurchase_isexclusive, 
    				opm_products_accounts.enddate AS lastpurchase_enddate , 
    				opm_purchase_types.purchasetype AS lastpurchase_purchasetype, 
    				opm_purchase_types.pt_pasttense AS lastpurchase_pt_pasttense,
    				opm_exploit_statuses.exploitstatus, opm_usage_statuses.usagestatus";
    	} else { 
    	
    		$sql = "SELECT COUNT( DISTINCT opm_products.opm_productid ) AS totalProducts ";
		
		}

    			
    	$sql .=	" FROM opm_products ";
    	
    	
    		$sql .= "
    	       	
    	       	LEFT JOIN opm_separations on opm_separations.opm_productid = opm_products.opm_productid AND opm_separations.confirmed = 1 AND opm_separations.archivedate = 0
    	        LEFT JOIN opm_masterfiles ON opm_masterfiles.opm_productid = opm_products.opm_productid AND opm_masterfiles.confirmed = 1 AND opm_masterfiles.archivedate = 0
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			LEFT JOIN opm_categories ON opm_categories.categoryid = opm_products.categoryid
    			LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_products.approvalstatusid
    			LEFT JOIN opm_approvalstatuses AS appstatuses2 ON appstatuses2.approvalstatusid = opm_products.sampleappstatusid
    			LEFT JOIN opm_exploit_statuses ON opm_exploit_statuses.id = opm_products.exploitstatusid
    			LEFT JOIN opm_usage_statuses ON opm_usage_statuses.id = opm_products.usagestatusid
    			LEFT JOIN opm_products_productlines ON opm_products_productlines.opm_productid = opm_products.opm_productid
    		";	
    			
    			
    		if ($usergroupid) {
    			
    			$sql .=" LEFT JOIN opm_products_usergroups ON opm_products_usergroups.opm_productid = opm_products.opm_productid ";
    		
    		}
    		
    		if ($designerid) {
    		
    			$sql .=" LEFT JOIN opm_products_designers ON opm_products_designers.opm_productid = opm_products.opm_productid ";
    			
    		}
    		
    		if ($creatorid) {

    			/*$sql .=" LEFT JOIN opm_usergroup_properties ON opm_usergroup_properties.propertyid = opm_products.propertyid
    					 LEFT JOIN opm_products_licensees ON opm_products_licensees.opm_productid = opm_products.opm_productid ";*/
    			
    		}
    		    		
    		if ($territoryid) {
    		
    			$sql .=" LEFT JOIN opm_products_territories ON opm_products_territories.opm_productid = opm_products.opm_productid
    			LEFT JOIN opm_property_territories ON opm_property_territories.propertyid = properties.propertyid ";
    			
    		}
    		
    			
    		
    		$sql .= "
    			
    			LEFT JOIN opm_productlines ON opm_productlines.productlineid = opm_products_productlines.productlineid
    			LEFT JOIN opm_products_accounts ON opm_products_accounts.id = opm_products.lastpurchaseid
    			LEFT JOIN opm_purchase_types ON opm_purchase_types.id = opm_products_accounts.purchasetypeid
    			LEFT JOIN opm_accounts ON opm_accounts.accountid = opm_products_accounts.account_id
    			
    			";
    	
    	if (USE_PERMISSION_QUERY)
    		$sql .= PERMISSION_QUERY;
    	
    			
    	$sql .= " WHERE opm_products.deletedate = 0 ";
    	
    	if (USE_PERMISSION_QUERY)
    		$sql .= " AND canview.id IS NOT NULL";
    		
    	if ($productlineid)
    		$sql .= " AND opm_products_productlines.productlineid = '".$productlineid."'";
    			
    	if ($propertyid)
    		$sql .= " AND opm_products.propertyid = '".$propertyid."'";
    	
    	if ($designerid)
    		$sql .= " AND opm_products_designers.userid = '".$designerid."'";
    			
    	if ($creatorid)
    		$sql .= " AND opm_products.createdby = '".$creatorid."'";
    		
    	if ($categoryid)
    		$sql .= " AND opm_products.categoryid = '".$categoryid."'";
    		
    	if ($usergroupid) {
    		
    		// get children of this usergroupid!
    		
    		$CI =& get_instance();
			
			$CI->load->model('usergroups_model');
			$ugids = $CI->usergroups_model->getChildren($usergroupid);
			$ugids[] = $usergroupid;
    		$strUGs = implode(",", $ugids); // make it into a comma-delimited list for the sql query!

    		$sql .= " AND opm_products_usergroups.usergroupid IN (".$strUGs.")";

    	}
    	
    	if ($approvalstatusid) {
    		
    		if ($approvalstatusid == 'p')
    			$approvalstatusid = 0;
    		
    		if (!is_numeric($approvalstatusid))
    			$sql .= " AND opm_products.approvalstatusid IN (".addslashes($approvalstatusid).")";
    		else
    			$sql .= " AND opm_products.approvalstatusid = '".addslashes($approvalstatusid)."'";
    	
    	}
    	
    	if (!checkPerms('can_view_expired_products')) {
    	
    		$sql .= " AND opm_products.approvalstatusid <> " . $this->config->item('appStatusExpired');
    	
    	}
    	
    	if ($sampappstatusid) {
    		
    		if ($sampappstatusid == 'p')
    			$sampappstatusid = 0;
    		
    		$sql .= " AND opm_products.sampleappstatusid = '".$sampappstatusid."'";
    	
    	}
    	
    	if ($searchtext) {
    	
    		if (!$isQuickSearch) {
    			
    			$sql .= " AND opm_products.productname LIKE '%".addslashes($searchtext)."%'";
    		
    		} else {
    		
    			// quick search !!
    			
    			// split up search into individual words!
    			
    			$arrSearchWords = explode(" ",$searchtext);
    			
    			$sql .= " AND ( ";
    			
    			foreach ($arrSearchWords as $key => $data) {
    				
    				//if ($data)
    				//echo "word".$key.":" . $data . "<br>";
    				
    				$sql .= " (opm_products.productname LIKE '%".addslashes($data)."%'
    						  OR properties.property LIKE '%".addslashes($data)."%'
    						  OR opm_categories.category LIKE '%".addslashes($data)."%'
    						  OR opm_productlines.productline LIKE '%".addslashes($data)."%') AND";
    				
    			}
    			
    			// remove last AND
    			
    			$sql = substr($sql,0,strlen($sql)-3);
    			
    			$sql .= ")";
    			
    			//die($sql);
    		
    		}
    	
    	}
    	
    	if ($productcode)
    		$sql .= " AND opm_products.productcode LIKE '%".$productcode."%'";
    		
    	if ($filename)
    		$sql .= " AND opm_masterfiles.filename LIKE '%".$filename."%'";	
    	
    	if ($territoryid) {
    	
    		if ($territoryid == 'all') {
    		
    			$sql .= " AND opm_products.availworldwide = 1";
    		
    		} else {
    		
    			$sql .= " AND (opm_products_territories.territoryid = ".$territoryid." OR opm_property_territories.territoryid = ".$territoryid.")";
    		
    		}
    		
    		
    	}
    	
    	if ($filmlocations)
    		$sql .= " AND opm_products.filmlocations LIKE '%".$filmlocations."%'";
    		
    	if (!$returnTotal)	
    		$sql .=" GROUP BY opm_products.opm_productid";
    	
    	
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
    	
    	if (!$returnTotal) {
    	
    		return $query;
    	
    	} else {
    	
    		$row = $query->row();
    		return $row->totalProducts;	
    	
    	}
    		
    
    }
    
    function fetchPendingApprovalProducts($userid,$returnTotal = false, $offset = 0, $perPage = null,$propertyid = 0) {
    	
    	
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT opm_products.*,IFNULL(opm_approvalstatuses.approvalstatus,'Pending Approval') as approvalstatus,properties.property,opm_categories.category,GROUP_CONCAT(opm_productlines.productline SEPARATOR ', ') as productlines, IFNULL(appstatuses2.approvalstatus,'Pending Approval') AS sampleappstatus,";
    	else 
    		$sql = "SELECT opm_products.opm_productid";

    			
    	$sql .=	" FROM opm_products
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			LEFT JOIN opm_user_app_properties ON opm_user_app_properties.propertyid = properties.propertyid AND opm_user_app_properties.userid = ".$this->db->escape($userid)." AND opm_user_app_properties.begindate < '".mktime()."' AND (opm_user_app_properties.enddate = 0 OR opm_user_app_properties.enddate > '".mktime()."')
    			LEFT JOIN opm_categories ON opm_categories.categoryid = opm_products.categoryid
    			LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_products.approvalstatusid
    			LEFT JOIN opm_approvalstatuses AS appstatuses2 ON appstatuses2.approvalstatusid = opm_products.sampleappstatusid
    			LEFT JOIN opm_approvalstatus ON opm_approvalstatus.opm_productid = opm_products.opm_productid AND opm_approvalstatus.userid = ".$this->db->escape($userid)."
    			LEFT JOIN opm_products_productlines ON opm_products_productlines.opm_productid = opm_products.opm_productid
    			LEFT JOIN opm_productlines ON opm_productlines.productlineid = opm_products_productlines.productlineid
    			";
    	
    	if (USE_PERMISSION_QUERY)
    		$sql .= PERMISSION_QUERY;
    	
    			
    	$sql .= " WHERE opm_products.deletedate = 0 AND opm_user_app_properties.lineid IS NOT NULL AND opm_products.approvalstatusid = 0";
    	
    	if (USE_PERMISSION_QUERY)
    		$sql .= " AND canview.id IS NOT NULL";
    			
    	/*if ($propertyid)
    		$sql .= " AND opm_products.propertyid = '".$propertyid."'";
    	
    	if ($categoryid)
    		$sql .= " AND opm_products.categoryid = '".$categoryid."'";
    	
    	if ($approvalstatusid) {
    		
    		if ($approvalstatusid == 'p')
    			$approvalstatusid = 0;
    		
    		$sql .= " AND opm_products.approvalstatusid = '".$approvalstatusid."'";
    	
    	}*/    		
    		
    	$sql .=" GROUP BY opm_products.opm_productid";
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	
    	
    	$query = $this->db->query($sql);
    	
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    
    
    function fetchProductInfo($opm_productid, $liteMode = false, $wholesaleInfo = false) // light mode is used for tooltips, etc. where not so much info is needed!
    {
    
    	// get general info from products, etc tables.
    	
    	$sql = "SELECT opm_products.*,IFNULL(opm_approvalstatuses.approvalstatus,'Pending Approval') AS approvalstatus, IFNULL(appstatuses2.approvalstatus,'Pending Approval') AS sampleappstatus, properties.property,properties.approval_methodid, properties.notificationrecips, opm_categories.category, opm_bodystyles.bodystyle, properties.copyright, properties.nv_propid,
    			GROUP_CONCAT(opm_productlines.productline) AS productline, users.username AS createdbyname, opm_exploit_statuses.exploitstatus, opm_usage_statuses.usagestatus
    			FROM opm_products
    			LEFT JOIN opm_products_productlines ON opm_products_productlines.opm_productid = opm_products.opm_productid
    			LEFT JOIN opm_productlines ON opm_productlines.productlineid = opm_products_productlines.productlineid
    			LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_products.approvalstatusid
    			LEFT JOIN opm_approvalstatuses AS appstatuses2 ON appstatuses2.approvalstatusid = opm_products.sampleappstatusid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			LEFT JOIN opm_categories ON opm_categories.categoryid = opm_products.categoryid
    			LEFT JOIN opm_bodystyles ON opm_bodystyles.id = opm_products.bodystyleid
    			LEFT JOIN users ON users.userid = opm_products.createdby
    			LEFT JOIN opm_exploit_statuses ON opm_exploit_statuses.id = opm_products.exploitstatusid
    			LEFT JOIN opm_usage_statuses ON opm_usage_statuses.id = opm_products.usagestatusid
    			WHERE opm_products.opm_productid = " . $this->db->escape($opm_productid) . " AND opm_products.deletedate = 0";
    			
    		
    			
    			$sql .= " GROUP BY opm_products.opm_productid";
    	
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0)
			$product = $query->row();
		else 
		 	$this->opm->displayError("PRODUCT NOT FOUND");
		 	
		 	
		if (!$liteMode) {
		
			$CI =& get_instance();
		
			// get last product view
			
			if (isset($CI->userinfo->userid)) { // we are logged in
			
				$sql = "SELECT timestamp FROM opm_products_views WHERE opm_productid = " . $this->db->escape($opm_productid). " AND userid = '" . $CI->userinfo->userid . "' ORDER BY timestamp DESC LIMIT 1";
				$query = $this->db->query($sql);
				
				if ($query->num_rows() > 0) {
				
					$row = $query->row();
					$product->lastview = $row->timestamp;
				
				} else {
				
					$product->lastview = 0;
				
				}
			
			} else {
				
				$product->lastview = 0;
				
			}
			
			// get assigned usergroups
			
			$CI =& get_instance();
			$CI->load->model('usergroups_model');
				
			
			$product->usergroups = array();
			
			
			$sql = "SELECT usergroupid FROM opm_products_usergroups WHERE opm_productid = " . $this->db->escape($opm_productid);
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row) {
				
				$product->usergroups[] = $row->usergroupid;
				
				$arrChildren = $CI->usergroups_model->getChildren($row->usergroupid);
				
				foreach ($arrChildren as $childid) {
				
					$product->usergroups[] = $childid;
				
				}
			
			}
			
			// add product licensees to usergroup array
			
			$sql = "SELECT usergroupid FROM opm_products_licensees WHERE opm_productid = " . $this->db->escape($opm_productid);
			
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row) {
				
				$product->usergroups[] = $row->usergroupid;
				
			}
			
			
		
			// get territories
			
			$sql = "SELECT GROUP_CONCAT(opm_territories.territory_abv) as terrList
					FROM opm_territories
					LEFT JOIN opm_products_territories ON opm_products_territories.territoryid = opm_territories.id AND opm_products_territories.opm_productid = ".$this->db->escape($opm_productid)."
					LEFT JOIN opm_property_territories ON opm_property_territories.territoryid = opm_territories.id AND opm_property_territories.propertyid = ".$product->propertyid."
					
					WHERE (propertyid IS NOT NULL AND (isexception <> 1 OR isexception IS NULL)) OR 
					(opm_products_territories.id IS NOT NULL AND (isexception <> 1 OR isexception IS NULL)) ";
			
			$query = $this->db->query($sql);
			$row = $query->row();
			$product->territories = $row->terrList;
			
			// get designers
			
			$arrDesigners = array();
			
			$sql = "SELECT users.userid,users.username
					FROM opm_products_designers
					LEFT JOIN users ON users.userid = opm_products_designers.userid
					WHERE opm_products_designers.opm_productid = ".$this->db->escape($opm_productid)."
					AND users.userid IS NOT NULL";
			
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row)
				$arrDesigners[] = array("userid"=>$row->userid,"username"=>$row->username);
			
			$product->designers = $arrDesigners;
			
			// get separators
			
			$arrSeparators = array();
			
			$query = $CI->usergroups_model->fetchUsergroupsByParent($CI->config->item('separatorsGroupID'),$opm_productid);
			
			foreach ($query->result() as $row)
				$arrSeparators[] = array("usergroupid"=>$row->usergroupid,"usergroup"=>$row->usergroup);
			
			$product->separators = $arrSeparators;
			
			// get screen printers
			
			$arrScreenprinters = array();
			
			$query = $CI->usergroups_model->fetchUsergroupsByParent($CI->config->item('screenprintersGroupID'),$opm_productid);
			
			foreach ($query->result() as $row)
				$arrScreenprinters[] = array("usergroupid"=>$row->usergroupid,"usergroup"=>$row->usergroup);
			
			$product->screenprinters = $arrScreenprinters;
			
			
			// get licensees
			
			$arrLicensees = array();
			
			$sql = "SELECT opm_usergroups.usergroupid,opm_usergroups.usergroup
					FROM opm_products_licensees
					LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = opm_products_licensees.usergroupid
					WHERE opm_products_licensees.opm_productid = ".$this->db->escape($opm_productid)."
					AND opm_usergroups.usergroupid IS NOT NULL";
			
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row)
				$arrLicensees[] = array("usergroupid"=>$row->usergroupid,"usergroup"=>$row->usergroup);
			
			$product->licensees = $arrLicensees;
			
			
			// get property licensees
			
			$arrPropLicensees = array();
			
			$query = $CI->usergroups_model->fetchPropertyLicensees($product->propertyid);
			
			foreach ($query->result() as $row)
				$arrPropLicensees[] = array("usergroupid"=>$row->usergroupid,"usergroup"=>$row->usergroup);
			
			$product->propLicensees = $arrPropLicensees;
			
				
				
			// get imageids
			
			$sql = "SELECT GROUP_CONCAT(opm_images.imageid) as imageids FROM opm_images WHERE opm_images.opm_productid = ".$this->db->escape($opm_productid)." AND opm_images.deletedate = 0 GROUP BY opm_images.opm_productid";
			$query = $this->db->query($sql);
			
			if(isset($query->row()->imageids)) 
				$product->imageids = $query->row()->imageids;
			else
				$product->imageids = "";
			
			if ($product->imageids)
				$product->imageids = explode(",",$product->imageids); // convert to array 
			else
				$product->imageids = array();
			
			// get approval status info
			
			$sql = "SELECT users.userid,users.username,users.login,IFNULL(opm_approvalstatuses.approvalstatus,'Pending Approval') as approvalstatus,IFNULL(opm_approvalstatus.approvalstatusid,0) as approvalstatusid, opm_user_app_properties.approvalrequired
			FROM users
			LEFT JOIN opm_user_app_properties ON opm_user_app_properties.userid = users.userid
			LEFT JOIN opm_approvalstatus ON opm_approvalstatus.userid = users.userid AND opm_approvalstatus.opm_productid = ".$this->db->escape($opm_productid)."
			LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_approvalstatus.approvalstatusid
			WHERE opm_user_app_properties.propertyid = '".$product->propertyid."'
			AND opm_user_app_properties.begindate < '".$product->timestamp."'
			AND (opm_user_app_properties.enddate = 0 OR opm_user_app_properties.enddate > '".$product->timestamp."')
			ORDER BY users.username";
			
			$query2 = $this->db->query($sql);
			$product->approvalInfo = $query2->result();
			
			
			// get "my approval status", first check if user is approval contact!
			
			if ($product->approvalInfo) {
			
				$appRequired = false;
			
				foreach ($product->approvalInfo as $ai) {
					$arrAppUsers[] = $ai->userid;
					
					// is user's approval requird?
					if (isset($CI->userinfo->userid)) {
						
						if ($ai->userid == $CI->userinfo->userid && $ai->approvalrequired == '1')
							$appRequired = true;
					}
					
				}
				
				
				if (isset($CI->userinfo->userid) && in_array($CI->userinfo->userid, $arrAppUsers)) {
						
					if ($appRequired) {
				
						$sql = "SELECT opm_approvalstatus.approvalstatusid, opm_approvalstatuses.approvalstatus
								FROM opm_approvalstatus
								LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_approvalstatus.approvalstatusid
								WHERE opm_approvalstatus.opm_productid = '".$product->opm_productid."'
								AND userid = '".$CI->userinfo->userid."'";
						
						$query = $this->db->query($sql);
						
						$product->myStatus = new stdClass();
						
						if ($row = $query->row()) {
							$product->myStatus = $row;
						} else {
							$product->myStatus->approvalstatusid = "";
							$product->myStatus->approvalstatus = "Pending Approval";
						}
					
					}
				
				}
			
			}
			
			// get account info
			
		   // get account info
			
		   $arrPurchases = array();

		   // 20120102 mark
		   $sql = "
		   		SELECT 
		   			opm_products_accounts.id, 
		   			opm_accounts.account, 
		   			users.userid, 
		   			users.username, 
		   			opm_purchase_types.purchasetype,
		   			opm_purchase_types.pt_pasttense,
		   			opm_products_accounts.enddate, 
		   			opm_products_accounts.timestamp,
		   			opm_products_accounts.isexclusive
				FROM 
					opm_purchase_types,
					opm_products_accounts
					LEFT JOIN opm_accounts ON opm_accounts.accountid = opm_products_accounts.account_id
					LEFT JOIN users ON users.userid = opm_products_accounts.user_id
				WHERE 
					opm_products_accounts.opm_productid = " . $this->db->escape($opm_productid) . "
					and opm_products_accounts.purchasetypeid = opm_purchase_types.id
				ORDER BY 
					opm_products_accounts.timestamp DESC";
				
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row)
				$arrPurchases[] = array("id"=>$row->id,"account"=>$row->account,"userid"=>$row->userid,"username"=>$row->username,"purchasetype"=>$row->purchasetype,"pt_pasttense"=>$row->pt_pasttense,"enddate"=>$row->enddate,"timestamp"=>$row->timestamp,"isexclusive"=>$row->isexclusive);
			
			$product->purchases = $arrPurchases;
			
			// get colors
			
		   $arrColors = array();
			
		   $sql = "SELECT opm_colors.id, opm_colors.color
					FROM opm_products_colors
					LEFT JOIN opm_colors ON opm_colors.id = opm_products_colors.colorid
					WHERE opm_products_colors.opm_productid = ".$this->db->escape($opm_productid);
				
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row)
				$arrColors[] = array("id"=>$row->id,"color"=>$row->color);
			
			$product->colors = $arrColors;	
			
			// get sizes
			
		   $arrSizes = array();
			
		   $sql = "SELECT opm_sizes.id, opm_sizes.size
					FROM opm_products_sizes
					LEFT JOIN opm_sizes ON opm_sizes.id = opm_products_sizes.sizeid
					WHERE opm_products_sizes.opm_productid = " . $this->db->escape($opm_productid);
				
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row)
				$arrSizes[] = array("id"=>$row->id,"size"=>$row->size);
			
			$product->sizes = $arrSizes;	
			
			// get skus
			
		   $arrSkus = array();
			
		   $sql = "	SELECT opm_skus.*,opm_colors.color,opm_sizes.sizecode				
		   			FROM opm_skus
		   			LEFT JOIN opm_colors ON opm_colors.id = opm_skus.colorid
		   			LEFT JOIN opm_sizes ON opm_sizes.id = opm_skus.sizeid
					WHERE opm_skus.opm_productid = ".$this->db->escape($opm_productid)."
					ORDER BY opm_skus.colorid,opm_skus.sizeid";
				
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row)
				$arrSkus[] = (object)array("id"=>$row->id,
									"color"=>$row->color,
									"sizecode"=>$row->sizecode,
									"sku"=>$row->sku);
			
			$product->skus = (object)$arrSkus;		
			
			// get latest forum entry
			
		   $sql = "SELECT opm_forum.*
					FROM opm_forum
					WHERE opm_forum.opm_productid = ".$this->db->escape($opm_productid)."
					ORDER BY timestamp DESC
					LIMIT 1";
				
			$query = $this->db->query($sql);
			$product->latestForum = $query->row();
			
			// get latest history entry
			
		   $sql = "SELECT opm_history.*
					FROM opm_history
					WHERE opm_history.opm_productid = ".$this->db->escape($opm_productid)."
					ORDER BY timestamp DESC
					LIMIT 1";
				
			$query = $this->db->query($sql);
			$product->latestHistory = $query->row();
			
			
			// determine if we show "new history since last view and new comments since last view"!
			
			if ($wholesaleInfo) {
				
				
				
				$sql = "SELECT * FROM opm_ws_products WHERE opm_ws_products.opm_productid = " . $this->db->escape($opm_productid);
				$query = $this->db->query($sql);
				
				if ($product->wholesaleInfo = $query->row()) {
				
					
				
				} else {
					
					
					$product->wholesaleInfo = new stdClass();
					$product->wholesaleInfo->sitebrandid = 0;
					$product->wholesaleInfo->isactive = 0;
					$product->wholesaleInfo->isfeatured = 0;
					$product->wholesaleInfo->baseprice = null;
					
				}
				
				
				$arrWsSizes = array();
			
			   $sql = "SELECT opm_ws_product_sizes.*,opm_sizes.size
						FROM opm_ws_product_sizes
						LEFT JOIN opm_sizes ON opm_sizes.id = opm_ws_product_sizes.sizeid
						WHERE opm_ws_product_sizes.opm_productid = " . $this->db->escape($opm_productid);
					
				$query = $this->db->query($sql);
				
				foreach ($query->result() as $row)
					$arrWsSizes[] = array("id"=>$row->id,"size"=>$row->size,"sku"=>$row->sku,"isactive"=>$row->isactive);
				
				$product->wholesaleInfo->sizes = $arrWsSizes;
				
			}
			
			// get linked products
			
			$arrLinks = array();
			//$arrRevLinks = array();
			
			$sql = "SELECT opm_product_links.*, opm_products.productname, properties.property, opm_products2.productname as productname2, properties2.property AS property2
					FROM opm_product_links
					LEFT JOIN opm_products ON opm_products.opm_productid = opm_product_links.linked_id
					LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
					LEFT JOIN opm_products opm_products2 ON opm_products2.opm_productid = opm_product_links.opm_productid
					LEFT JOIN properties properties2 ON properties2.propertyid = opm_products2.propertyid
					WHERE (opm_product_links.opm_productid = ".$this->db->escape($opm_productid) ." OR opm_product_links.linked_id = ".$this->db->escape($opm_productid) .")";
				
			$query = $this->db->query($sql);
			
			foreach ($query->result() as $row) {
			
				if ($row->opm_productid == $opm_productid)
					$arrLinks[] = array("id"=>$row->id,"linked_id"=>$row->linked_id,"productname"=>$row->property . " - " . $row->productname);
				else
					$arrLinks[] = array("id"=>$row->id,"linked_id"=>$row->opm_productid,"productname"=>$row->property2 . " - " . $row->productname2);

			
			}
			
			
			$product->links = $arrLinks;
			//$product->revlinks = $arrRevLinks;
			
        }
  
		return $product;
		 	
        
    }
    
    function fetchProductInfoByImageID($imageid) {
    	
    	// get general info from products, etc tables.
    	
    	$sql = "SELECT opm_products.*,IFNULL(opm_approvalstatuses.approvalstatus,'Pending Approval') as approvalstatus,properties.property,properties.approval_methodid,opm_categories.category, properties.copyright
    			FROM opm_images
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_images.opm_productid    			
    			LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_products.approvalstatusid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			LEFT JOIN opm_categories ON opm_categories.categoryid = opm_products.categoryid
    			WHERE opm_images.imageid = '" . $imageid ."'
    			AND opm_products.deletedate = 0
    			GROUP BY opm_products.opm_productid";
    	
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0)
			$product = $query->row();
		else 
		 	$this->opm->displayError("product_not_found");
    
    	return $product;
    
    }
    
    function saveProduct($arrPostData) {
    
    	/*echo "<pre>";
    	print_r($arrPostData);
    	echo "</pre>";
    	die();*/

		$errors = false;
     	
     	if ($arrPostData['opm_productid'] && checkPerms('can_edit_products',true)) { // we have a productid, update
     	
     		$sql = "UPDATE opm_products
     				SET propertyid = ". $this->db->escape($arrPostData['propertyid']).",
     				productname = ".$this->db->escape($arrPostData['productname']).",
     				shortname = ".$this->db->escape($arrPostData['shortname']).",
     				designcode = ".$this->db->escape($arrPostData['designcode']).",
     				numprints = ".$this->db->escape($arrPostData['numprints']).",
     				productcode = ".$this->db->escape($arrPostData['productcode']).",
     				licenseecode = ".$this->db->escape($arrPostData['licenseecode']).",
     				productdesc = ".$this->db->escape($arrPostData['productdesc']).",
     				categoryid = ".$this->db->escape($arrPostData['categoryid']).",
     				bodystyleid = ".$this->db->escape($arrPostData['bodystyleid']).",
     				copyrightaddendums = ".$this->db->escape($arrPostData['copyrightaddendums']).",
     				filmnumber = ".$this->db->escape($arrPostData['filmnumber']).",
     				artworkcharges = ".$this->db->escape($arrPostData['artworkcharges']).",
     				presentationstyles = ".$this->db->escape($arrPostData['presentationstyles']).",
     				duedate = ".$this->db->escape($arrPostData['duedate']).",
     				filmlocations = ".$this->db->escape($arrPostData['filmlocations'])." ";
     				
     				
     		$sql .= " WHERE opm_productid = '".$arrPostData['opm_productid']."'";

    		if ($query = $this->db->query($sql))
       			$opm_productid = $arrPostData['opm_productid'];
       		else
       			$errors = true;
    
     	
     	} else { // new product, insert
     	
     		if (checkPerms('can_add_products',true)) {
     		
     			//if (checkPerms('can_bypass_submission_process'))
     				$approvalStatusID = 0;
     			//else
     			//	$approvalStatusID = $this->config->item('appStatusSubmitted');
     				
     				
     			     		
     			// get largest design code # for this property and increment by 1
     			
     			
     			if ($arrPostData['designcode'] == '') {
     			
     				$sql = "SELECT MAX(designcode) AS maxDesignCode FROM opm_products WHERE propertyid = " . $this->db->escape($arrPostData['propertyid']);
     				$query = $this->db->query($sql);
     				$row = $query->row();
     				$arrPostData['designcode'] = $row->maxDesignCode + 1;
     			
     			}
     			
     			// insert the product.
     		     		
				$sql = "INSERT INTO opm_products (propertyid,productname,shortname,numprints,designcode,productcode,licenseecode,productdesc,categoryid,bodystyleid,filmnumber,copyrightaddendums,artworkcharges,presentationstyles,filmlocations,duedate,designcode_islocked,approvalstatusid,createdby,timestamp)
					VALUES (". $this->db->escape($arrPostData['propertyid']).",".$this->db->escape($arrPostData['productname']).",".$this->db->escape($arrPostData['shortname']).",".$this->db->escape($arrPostData['numprints']).",".$this->db->escape($arrPostData['designcode']).",".$this->db->escape($arrPostData['productcode']).",".$this->db->escape($arrPostData['licenseecode']).",".$this->db->escape($arrPostData['productdesc']).",".$this->db->escape($arrPostData['categoryid']).",".$this->db->escape($arrPostData['bodystyleid']).",".$this->db->escape($arrPostData['filmnumber']).",".$this->db->escape($arrPostData['copyrightaddendums']).",".$this->db->escape($arrPostData['artworkcharges']).",".$this->db->escape($arrPostData['presentationstyles']).",".$this->db->escape($arrPostData['filmlocations']).",".$this->db->escape($arrPostData['duedate']).",1,".$this->db->escape($approvalStatusID).",".$this->userinfo->userid.",'".mktime()."')";
			
				
				if ($query = $this->db->query($sql)) {
				
					$opm_productid = $this->db->insert_id();
					$this->opm->addHistoryItem($opm_productid,"Product created by " . $this->userinfo->username); 
					
					// add default usergroups to product
					
					if ($approvalStatusID == 0) { // This product is "pending" and can be viewd by property contacts, etc...
			
						$sql = "INSERT INTO opm_products_usergroups (opm_productid,usergroupid)
								VALUES($opm_productid,".$this->config->item('bravadoInternalGroupID')."),
								($opm_productid,".$this->config->item('propertyContactsGroupID')."),
								($opm_productid,".$this->config->item('internationalUmgGroupID')."),
								($opm_productid,".$this->config->item('externalViewingGroupID')."),
								($opm_productid,".$this->config->item('designersGroupID').")
								";
						
						$query = $this->db->query($sql);
					
					} else { 
					
						$sql = "INSERT INTO opm_products_usergroups (opm_productid,usergroupid)
								VALUES($opm_productid,".$this->config->item('bravadoInternalGroupID')."),
								($opm_productid,".$this->config->item('licenseeGroupID').")
								";
						
						$query = $this->db->query($sql);
						
					
					}
					
					// increment latest_designcode for property.
					
					$sql = "UPDATE properties SET latest_designcode = " . $this->db->escape($arrPostData['designcode']) . " WHERE propertyid = " . $this->db->escape($arrPostData['propertyid']);
					$query = $this->db->query($sql);
					
					
					
					// if this product was created by a licensee, add that licensee to opm_products_licensees.
     			
	     			$CI =& get_instance();
				
					$CI->load->model('usergroups_model');
					$ugids = $CI->usergroups_model->getChildren($CI->config->item('licenseeGroupID'));
					$ugids[] = $CI->config->item('licenseeGroupID');
						     			
	     			if (in_array($this->userinfo->usergroupid, $ugids)) {
	     				
	     				$ugidToAdd = $this->userinfo->usergroupid;
	     			
	     			} elseif (in_array($this->userinfo->usergroupid2, $ugids)) {
	     				
	     				$ugidToAdd = $this->userinfo->usergroupid2;
	     			
	     			} else { // person adding is not a licensee, make product visible to licensees.
		     			
		     			$sql = "INSERT INTO opm_products_usergroups (opm_productid,usergroupid)
								VALUES ($opm_productid,".$this->config->item('licenseeGroupID').")
								";
						
						$query = $this->db->query($sql);
		     			
	     			}
	     		
	     			if (isset($ugidToAdd) && ($ugidToAdd)) { // add ugid to opm_products_licensees for this product
	     			
	     			
	     				$sql = "INSERT INTO opm_products_licensees (opm_productid, usergroupid)
	     						VALUES (".$opm_productid.",".$ugidToAdd.")";
	     			
	     				$this->db->query($sql);
	     			}

					
					
				} else {
				
					$errors = true;
				
				}
			}
     	
     	}
     	
     	if ($errors) {
     	
     		return false;
     	
     	} else { 
     	
     		// record product line info
     	
     		if (checkPerms('prodEdit_can_choose_product_line')) {
     	
	     		$sql = "DELETE FROM opm_products_productlines WHERE opm_productid = '".$opm_productid."'";
	     		$query = $this->db->query($sql);
	     		
	     		if ($arrPostData['productLineIDs']) { // some productlineids have been assigned.
	     			
	     			foreach ($arrPostData['productLineIDs'] as $plid) {
	     			
	     				$sql = "INSERT INTO opm_products_productlines (opm_productid,productlineid) VALUES ('".$opm_productid."','".$plid."')";
	     				$query = $this->db->query($sql);
	     				
	     			}
	     		
	     		}
     		
     		}
     		
     		// record designer info
     	
     		if (checkPerms('prodEdit_can_choose_designer')) {
     	
	     		$sql = "DELETE FROM opm_products_designers WHERE opm_productid = '".$opm_productid."'";
	     		$query = $this->db->query($sql);
	     		
	     		if ($arrPostData['designerIDs']) { // some designers have been assigned.
	     			
	     			foreach ($arrPostData['designerIDs'] as $userid) {
	     			
	     				$sql = "INSERT INTO opm_products_designers (opm_productid,userid) VALUES ('".$opm_productid."','".$userid."')";
	     				$query = $this->db->query($sql);
	     				
	     			}
	     		
	     		}
     		
     		}
     		
     		// record licensee info
     	
     		if (checkPerms('prodEdit_can_choose_licensees')) {
     	
	     		$sql = "DELETE FROM opm_products_licensees WHERE opm_productid = '".$opm_productid."'";
	     		$query = $this->db->query($sql);
	     		
	     		if ($arrPostData['licenseeIDs']) { // some designers have been assigned.
	     			
	     			foreach ($arrPostData['licenseeIDs'] as $usergroupid) {
	     			
	     				$sql = "INSERT INTO opm_products_licensees (opm_productid,usergroupid) VALUES ('".$opm_productid."','".$usergroupid."')";
	     				$query = $this->db->query($sql);
	     				
	     			}
	     		
	     		}
     		
     		}
     		
     		return $opm_productid;
     	
     	}
    
    }
    
    /*function getNextDesignCode($propertyid) {
    
    	$sql = "SELECT MAX(designcode) AS maxDesignCode FROM opm_products WHERE propertyid = " . $this->db->escape($propertyid);
		$query = $this->db->query($sql);
		$row = $query->row();
		
		if ($row->maxDesignCode == 0) {
		
			$this->opm->displayError("product_not_found");
		
		} else {
		
			$arrPostData['designcode'] = $row->maxDesignCode + 1;
		
		}
		
    
    }*/
    

    
    function deleteProduct($opm_productid)
    {
    
    	
    	$sql = "UPDATE opm_products SET deletedate = '".mktime()."' WHERE opm_productid = " . $this->db->escape($opm_productid);
    	
    	$this->db->query($sql);  	
    	return true;
    	
        
    }
    
    // image functions
    
    function fetchImage($imageid)
    {
    
    	// get general info from products, etc tables.
    	
    	$sql = "SELECT * FROM opm_images WHERE imageid = '" . $imageid ."'";
        $query = $this->db->query($sql);
       
       
       	 if ($query->num_rows() > 0) {
		   return $query->row();
		 } else {
		 
		 	return false;
		
		 }
		 	
        
    }
    
    function setLastModified($opm_productid)
    {

    	$sql = "UPDATE opm_products SET lastmodified = '".mktime()."' WHERE opm_productid = " . $this->db->escape($opm_productid);
        $query = $this->db->query($sql);
		return true;
        
    }
    
    function getProductsFromProperty($propertyid)
    {

    	$sql = "SELECT opm_productid FROM opm_products WHERE opm_products.deletedate = 0 AND propertyid = " . $this->db->escape($propertyid);
      
        $query = $this->db->query($sql);
		return $query;
        
    }
    
    function getTotalSystemProducts()
    {

    	$sql = "SELECT COUNT(opm_productid) as totalProducts FROM opm_products";
      
        $query = $this->db->query($sql);
        $row = $query->row();
        
		return $row->totalProducts;
        
    }
    
    function setViewTimestamp($opm_productid)
    {

    	$sql = "INSERT INTO opm_products_views (opm_productid,userid,timestamp)
    			VALUES (".$this->db->escape($opm_productid).",".$this->userinfo->userid.",".mktime().")";
        $query = $this->db->query($sql);
		return true;
        
    }
    
    function setSampAppStatus($opm_productid,$appstatusid) {
    	
    	$sql = "UPDATE opm_products
    			SET sampleappstatusid = ".$this->db->escape($appstatusid)."
    			WHERE opm_productid = " . $this->db->escape($opm_productid);
        
        if ($query = $this->db->query($sql))
			return true;
		else
			return false;
    
    }
    
    function updateAvailableTerritories($opm_productid) {
    	
    	
    	$sql = "SELECT * FROM opm_territories ORDER BY id";
    	$result = $this->db->query($sql);
    
    	foreach ($result->result() as $t) 
    		$tmpArray[] = $t->id;
    	
    	$strWW = implode(",",$tmpArray);
    	
    	//echo "AVAIL WW = " . $strWW;
    	//die();	
    		
    	
    	$sql = "SELECT DISTINCT * FROM opm_prodterritory_view WHERE opm_productid = ".$opm_productid." ORDER BY opm_productid,territoryid";

    	$result = $this->db->query($sql);
    	    	
    	$lastID = 0;
    	$arrIDs = array();
    	
    	foreach ($result->result() as $p) {
    	
    		if ($p->opm_productid != $lastID) {
    			
    			$strIDs = implode(",",$arrIDs);
    			
    			if ($strIDs == $strWW)
    				$availWorldwide = 1;
    			else
    				$availWorldwide = 0;
    			
    			$sql = "UPDATE opm_products SET availableinterr = " . $this->db->escape($strIDs) . ", availworldwide = " . $availWorldwide . " WHERE opm_productid = " . $this->db->escape($lastID);
    			$this->db->query($sql);
    			
    			unset($arrIDs);
    		}
    		
    		$arrIDs[] = $p->territoryid;
    		$lastID = $p->opm_productid;
    		
    	}
    	
    	
    	// catch the last product, i should rewrite this
    	
    	$strIDs = implode(",",$arrIDs);
    			
    	if ($strIDs == $strWW)
    		$availWorldwide = 1;
    	else
    		$availWorldwide = 0;
    			
    	$sql = "UPDATE opm_products SET availableinterr = " . $this->db->escape($strIDs) . ", availworldwide = " . $availWorldwide . " WHERE opm_productid = " . $this->db->escape($lastID);
    	$this->db->query($sql);
    	
    	return true;
    	
    	
    	
    	
    }
    
     function updateAvailableRights($opm_productid) {
    	
    	
    	$sql = "SELECT * FROM opm_rights ORDER BY id";
    	$result = $this->db->query($sql);
    
    	foreach ($result->result() as $t) 
    		$tmpArray[] = $t->id;
    	
    	$strWW = implode(",",$tmpArray);
    	
    	//echo "AVAIL WW = " . $strWW;
    	//die();	
    		
    	
    	$sql = "SELECT DISTINCT * FROM opm_prodrights_view WHERE opm_productid = ".$opm_productid." ORDER BY opm_productid,rightid";

    	$result = $this->db->query($sql);
    	    	
    	$lastID = 0;
    	$arrIDs = array();
    	
    	foreach ($result->result() as $p) {
    	
    		if ($p->opm_productid != $lastID) {
    			
    			$strIDs = implode(",",$arrIDs);
    			
    			if ($strIDs == $strWW)
    				$hasallrights = 1;
    			else
    				$hasallrights = 0;
    			
    			$sql = "UPDATE opm_products SET rights = " . $this->db->escape($strIDs) . ", hasallrights = " . $hasallrights . " WHERE opm_productid = " . $this->db->escape($lastID);
    			$this->db->query($sql);
    			
    			unset($arrIDs);
    		}
    		
    		$arrIDs[] = $p->rightid;
    		$lastID = $p->opm_productid;
    		
    	}
    	
    	
    	// catch the last product, i should rewrite this
    	
    	$strIDs = implode(",",$arrIDs);
    			
    	if ($strIDs == $strWW)
    		$hasallrights = 1;
    	else
    		$hasallrights = 0;
    			
    	$sql = "UPDATE opm_products SET rights = " . $this->db->escape($strIDs) . ", hasallrights = " . $hasallrights . " WHERE opm_productid = " . $this->db->escape($lastID);
    	$this->db->query($sql);
    	
    	return true;
    	
    	
    	
    	
    }
    
    function updateAvailableTerritoriesProp($propertyid) {
    
    	
    
    	$sql = "SELECT * FROM opm_territories ORDER BY id";
    	$result = $this->db->query($sql);
    
    	foreach ($result->result() as $t) 
    		$tmpArray[] = $t->id;
    	
    	$strWW = implode(",",$tmpArray);
    	
    	//echo "AVAIL WW = " . $strWW;
    	//die();	
    		
    	
    	$sql = "SELECT * FROM opm_prodterritory_view WHERE propertyid = ".$propertyid." ORDER BY opm_productid,territoryid";
    	$result = $this->db->query($sql);
    	
    	$lastID = 0;
    	$arrIDs = array();
    	
    	foreach ($result->result() as $p) {
    	
    		if ($p->opm_productid != $lastID) {
    			
    			$strIDs = implode(",",$arrIDs);
    			
    			if ($strIDs == $strWW)
    				$availWorldwide = 1;
    			else
    				$availWorldwide = 0;
    			
    			$sql = "UPDATE opm_products SET availableinterr = " . $this->db->escape($strIDs) . ", availworldwide = " . $availWorldwide . " WHERE opm_productid = " . $this->db->escape($lastID);
    			$this->db->query($sql);
    			
    			unset($arrIDs);
    		}
    		
    		$arrIDs[] = $p->territoryid;	
    		$lastID = $p->opm_productid;
    		
    	}
    	
    	// catch the last product, i should rewrite this
    	
    	$strIDs = implode(",",$arrIDs);
    			
    	if ($strIDs == $strWW)
    		$availWorldwide = 1;
    	else
    		$availWorldwide = 0;
    			
    	$sql = "UPDATE opm_products SET availableinterr = " . $this->db->escape($strIDs) . ", availworldwide = " . $availWorldwide . " WHERE opm_productid = " . $this->db->escape($lastID);
    	$this->db->query($sql);
    	
    	return true;
    
    }
    
    function updateAvailableRightsProp($propertyid) {
    
    	$sql = "SELECT * FROM opm_rights ORDER BY id";
    	$result = $this->db->query($sql);
    
    	foreach ($result->result() as $t) 
    		$tmpArray[] = $t->id;
    	
    	$strWW = implode(",",$tmpArray);
    	
    	//echo "AVAIL WW = " . $strWW;
    	//die();	
    		
    	
    	$sql = "SELECT * FROM opm_prodrights_view WHERE propertyid = ".$propertyid." ORDER BY opm_productid,rightid";
    	$result = $this->db->query($sql);
    	
    	$lastID = 0;
    	$arrIDs = array();
    	
    	foreach ($result->result() as $p) {
    	
    		if ($p->opm_productid != $lastID) {
    			
    			$strIDs = implode(",",$arrIDs);
    			
    			if ($strIDs == $strWW)
    				$hasallrights = 1;
    			else
    				$hasallrights = 0;
    			
    			$sql = "UPDATE opm_products SET rights = " . $this->db->escape($strIDs) . ", hasallrights = " . $hasallrights . " WHERE opm_productid = " . $this->db->escape($lastID);
    			$this->db->query($sql);
    			
    			unset($arrIDs);
    		}
    		
    		$arrIDs[] = $p->rightid;	
    		$lastID = $p->opm_productid;
    		
    	}
    	
    	// catch the last product, i should rewrite this
    	
    	$strIDs = implode(",",$arrIDs);
    			
    	if ($strIDs == $strWW)
    		$hasallrights = 1;
    	else
    		$hasallrights = 0;
    			
    	$sql = "UPDATE opm_products SET rights = " . $this->db->escape($strIDs) . ", hasallrights = " . $hasallrights . " WHERE opm_productid = " . $this->db->escape($lastID);
    	$this->db->query($sql);
    	
    	return true;
    
    }
    
    function updateAvailableChannelsProp($propertyid) {
    
    	$sql = "SELECT * FROM opm_channels ORDER BY id";
    	$result = $this->db->query($sql);
    
    	foreach ($result->result() as $t) 
    		$tmpArray[] = $t->id;
    	
    	$strWW = implode(",",$tmpArray);
    	
    	//echo "AVAIL WW = " . $strWW;
    	//die();	
    		
    	
    	$sql = "SELECT * FROM opm_prodchannels_view WHERE propertyid = ".$propertyid." ORDER BY opm_productid,channelid";
    	$result = $this->db->query($sql);
    	
    	$lastID = 0;
    	$arrIDs = array();
    	
    	foreach ($result->result() as $p) {
    	
    		if ($p->opm_productid != $lastID) {
    			
    			$strIDs = implode(",",$arrIDs);
    			
    			if ($strIDs == $strWW)
    				$hasallchannels = 1;
    			else
    				$hasallchannels = 0;
    			
    			$sql = "UPDATE opm_products SET channels = " . $this->db->escape($strIDs) . ", hasallchannels = " . $hasallchannels . " WHERE opm_productid = " . $this->db->escape($lastID);
    			$this->db->query($sql);
    			
    			unset($arrIDs);
    		}
    		
    		$arrIDs[] = $p->channelid;	
    		$lastID = $p->opm_productid;
    		
    	}
    	
    	// catch the last product, i should rewrite this
    	
    	$strIDs = implode(",",$arrIDs);
    			
    	if ($strIDs == $strWW)
    		$hasallchannels = 1;
    	else
    		$hasallchannels = 0;
    			
    	$sql = "UPDATE opm_products SET channels = " . $this->db->escape($strIDs) . ", hasallchannels = " . $hasallchannels . " WHERE opm_productid = " . $this->db->escape($lastID);
    	$this->db->query($sql);
    	
    	return true;
    
    }
    
    function quickFetchProducts($query) { // used by invoice ajax query...
    
    	// split words up into individual queries
    	
    	$arrWords = split(" ", $query);
    
    	$sql = "SELECT opm_products.opm_productid, opm_products.productname, properties.property 
    			FROM opm_products
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid";
    	
    	if (USE_PERMISSION_QUERY)
    		$sql .= PERMISSION_QUERY;		
    			
    	$sql .= " WHERE opm_products.deletedate = 0 AND ( ";
    	
    	foreach ($arrWords as $w) {
    	
    		$sql .= " (opm_products.productname LIKE '%".addslashes($w)."%' OR properties.property LIKE '%".addslashes($w)."%' OR opm_products.opm_productid = '".addslashes($w)."') AND";
    	
    	}		
    	
    	// remove last "OR"
    	
    	$sql = substr($sql, 0, strlen($sql)-3);
    			 
    			
    	$sql .= ") LIMIT 10";
    	
    	//die($sql);
    	
    	$query = $this->db->query($sql);
    	return $query;
    
    
    }
    
    function hideAllProducts($propertyid) { // hide all products of a given property from all but internal users.
    
    	// get internal id and all children
    	
    	$CI =& get_instance();
			
		$CI->load->model('usergroups_model');
		$ugids = $CI->usergroups_model->getChildren($CI->config->item('bravadoInternalGroupID'));
		$ugids[] = $CI->config->item('bravadoInternalGroupID');
		$strUGs = implode(",", $ugids); // make it into a comma-delimited list for the sql query!

		// first get a list of all products we will be hiding, so we can add to history...
		
		$sql = "SELECT DISTINCT opm_products_usergroups.opm_productid 
				FROM opm_products_usergroups
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_usergroups.opm_productid
				WHERE opm_products.propertyid = " . $this->db->escape($propertyid) . "
				AND opm_products_usergroups.usergroupid NOT IN (".$strUGs.")";
				
		$query = $this->db->query($sql);
		
		foreach ($query->result() as $p)
			$prodIDs[] = $p->opm_productid;
			
		$sql = "DELETE opm_products_usergroups
				FROM opm_products_usergroups
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_usergroups.opm_productid
				WHERE opm_products.propertyid = " . $this->db->escape($propertyid) . "
				AND opm_products_usergroups.usergroupid NOT IN (".$strUGs.")";
		
		if ($this->db->query($sql)) {
		
			foreach ($prodIDs as $p) 
				$CI->opm->addHistoryItem($p,"Product Hidden To All External Users"); 
			
		}
		
		return true;
		
    
    }
    
    function deleteSkus($opm_productid) {
    
    	$sql = "DELETE FROM opm_skus WHERE opm_productid = " . $this->db->escape($opm_productid);
    	$this->db->query($sql);
    
    }
    
    function addSku($opm_productid,$colorid,$sizeid,$sku) {
    	
    	// check if sku already exists.
    	
    	$sql = "SELECT id FROM opm_skus WHERE sku = " . $this->db->escape($sku);
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() > 0)
    		return "duplicate";
    
    	//  ensure no duplicate skus exist.
    	
    	$sql = "INSERT INTO opm_skus (opm_productid,colorid,sizeid,sku)
    			VALUES (".$this->db->escape($opm_productid).",".$this->db->escape($colorid).",".$this->db->escape($sizeid).",".$this->db->escape($sku).")";
    	
    	if ($this->db->query($sql))
    		return "inserted";
    	else
    		return "queryfailed";
    	
    
    }
    
    function checkForDuplicateProducts($productName,$propertyId) {
	    
	    $sql = "SELECT * FROM opm_products WHERE productname LIKE '%" . addslashes($productName) ."%' and propertyid = " . $this->db->escape($propertyId);
       
        $query = $this->db->query($sql);
       
		if ($query->num_rows() > 0) {
		
			return $query->row();
		
		} else {
		 
			return false;
		
		}
	
	}
	
	function findProductMatches($propertyID,$arrSearchWords) {
	
		$sql = "SELECT opm_products.opm_productid,opm_products.productname,opm_products.default_imageid,opm_categories.category , ";
		
		$sql .=" ( ";
		
		
		foreach ($arrSearchWords as $word) {
		
			$sql .= "( CASE WHEN productname LIKE '%".addslashes($word)."%' THEN 1 ELSE 0 END ) + ";
		
		}
		
		// eliminate last "+ "
		$sql = substr($sql, 0, strlen($sql)-2);
		
					
		$sql .=" ) AS relevance";
		
		
		$sql .=" FROM opm_products 
				LEFT JOIN opm_categories ON opm_categories.categoryid = opm_products.categoryid
				WHERE propertyid = ".$this->db->escape($propertyID)."
				AND ( ";
				
		foreach ($arrSearchWords as $word) {
		
			$sql .= "productname LIKE '%".addslashes($word)."%' OR ";
		
		}
		
		// eliminate last "OR "
		$sql = substr($sql, 0, strlen($sql)-3);
				
				
		$sql .= " ) 
		
			ORDER BY relevance
		
		";
		
		//echo $sql . "<br><br><br>";
				
		$query = $this->db->query($sql);

		// put results in array and return it
		
		$arrResults = array();
		
		foreach ($query->result() as $row)
			$arrResults[] = $row;
			
		return $arrResults;

	
	}
	
	
	function assignDesignCode($opm_productid,$designcode) {
	
		$sql = "UPDATE opm_products SET designcode = ".$this->db->escape($designcode)." WHERE opm_productid = " . $this->db->escape($opm_productid);
		
		if ($this->db->query($sql))
			return true;
		else
			return false;
			
	
	}
	
	function checkDesignCode($opm_productid,$propertyid) { // check to make sure opm_productid is from correct property.
	
		$sql = "SELECT propertyid FROM opm_products WHERE opm_productid = " . $this->db->escape($opm_productid);
		$query = $this->db->query($sql);
		
		if ($row = $query->row()) {
		
			if ($row->propertyid == $propertyid)
				return true;
			else
				return false;
		
		} else {
		
			return false;
		
		}
		
			
	}
	
	function checkIfDuplicateDesignCode($designCode,$opm_productid,$propertyid) {
	
		$sql = "SELECT opm_productid FROM opm_products 
				WHERE designcode = ".$this->db->escape($designCode)."
				AND propertyid = ".$this->db->escape($propertyid)."
				AND opm_productid <> " . $this->db->escape($opm_productid);
			
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
			return true;
		else
			return false;
				
	
	}
	
	function setActiveResubmit($opm_productid,$trueFalse = 0) {
	
		$sql = "UPDATE opm_products SET active_resubmit = ". $this->db->escape($trueFalse)." WHERE opm_productid = " . $this->db->escape($opm_productid);
		
		if ($query = $this->db->query($sql))
			return true;
		else
			return false;
				
	
	}
	
	function fetchProductsForExport() {
	
		$sql = "SELECT opm_productid FROM opm_products WHERE lastmodified > exportdate";
		return $this->db->query($sql);
	
	}
	
	function fetchProductCreators($showDeleted = false) {
	
		$sql = "SELECT DISTINCT opm_products.createdby AS userid, users.username, opm_usergroups.usergroup
				FROM opm_products
				LEFT JOIN users ON users.userid = opm_products.createdby
				LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
				WHERE users.username IS NOT NULL 
				 ";
		
		if (!$showDeleted) {
		
		 	$sql .= " AND opm_products.deletedate = 0";
			
		}
		
		return $this->db->query($sql);
	
	}
	
	function setExportDate($productIDs) { // accepts array or single id.
	
		if (is_array($productIDs)) {
		
			$strProductIDs = implode(",",$productIDs);
		
		} else {
		
			$strProductIDs = $productIDs;
		
		}
			
	
		$sql = "UPDATE opm_products SET exportdate = " . mktime() . " WHERE opm_productid IN (".$strProductIDs.")";
		
		if ($this->db->query($sql))
			return true;
		else
			return false;
	
	}
	
	function changeLockStatus($opm_productid,$status = null) {
		
		$sql = "SELECT islocked FROM opm_products WHERE opm_productid = " . $this->db->escape($opm_productid);
		
		if ($query = $this->db->query($sql)) {
			
			if ($query->row()->islocked == "0" || $status == 1) {
			
				$sql = "UPDATE opm_products SET islocked = 1 WHERE opm_productid = " . $this->db->escape($opm_productid);
		
				if ($this->db->query($sql)) {
					
					if ($status == 1) {
			
						$CI =& get_instance();
									
						$message =  "Product automatically locked";
						$CI->opm->addHistoryItem($opm_productid,$message);
						
					}
					
					return "locked";
					
				}
			
			} else {
			
				$sql = "UPDATE opm_products SET islocked = 0 WHERE opm_productid = " . $this->db->escape($opm_productid);
		
				if ($this->db->query($sql)) {
					
					return "unlocked";
					
				}
			
			
			}
			
		
			
		} else {
			
			return "failed";
			
		}
		
	
		
	}
	
	function saveSampleDates($data) {
		
		/*echo "<pre>";
		print_r($data);
		die();*/
		
		if (isset($data['samplesentdate'])) {
			
			
			$sql = "UPDATE opm_products SET samplesentdate = " . $this->db->escape($data['tsSentDate']) . " WHERE opm_productid = " . $this->db->escape($data['opm_productid']);
			
			if ($this->db->query($sql)) {
			
				return $data['samplesentdate'];
			
			}
			
		} else if (isset($data['samplerecdate'])) {
			
			$sql = "UPDATE opm_products SET samplerecdate = " . $this->db->escape($data['tsRecDate']) . " WHERE opm_productid = " . $this->db->escape($data['opm_productid']);
			
			if ($this->db->query($sql)) {
			
				return $data['samplerecdate'];
			
			}
			
			
		} else {
			
			
			
		}
		
		
		if ($this->db->query($sql))
			return true;
		else
			return false;
			
			
		
	}
	
	function saveSampleNotes($data) {
		
		$sql = "UPDATE opm_products SET samplenotes = " . $this->db->escape($data['notes']) . " WHERE opm_productid =   " . $this->db->escape($data['opm_productid']);
		$this->db->query($sql);
				
		if ($this->db->query($sql))
			return true;
		else
			return false;
			
			
		
	}
	
	function saveWholesaleInfo($postdata) {
		
		// are we adding or updating?
		
		/*echo "<pre>";
		print_r($postdata);
		die();*/
		
		if ($postdata['isactive'] == 'on')
			$postdata['isactive'] = 1;
		else
			$postdata['isactive'] = 0;
			
			
		if ($postdata['isfeatured'] == 'on')
			$postdata['isfeatured'] = 1;
		else
			$postdata['isfeatured'] = 0;
			
		
		$sql = "SELECT id FROM opm_ws_products WHERE opm_productid = " . $this->db->escape($postdata['opm_productid']);
		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0) { // we are adding
		
			$sql = "INSERT INTO opm_ws_products
					(sitebrandid,opm_productid,isactive,baseprice,isfeatured)
					VALUES (".$this->db->escape($postdata['sitebrandid']).",".$this->db->escape($postdata['opm_productid']).",
					".$this->db->escape($postdata['isactive']).",".$this->db->escape($postdata['baseprice']).",
					".$this->db->escape($postdata['isfeatured']).")";
			
			$this->db->query($sql);
		
		
		} else { // we are updating
		
		
			$sql = "UPDATE opm_ws_products SET
					sitebrandid = ".$this->db->escape($postdata['sitebrandid']).",
					isactive = ".$this->db->escape($postdata['isactive']).",
					baseprice = ".$this->db->escape($postdata['baseprice']).",
					isfeatured = ".$this->db->escape($postdata['isfeatured'])."
					";
			
			$this->db->query($sql);
			
		}
		
		if ($postdata['add_sizeid'] && $postdata['add_sku']) {
			
			$sql = "INSERT INTO opm_ws_product_sizes (opm_productid,sizeid,sku,isactive,createdby,lastmodified)
			VALUES(".$this->db->escape($postdata['opm_productid']).",
			".$this->db->escape($postdata['add_sizeid']).",
			".$this->db->escape($postdata['add_sku']).",1,
			".$this->userinfo->userid.",
			".mktime().")";
			
			$this->db->query($sql);
			
			
		}
		
		return true;
			
	}
	
	function setExploitStatus($opm_productid,$statusid) {
		
		$sql = "UPDATE opm_products SET exploitstatusid = " . $this->db->escape($statusid) . " WHERE opm_productid = " . $this->db->escape($opm_productid);
		
		if ($this->db->query($sql))
			return true;
		else
			return false;
			
			
		
	}
	
	function setUsageStatus($opm_productid,$statusid) {
		
		$sql = "UPDATE opm_products SET usagestatusid = " . $this->db->escape($statusid) . " WHERE opm_productid = " . $this->db->escape($opm_productid);
		
		if ($this->db->query($sql))
			return true;
		else
			return false;
			
			
		
	}
	
	function createProductLink($opm_productid,$opmIDToLink) {
		
		$sql = "INSERT INTO opm_product_links (opm_productid,linked_id,createdate,createdby)
				VALUES(" . $this->db->escape($opm_productid) . "," . $this->db->escape($opmIDToLink) . "," . mktime() . "," . $this->db->escape($this->userinfo->userid) . ")";		
				
		if ($this->db->query($sql))
			return true;
		else
			return false;
		
		
	}
	
	function checkOpmProductID($opm_productid) {
		
		$sql = "SELECT opm_productid FROM opm_products WHERE opm_productid = " . $this->db->escape($opm_productid);		
		
		$query = $this->db->query($sql);
			
		if ($query->num_rows() > 0)
			return true;
		else
			return false;
		
		
	}
	
	function removeProductLink($linkid) {
		
		$sql = "DELETE FROM opm_product_links WHERE id = " . $this->db->escape($linkid);		
			
		if ($query = $this->db->query($sql))
			return true;
		else
			return false;
		
		
	}
    
   
}

?>