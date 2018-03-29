<?

class Users_model extends CI_Model {

    function UsersModel()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function fetchUsers($returnTotal = false, $offset = 0, $perPage = null, $usergroupid = null, $appPropertyID = null, $username = null, $includeDisabled = false, $login = null, $permissionid = null, $caninvoice = null, $orderby = null, $officeid = null, $deptid = null, $restrictByCreator = false) {
    
    	$CI =& get_instance();
		$CI->load->model('usergroups_model');
    
    	// if we are searching for a ugid, we must also include children in the search!
    	
    	if ($usergroupid) {
    	
    		
			$childrenIDs = $CI->usergroups_model->getChildren($usergroupid);
			$childrenIDs[] = $usergroupid;

			$strUsergroupIDs = implode(",",$childrenIDs);
				
    	}
    
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT users.*, opm_usergroups.usergroup, ug2.usergroup AS usergroup2";
    	else 
    		$sql = "SELECT users.userid";

    			
    	$sql .=	" FROM users 
    			  LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
    			  LEFT JOIN opm_usergroups AS ug2 ON ug2.usergroupid = users.usergroupid2 ";
    	
    	if ($appPropertyID) {
    	
    		$sql .= " LEFT JOIN opm_user_app_properties ON opm_user_app_properties.userid = users.userid";
    	
    	}
    	
    			
    	$sql .= " WHERE users.userid <> 0 ";
    	
    	if ($appPropertyID)
    		$sql .= " AND opm_user_app_properties.propertyid = '".$appPropertyID."'";
    			
    	if ($usergroupid)
    		$sql .= " AND ( users.usergroupid IN (".$strUsergroupIDs.") OR users.usergroupid2 IN (".$strUsergroupIDs."))";
    	
    	if ($username)
    		$sql .= " AND users.username LIKE '%".$username."%'";
    		
		if ($login)
    		$sql .= " AND users.login LIKE '%".$login."%'";
    		
    	if (!$includeDisabled)
    		$sql .= " AND users.isactive = 1";
    		
    	    	if ($permissionid) {
    	
    		// first get all the usergroups that have that perm, with children...
    		
    		$sql2 = "SELECT usergroupid FROM opm_usergroup_perms WHERE permid = " . $this->db->escape($permissionid);
    		$query2 = $this->db->query($sql2);
    		
    		$pUgIDs = array();
    		
    		foreach ($query2->result() as $row) {
    		
    			$tempUgIDs = $CI->usergroups_model->getChildren($row->usergroupid);
    			$tempUgIDs[] = $row->usergroupid;
    			$pUgIDs = array_merge($pUgIDs, $tempUgIDs);
    		}
    		
    		$strPermUgIDs = implode(",",$pUgIDs);
    		    	
			$sql .= " AND ( users.usergroupid IN (".$strPermUgIDs.") OR users.usergroupid2 IN (".$strPermUgIDs.") ) ";
    		    	
    	}
    		
    	if ($caninvoice)
    		$sql .= " AND users.caninvoice = " . $this->db->escape($caninvoice);
    	
    	if ($restrictByCreator && !checkPerms('admin_all_users')) {
	    	
	    	$sql .= " AND users.createdby = " . $this->db->escape($this->userinfo->userid);
	    	
    	}
    	
    	if ($orderby)
    		$sql .=" ORDER BY $orderby";
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";

    	
    	
    	
    	$query = $this->db->query($sql);
    	
    	//echo $query->num_rows();
    	//exit();
    	
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
	function fetchUsersFromUsergroups($strUserGroups) {
	
		if ($strUserGroups) { // make sure str isn't empty
    
			$sql = "SELECT users.userid,users.username,users.login,GROUP_CONCAT(opm_preferences.pref) as preferences
					FROM users
					LEFT JOIN opm_user_preferences ON opm_user_preferences.userid = users.userid
					LEFT JOIN opm_preferences ON opm_preferences.prefid = opm_user_preferences.prefid
					WHERE (usergroupid IN ($strUserGroups)
					OR usergroupid2 IN ($strUserGroups))
					AND users.isactive = 1
					GROUP BY users.userid";
			
			$query = $this->db->query($sql);
			
			return $query;
    	
    	} else { // no usergroup ids sent!
    	
    		return false;
    	
    	}

    }
    
    function fetchInvoiceUsers() {
    	
    	// we need to get all the users in Designers, Separators and Screenprinters
    	
    	$usergroups = array();
    	
    	$sql = "SELECT usergroupid FROM opm_usergroups 
    			WHERE parentid IN (".$this->config->item('designersGroupID').",".$this->config->item('separatorsGroupID').",".$this->config->item('screenprintersGroupID').")
    			OR usergroupid IN (".$this->config->item('designersGroupID').",".$this->config->item('separatorsGroupID').",".$this->config->item('screenprintersGroupID').")";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $row) 
			$usergroups[] = $row->usergroupid;
			
		$strUsergroups = implode(",",$usergroups);
		
		$sql = "SELECT users.userid,users.usergroupid, users.username, opm_usergroups.usergroup
				FROM users
				LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
				WHERE ( users.usergroupid IN (".$strUsergroups.") OR users.usergroupid2 IN (".$strUsergroups.") )
				AND caninvoice = 1
				ORDER BY username";
		
		$query = $this->db->query($sql);
    	
    	return $query;
    	
    
    }
    
    function fetchInternalUsers() {
    	
    	// we need to get all the users in Designers, Separators and Screenprinters
    	
    	$usergroupid = $this->config->item('bravadoInternalGroupID');
    	
    	$CI =& get_instance();
		$CI->load->model('usergroups_model');
		$childrenIDs = $CI->usergroups_model->getChildren($usergroupid);
		$childrenIDs[] = $usergroupid;

		$strUsergroupIDs = implode(",",$childrenIDs);
		
		$sql = "SELECT users.userid,users.usergroupid, users.username, opm_usergroups.usergroup
				FROM users
				LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
				WHERE ( users.usergroupid IN (".$strUsergroupIDs.") OR users.usergroupid2 IN (".$strUsergroupIDs.") )
				AND isactive = 1
				ORDER BY username";
		
		$query = $this->db->query($sql);
    	
    	return $query;
    	
    
    }
    
    
    
	function fetchDesigners($inc_inactive = false, $opm_productid = null, $returnAllDesigners = true, $includeDesignersWithAssignedProducts = false) {
    
    	// if we are searching for a ugid, we must also include children in the search!
    	
    	$usergroupid = $this->config->item('designersGroupID');
    	
    	if ($inc_inactive) {
    		
    		$query_where = " ";
    		
    	} else { 
    	
    		$query_where = " AND (users.isactive = 1 ";
    		
    		// if we are querying for a product, we need to include assigned designers even if they aren't active.
    		
    		if ($opm_productid || $includeDesignersWithAssignedProducts) 
    			$query_where .= "OR opm_products_designers.lineid IS NOT NULL )";
    		else
    			$query_where .= ")";
    	
    	}
    	
    	if (!$returnAllDesigners)
    		$query_where .= " AND opm_products_designers.lineid IS NOT NULL ";
  
    	    	
		$CI =& get_instance();
		$CI->load->model('usergroups_model');
		$childrenIDs = $CI->usergroups_model->getChildren($usergroupid);
		$childrenIDs[] = $usergroupid;

		$strUsergroupIDs = implode(",",$childrenIDs);

    	$sql = "SELECT users.*,opm_products_designers.lineid AS isassigned
    			FROM users
    			LEFT JOIN opm_products_designers ON opm_products_designers.userid = users.userid ";
    			
    			if ($opm_productid || $opm_productid == 0)
    				$sql .=" AND opm_products_designers.opm_productid = '".$opm_productid."' ";
    			
    			$sql .= "
    			WHERE ( users.usergroupid IN (".$strUsergroupIDs.") OR users.usergroupid2 IN (".$strUsergroupIDs."))
    			$query_where
    			GROUP BY users.userid
    			ORDER BY users.username";
 
 		//echo $sql;
 
    	$query = $this->db->query($sql);
    	return $query;

    
    }
    
    function fetchInvoiceOwners($inc_inactive = false, $invoiceid = 0, $incAllOwners = true) {
    
    	// if we are searching for a ugid, we must also include children in the search!
    	
    	$usergroupid = $this->config->item('productManagersGroupID');
    	
    	if ($inc_inactive)
    		$query_where = " AND users.isactive = 1";
    	else
    		$query_where = "";
    	
    	
    	if (!$incAllOwners)
    		$query_where .= " AND opm_invoices_cc.id IS NOT NULL ";

    	$sql = "SELECT users.*,opm_invoice_cc.id AS isassigned
    			FROM users
    			LEFT JOIN opm_invoice_cc ON opm_invoice_cc.userid = users.userid AND opm_invoice_cc.invoiceid = '".$invoiceid."'
    			WHERE ( users.usergroupid = ".$usergroupid." OR users.usergroupid2 = ".$usergroupid.")
    			$query_where
    			GROUP BY users.userid
    			ORDER BY users.username";
 
    	$query = $this->db->query($sql);
    	return $query;

    }
    
      
    function fetchUserInfo($userid, $liteMode = false) // litemode is for simple queries
    {  	
       	
       	// get general userinfo
       	       	
       	$sql = "SELECT users.*,opm_usergroups.usergroup, opm_currencies.id AS currencyid, opm_currencies.currency AS currency, opm_currencies.currencysymbol
       			FROM users 
       			LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
       			LEFT JOIN opm_currencies ON opm_currencies.id = users.currencyid
       			WHERE userid = '".$userid."'";
		
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0) {
		
			$userinfo = $query->row();
				
			$userinfo->password = $this->opm->text_decrypt($userinfo->password);
			
			if (!$liteMode) {
			
				// get permissions (including parent permissions)
				
				// first, if user is super admin, auto get all perms!
				
				if (in_array($userinfo->userid, $this->config->item('superAdmins'))) {
					
					$userinfo->isSuperAdmin = true;
					
					$sql = "SELECT opm_perms.permid, opm_perms.perm, 0 as isexplicit
							FROM opm_perms";
				
				} else { // user is not super admin, fetch perms.
				
					$CI =& get_instance();
				
					$CI->load->model('usergroups_model');
					$parentids = $CI->usergroups_model->getParents($userinfo->usergroupid);
					
					if($userinfo->usergroupid2) {
						
						$parentids2 = $CI->usergroups_model->getParents($userinfo->usergroupid2);
						$parentids2[] = $userinfo->usergroupid2;
						
						$parentids = array_merge($parentids,$parentids2);
					
					}
					
					if (is_array($parentids))
						$strIN = "'".$userinfo->usergroupid."','" . implode("','",$parentids) . "'";
					else 
						$strIN = "'".$userinfo->usergroupid."'";
					
					$sql = "
					
							SELECT  opm_perms.permid, opm_perms.perm,1 as isexplicit
							FROM opm_user_perms
							LEFT JOIN opm_perms ON opm_perms.permid  = opm_user_perms.permid
							WHERE opm_user_perms.userid = ".$this->db->escape($userinfo->userid)."
					
							UNION
				
							SELECT opm_perms.permid, opm_perms.perm, 0 as isexplicit
							FROM opm_usergroup_perms
							LEFT JOIN opm_perms ON opm_perms.permid  = opm_usergroup_perms.permid
							WHERE opm_usergroup_perms.usergroupid IN ($strIN)

							
							ORDER BY permid
							
											
							
							";
				
				}
				
				
				$query = $this->db->query($sql);
				
				
				foreach ($query->result() as $row) {
														
					$userinfo->perms[$row->perm] = array(
						"permid"=>$row->permid,
						"isexplicit"=>$row->isexplicit
					);
				
				}
			
				//	echo "<pre>";
				//  print_r($userinfo->perms);	
				//	die();
				
				// get all usergroups (including parents) - Exclude propertyContacts and designers groups, for they don't make a difference as far as searches / view rights goes.
				
				$CI =& get_instance();
				$CI->load->model('usergroups_model');
				$CI->load->model('territories_model');
				
				$UGs1 = $CI->usergroups_model->getParents($userinfo->usergroupid);
				$UGs2 = $CI->usergroups_model->getParents($userinfo->usergroupid2);
							
				$userinfo->viewRightsUserGroups = array_merge($UGs1,$UGs2);
				
				//if ($userinfo->usergroupid != $this->config->item('propertyContactsGroupID') && $userinfo->usergroupid != $this->config->item('designersGroupID'))
					$userinfo->viewRightsUserGroups[] = $userinfo->usergroupid;
				
				//if ($userinfo->usergroupid2 && $userinfo->usergroupid2 != $this->config->item('propertyContactsGroupID') && $userinfo->usergroupid2 != $this->config->item('designersGroupID'))
					$userinfo->viewRightsUserGroups[] = $userinfo->usergroupid2;
					
				// if we are a licensee (and don't have perms to see all products), flag to pull properties from assignments rather than associated products

				if ((in_array($CI->config->item('licenseeGroupID'), $userinfo->viewRightsUserGroups)) && !isset($userinfo->perms['view_all_products'])) {
				
					$userinfo->usePropAssignments = true;
				
				} else {
				
					$userinfo->usePropAssignments = false;
					
				}
					
				// get all ids for which user is property contact.
				
				$userinfo->approvalProperties = $this->getApprovalPropertiesByContact($userinfo->userid);
				
				// get all territories according to office.
				
				if ($userinfo->officeid) {
				
					$userinfo->territories = $CI->territories_model->fetchOfficeTerritories($userinfo->officeid);
				
				} else {
				
					$userinfo->territories = array();
				
				}
				
				// get all propertyids for which user is designer
				
				$userinfo->designerProperties = $this->getDesignerPropertiesByContact($userinfo->userid);
				
				// get all propertyids that user has opted to receive emails for!
				
				$userinfo->prefProperties = $this->getPrefProperties($userinfo->userid);
				
				// get preferences!
				
				$userinfo->prefs = array();
				
				$sql = "SELECT opm_preferences.pref 
						FROM opm_user_preferences
						LEFT JOIN opm_preferences ON opm_preferences.prefid = opm_user_preferences.prefid
						WHERE opm_user_preferences.userid = " . $this->db->escape($userid);
				
				$query = $this->db->query($sql);
				
				foreach ($query->result() as $row)
					$userinfo->prefs[$row->pref] = true;
					
			
			}
			
			return $userinfo;
		
		} else {
		
			return false;
		
		}		 	
        
    }
    
    function fetchPermissions($userid) { // get all permissions with assignments for a user.

    	// first get usergroup info for user.
    	
    	$CI =& get_instance();
	
		$CI->load->model('usergroups_model');
		$CI->load->model('users_model');
		
		$user = $CI->users_model->fetchUserInfo($userid);
		
		if (in_array($userid,$this->config->item('superAdmins'))) { // superuser auto has all perms
		
			$sql = "
	    	
	    		SELECT opm_perms.*, opm_permgroups.permgroup, 1 AS hasperm, 1 AS haspermexplicit
	    		FROM opm_perms
	    		LEFT JOIN opm_permgroups ON opm_permgroups.permgroupid = opm_perms.permgroupid
	    		ORDER BY opm_permgroups.displayorder,opm_perms.displayorder
	    	";
			
		
		} else {
		
			$parentids = $CI->usergroups_model->getParents($user->usergroupid);
			
			if($user->usergroupid2) {
				
				$parentids2 = $CI->usergroups_model->getParents($user->usergroupid2);
				$parentids2[] = $user->usergroupid2;
				
				$parentids = array_merge($parentids,$parentids2);
			
			}
			
			if (is_array($parentids))
				$strIN = "'".$user->usergroupid."','" . implode("','",$parentids) . "'";
			else 
				$strIN = "'".$user->usergroupid."'";
	    
	    
	    	$sql = "
	    	
	    		SELECT opm_perms.*, opm_permgroups.permgroup, opm_usergroup_perms.id AS hasperm, opm_user_perms.id AS haspermexplicit
	    		FROM opm_perms
	    		LEFT JOIN opm_usergroup_perms ON opm_usergroup_perms.permid = opm_perms.permid AND opm_usergroup_perms.usergroupid IN ($strIN)
	    		LEFT JOIN opm_user_perms ON opm_user_perms.permid = opm_perms.permid AND opm_user_perms.userid = ".$this->db->escape($user->userid)."
	    		LEFT JOIN opm_permgroups ON opm_permgroups.permgroupid = opm_perms.permgroupid
	    		ORDER BY opm_permgroups.displayorder,opm_perms.displayorder
	    	";
	    	
	    }
    	
    	
    	return $this->db->query($sql);
    	    
    }
    
   
    
    function checkLoginInfo($email,$password)
    {  	
       	
       	
       	$sql = "SELECT * FROM users WHERE login = ".$this->db->escape($email)." AND isactive = 1";
		$query = $this->db->query($sql);
      	$row = $query->row();
      	
      	if ($row->password_changed) { // use new verification
	      	
	   	      	
	      	$hashed = $row->password;
	      	$password = $password;

	      	if ($this->phpass->check($password, $hashed))
	     	 	return $row->userid;
	      	else
	      		return false;
	      	
	      	
      	} else { // use old
	      	
	      	if ($this->opm->text_decrypt($row->password) == $password)
      			return $row->userid;
      		else
      			return false;
	      	
	      	
      	}
      	
      	
		 	
        
    }
    
    function retrievePassword($email)
    {  	
       	
       	$sql = "SELECT * FROM users WHERE login = ".$this->db->escape($email)." AND isactive = 1";
		$query = $this->db->query($sql);
      	
      	if ($row = $query->row())
      		return $row;
      	else
      		return false; 	
        
    }
    
    function saveUserInfo($arrPostData) {

     	
     	if (isset($arrPostData['userid'])) { // we have a userid, update
     	
     		/*echo "<pre>";
     		print_r($arrPostData);
     		echo "</pre>";
     		
     		die();*/
     		
     		// deal w/ isactive checkbox
     		
     		if ($arrPostData['isactive'] == 'on')
     			$arrPostData['isactive'] = 1;
     		else
     			$arrPostData['isactive'] = 0;
     	
     		$sql = "UPDATE users
     				SET usergroupid = ". $this->db->escape($arrPostData['usergroupid']).",
     				usergroupid2 = ".$this->db->escape($arrPostData['usergroupid2']).",
     				officeid = ".$this->db->escape($arrPostData['officeid']).",
     				username = ".$this->db->escape($arrPostData['username']).",
     				nv_customerid = ".$this->db->escape($arrPostData['nv_customerid']).",
     				login = ".$this->db->escape($arrPostData['login']).",
     				isactive = ".$this->db->escape($arrPostData['isactive']).",
     				address = ".$this->db->escape($arrPostData['address'])." ";
     				
     		/*if (isset($arrPostData['submitPassword']))
     			$sql .= ", password = '".$this->opm->text_crypt($arrPostData['password'])."' ";*/
     				
     		if (isset($arrPostData['avatar_path']))
     			$sql .= ", avatar_path = '".$arrPostData['avatar_path']."'";
     				
     		$sql .= " WHERE userid = '".$arrPostData['userid']."'";
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['userid'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new user, insert
     	
     		
     		$sql = "INSERT INTO users (nv_customerid,usergroupid,usergroupid2,officeid,username,login,password,address,currencyid,isactive,createdate,password_changed,password_reset)
    			VALUES (". $this->db->escape($arrPostData['nv_customerid']).",". $this->db->escape($arrPostData['usergroupid']).",". $this->db->escape($arrPostData['usergroupid2']).",". $this->db->escape($arrPostData['officeid']).",".$this->db->escape($arrPostData['username']).",".$this->db->escape($arrPostData['login']).",".$this->db->escape($this->phpass->hash($arrPostData['password'])).",".$this->db->escape($arrPostData['address']).",".$this->config->item('USDollarsCurrencyID').",1,'".mktime()."',1,1)";
    	
       		
       		if ($query = $this->db->query($sql)) {
       		
       			return $this->db->insert_id(); 
       		
       		} else {
       		
       			return false;
       		
       		}
       		
     	
     	}
    
    }
    
    function saveInvoiceInfo($arrPostData) {

 		
 		// deal w/ checkboxs
 		
 		if ($arrPostData['ishourly'] == 'on')
 			$arrPostData['ishourly'] = 1;
 		else
 			$arrPostData['ishourly'] = 0;
 			
 		if ($arrPostData['caninvoice'] == 'on')
 			$arrPostData['caninvoice'] = 1;
 		else
 			$arrPostData['caninvoice'] = 0;
 			
 		
 		if ($arrPostData['notestoinvoices'] == 'on')
 			$arrPostData['notestoinvoices'] = 1;
 		else
 			$arrPostData['notestoinvoices'] = 0;
 	
 		$sql = "UPDATE users
 				SET taxid = ". $this->db->escape($arrPostData['taxid']).",
 				vatnumber = ". $this->db->escape($arrPostData['vatnumber']).",
 				submissionfee = ".$this->db->escape($arrPostData['submissionfee']).",
 				ishourly = ".$this->db->escape($arrPostData['ishourly']).",
 				caninvoice = ".$this->db->escape($arrPostData['caninvoice']).",
 				currencyid = ".$this->db->escape($arrPostData['currencyid']).",
 				staddress = ".$this->db->escape($arrPostData['staddress']).",
 				staddress2 = ".$this->db->escape($arrPostData['staddress2']).",
 				city = ".$this->db->escape($arrPostData['city']).",
 				state = ".$this->db->escape($arrPostData['state']).",
 				zip = ".$this->db->escape($arrPostData['zip']).",
 				notestoinvoices = ".$this->db->escape($arrPostData['notestoinvoices']).",
 				notes = ".$this->db->escape($arrPostData['notes']).",
 				hourlyrate = ".$this->db->escape($arrPostData['hourlyrate'])." ";

 		if (isset($arrPostData['invoiceimage_path']))
 			$sql .= ", invoiceimage_path = '".$arrPostData['invoiceimage_path']."'";
 				
 		$sql .= " WHERE userid = '".$arrPostData['userid']."'";
 
	
		if ($this->db->query($sql)) {
		
			return $arrPostData['userid'];
		
		} else {
		
			return false;
		
		}
    		

    
    }
    
     function savePermissions($arrPostData) {

		/*echo "<pre>";
		print_r($arrPostData);
		die();*/
	
		// first delete all user explicit perms
		
		$sql = "DELETE FROM opm_user_perms WHERE userid = " . $this->db->escape($arrPostData['userid']);
		$this->db->query($sql);
		
		// now insert checked perms!
		
		if (is_array($arrPostData['chkbox'])) {
		
		
			foreach ($arrPostData['chkbox'] as $permid=>$onoff) {
 		
 				$sql = "INSERT INTO opm_user_perms (permid,userid) VALUES (".$this->db->escape($permid).",".$this->db->escape($arrPostData['userid']).")";
 				$this->db->query($sql);
 		
 			}
		
		
		}
		
		
		return $arrPostData['userid'];
    
    }
    
    function checkIfEmailExists($email,$userid) {
    	
    	$sql = "SELECT login FROM users WHERE login = " . $this->db->escape($email);
    	
    	if ($userid)
    		$sql .= " AND userid <> " . $this->db->escape($userid);
    	
    	$result = $this->db->query($sql);
    	
    	if ($result->num_rows == 0)
    		return false;
    	else
    		return true;
    	
    
    }
    
    
    function getSepsSCPsForProduct($mode, $opm_productid) { // this functions gets either screen printers or separators associated with a product!
    	
    	$CI =& get_instance();
    	
    	if ($mode == 'separators')
    		$parentUGID = $CI->config->item('separatorsGroupID');
    	else if ($mode == 'screenprinters')
    		$parentUGID = $CI->config->item('screenprintersGroupID');
    	else
    		die("invalid mode set for get SCPs,SEPs function");
    			
		$CI->load->model('usergroups_model');
		$childids = $CI->usergroups_model->getChildren($parentUGID);
		$childids[] = $parentUGID;
		
		$strChildIDs = implode(",", $childids); // strChildIDs contains all of the usergroup ids within separators.
		
		// now we must find out which of these ugids are assigned to the product in question.
		
		$sql = "SELECT opm_products_usergroups.usergroupid FROM opm_products_usergroups
				WHERE opm_products_usergroups.opm_productid = ".$this->db->escape($opm_productid)."
				AND opm_products_usergroups.usergroupid IN (".$strChildIDs.")";
		//die($sql);
		
		$query = $this->db->query($sql);
		
		$users = array();
		
		foreach ($query->result() as $row) {
		
			$result2 = $this->fetchUsers(false,0,null,$row->usergroupid);
			
			foreach ($result2->result() as $row) {
			
				$users[] = array("userid"=>$row->userid,"email"=>$row->login,"username"=>$row->username,"usergroup"=>$row->usergroup);
			
			}
			
		}
    	
    	return $users;
			
    
    }
    
    function getApprovalPropertiesByContact($id) {
    
    	$propertyIDs = array();
    
    	$sql = "SELECT propertyid FROM opm_user_app_properties 
    			WHERE userid = " . $this->db->escape($id) . "
    			AND (enddate = 0
    			OR enddate > " . mktime() . ")";
    	
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() > 0) {
    	
    		foreach ($query->result() as $row)
    			$propertyIDs[] = $row->propertyid;
    	
    	}
    	
    	return $propertyIDs;
    
    }
    
    function getDesignerPropertiesByContact($id) {
    
    	$propertyIDs = array();
    
    	$sql = "SELECT opm_products.propertyid
    			FROM opm_products_designers
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_designers.opm_productid
    			WHERE opm_products_designers.userid = " . $id;
    	
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() > 0) {
    	
    		foreach ($query->result() as $row)
    			$propertyIDs[] = $row->propertyid;
    	
    	}
    	
    	return $propertyIDs;
    
    }
    
    function getPrefProperties($id) {
    
    	$propertyIDs = array();
    
    	$sql = "SELECT opm_user_properties.propertyid
    			FROM opm_user_properties
    			WHERE opm_user_properties.userid = " . $id . "
    			AND rcvemail = 1";
    	
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() > 0) {
    	
    		foreach ($query->result() as $row)
    			$propertyIDs[] = $row->propertyid;
    	
    	}
    	
    	return $propertyIDs;
    
    }
    
    function fetchDesignerQueue($id = 0) {
    
    	$CI =& get_instance();
		$CI->load->model('usergroups_model');
    
    	$designerUsergroups = $CI->usergroups_model->getChildren($this->config->item('designersGroupID'));
    	$designerUsergroups[] = $this->config->item('designersGroupID');
    	$strDesignerUserGroups = implode(",", $designerUsergroups);
    	
    	/*echo "<pre>";
    	print_r($designerUsergroups);
    	echo "</pre>";*/
    
    	$sql = "SELECT users.*,opm_products.*,properties.property
    			FROM users
    			LEFT JOIN opm_products_designers ON opm_products_designers.userid = users.userid
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_designers.opm_productid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			WHERE (users.usergroupid IN ($strDesignerUserGroups) OR users.usergroupid2 IN ($strDesignerUserGroups) )
    			AND opm_products.opm_productid IS NOT NULL
    			AND opm_products.design_completed = 0
    			AND opm_products.duedate <> 0
    			GROUP BY opm_products.opm_productid
    			ORDER BY users.username";
    			
    	
    	$query = $this->db->query($sql);
    	
    	return $query;
    
    }
    
    //Globosoft
	function checkUserGroup($user_id)
    {  	
       	
       	$sql = "SELECT usergroupid FROM users WHERE userid = ".$user_id."";
		
		$query = $this->db->query($sql);
      	$row = $query->row();
      	return $row->usergroupid;
     }


	function fetchPermissionsList() {

       	$sql = "SELECT DISTINCT opm_perms.permid, opm_perms.permtext 
				FROM opm_perms";
      
        $query = $this->db->query($sql);
		return $query;
		
	}

   //Globosoft
	function userDefaultPrefrence($userid,$usergroupid) {
	
		 if ($userid) {
		
			$sql = "INSERT INTO opm_user_preferences (userid,prefid) VALUES(".$userid.",'1')";
		 
			$this->db->query($sql);
							
			$sql = "INSERT INTO opm_user_preferences (userid,prefid) VALUES(".$userid.",'3')";
			
			$this->db->query($sql);
	
			if ($usergroupid == 3) {
				
				$sql = "INSERT INTO opm_user_preferences (userid,prefid) VALUES(".$userid.",'11')";
				$this->db->query($sql);
							
			}
		    
		    return true;
							
		} else {
		
			return false;
		}
	
	}
	//
	
	function changePassword($postdata,$userid = 0, $resetPassword = 0) {
	
		$sql = "
			
			UPDATE users
			SET password = '".$this->phpass->hash($postdata['newPassword'])."', 
			password_changed = 1, ";
		
		if ($resetPassword)
			$sql .= " password_reset = 1 ";
		else
			$sql .= " password_reset = 0 ";

			
		if ($userid)
			$sql .=	 " WHERE users.userid = " . $this->db->escape($userid);
		else
			$sql .=	 " WHERE users.userid = " . $this->userinfo->userid;
	
		if ($this->db->query($sql)) {
		
			return true;
		
		} else {
		
			return false;
		
		}
		
	}


}

?>