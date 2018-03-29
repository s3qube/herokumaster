<?

class Usergroups_model extends CI_Model {

    function UsergroupsModel()
    {
        // Call the Model constructor
		parent::__construct();   
			
	 }
    
    function fetchUsergroupsByParent($parentid,$opm_productid) { //
    
    	// for email picker. return 1dim array of usergroups with parent id of X
    	
    	$arrChildren = $this->getChildren($parentid);
    	
    	$strIDs = implode(",",$arrChildren);
    	
    	$sql = "SELECT opm_usergroups.usergroupid, opm_usergroups.usergroup
				FROM opm_products_usergroups 
				LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = opm_products_usergroups.usergroupid
				WHERE opm_products_usergroups.opm_productid = " . $this->db->escape($opm_productid) . "
				AND opm_usergroups.usergroupid IN (".$strIDs.")";
			
		$query = $this->db->query($sql);
		
		return $query;
				
    
    }
    
    function fetchPropertyLicensees($propertyid) { //
    	    	
    	$sql = "SELECT opm_usergroups.usergroupid, opm_usergroups.usergroup
				FROM opm_usergroup_properties 
				LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = opm_usergroup_properties.usergroupid
				WHERE opm_usergroup_properties.propertyid = " . $this->db->escape($propertyid);
			
		$query = $this->db->query($sql);
		
		return $query;
				
    
    }
    
    function checkDelete($usergroupid) { // see if a usergroup can be deleted
    
    	$errors = array();
    	
    	// check if any users belong to group. // must also check all children
    	
    	// get info about usergroup in question.
    	
    	$ug = $this->fetchUsergroup($usergroupid);
    	
    	    	
    	$arrUGs = $this->getChildren($usergroupid);
    	$arrUGs[] = $usergroupid;
    	$strIDs = implode(",",$arrUGs);
    	    	
    	$sql = "
    			SELECT 1 AS ug1,0 AS ug2, users.*, opm_usergroups.usergroup, opm_usergroups.usergroupid FROM users 
    			LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid
    			WHERE users.usergroupid IN (".$strIDs.")
    			
    			UNION ALL
    			
    			SELECT 0 AS ug1,1 AS ug2, users.*, opm_usergroups.usergroup, opm_usergroups.usergroupid FROM users 
    			LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = users.usergroupid2
    			WHERE users.usergroupid2 IN (".$strIDs.")
    			
    			";
    			
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $u) {
    	
    		if ($u->usergroupid == $usergroupid) {
    		
    			$errors[] = "User <a href=\"".base_url()."users/view/".$u->userid."\">" . $u->username . "</a> belongs to " . $u->usergroup;
    		
    		} else {
    		
    			$errors[] = "User " . $u->username . " belongs to " . $u->usergroup . " (child of ".$ug->usergroup.")";
    		
    		}
    	
    	}
    	
    	return $errors;
    
    }
    
    function deleteUsergroup($usergroupID) {
    
    	$errors = $this->checkDelete($usergroupID);
    	
    	if (sizeof($errors) == 0) {
    	
    		$sql = "DELETE FROM opm_usergroups WHERE usergroupid = " . $this->db->escape($usergroupID);
    		$this->db->query($sql);
    		
    		$sql = "DELETE FROM opm_usergroup_perms WHERE usergroupid = " . $this->db->escape($usergroupID);
    		$this->db->query($sql);
    		
    		$sql = "DELETE FROM opm_usergroup_properties WHERE usergroupid = " . $this->db->escape($usergroupID);
    		$this->db->query($sql);
    		
    		return true;
    		
    	
    	}
    
    }
    
     
    function fetchUsergroups($opm_productid = null, $productlineid = null, $fetchOnlySelect = false, $showAll = true, $assignPage = false) { // options allow for pulling usergroups w/ associations 
    
    	// declare vars
    	
    	$query_join = "";
    	$query_select = "";
    	$query_where = "";
		
		if ($opm_productid) { // check if this product is visible by usergroups
       	
       		$query_join = "LEFT JOIN opm_products_usergroups ON opm_products_usergroups.usergroupid = opm_usergroups.usergroupid AND opm_products_usergroups.opm_productid = '".$opm_productid."'";
			$query_select = ", opm_products_usergroups.id AS isassigned";
		}
		
		if ($assignPage) { // used for assign properties page, don't show unassignable ugs.
       	
       		$query_where = " WHERE hidefromassignpage = 0";

		}


		// build array of usergroups, parent child style  
    	
    	$refs = array();
		$list = array();

		$sql = "SELECT opm_usergroups.usergroupid, opm_usergroups.parentid, opm_usergroups.usergroup
				$query_select
				FROM opm_usergroups 
				$query_join
				$query_where
				ORDER BY opm_usergroups.usergroup";
				
				
		$query = $this->db->query($sql);
		
		// the code below puts the groups into a neat parent/child array
		
		foreach ($query->result() as $data) {
			$thisref = &$refs[ $data->usergroupid ];
			
			$thisref['usergroupid'] = $data->usergroupid;
			$thisref['parentid'] = $data->parentid;
			$thisref['usergroup'] = $data->usergroup;
			
			if(isset($data->isassigned))
				$thisref['isassigned'] = true;
		
			if ($data->parentid == 0) {
				
				if (!$fetchOnlySelect || in_array($data->usergroupid, $this->config->item('MultipleSelectUGs')))			
					$list[ $data->usergroupid ] = &$thisref;
			
			} else {
				
				if ((!$fetchOnlySelect || in_array($data->parentid, $this->config->item('MultipleSelectUGs'))))	{		
					
					if ($showAll || !in_array($data->parentid,$this->config->item('hideChildUGs'))) {					
				
						$refs[ $data->parentid ]['children'][ $data->usergroupid ] = &$thisref;
				
					}
					
				}
			}
		}
		
		return $list;
		 
    }
    
    function addUsergroupToProduct($opm_productid,$usergroupid) {
    
    	if($opm_productid && $usergroupid)
    		$sql = "INSERT INTO opm_products_usergroups (opm_productid,usergroupid) VALUES (".addslashes($opm_productid).",".addslashes($usergroupid).")";
		else
			return false;
			
		if($this->db->query($sql))
			return true;
		else
			return false;
    
    }
    
    function removeUsergroupFromProduct($opm_productid,$usergroupid) {
    	
		if ($opm_productid && $usergroupid) {
    	
    		$sql = "DELETE FROM opm_products_usergroups WHERE opm_productid = ".addslashes($opm_productid)." AND usergroupid = ".addslashes($usergroupid);
		
		} else {
		
			return false;
		
		}
			
		if($this->db->query($sql))
			return true;
		else
			return false;
    
    }
    
    function updateProductUsergroups($opm_productid,$arrUGids,$parentid) { // USED BY "CHOSEN" SELECTs.
    
		if ($opm_productid && is_array($arrUGids)) {
		
			$strUGids = implode(",",$arrUGids);
			
			$sql = "DELETE opm_products_usergroups FROM opm_products_usergroups 
					LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = opm_products_usergroups.usergroupid
					WHERE opm_productid = ".addslashes($opm_productid)." 
					AND opm_usergroups.parentid = " . $this->db->escape($parentid);
			
			$this->db->query($sql);
			
			//die($this->db->_error_message());
   	    			    		
	    	foreach ($arrUGids as $ugid) {
	    	
	    		$sql = "INSERT INTO opm_products_usergroups (opm_productid,usergroupid) VALUES (".addslashes($opm_productid).",".addslashes($ugid).")";
				$this->db->query($sql);
	    	
	    	}	
	    		
	    	return true;
			
			
		} else {
		
			return false;
		
		}
		
		
    
    }
    
    function getParents($usergroupid) { // returns array of parents for a given usergroupid
    	
    	$parentids = array();
    
    	$sql = "SELECT U2.usergroupid AS parent1,U3.usergroupid AS parent2,U4.usergroupid AS parent3,U5.usergroupid AS parent4
				FROM opm_usergroups AS U1
				LEFT JOIN opm_usergroups AS U2
					ON U2.usergroupid = U1.parentid
				LEFT JOIN opm_usergroups AS U3
					ON U3.usergroupid = U2.parentid
				LEFT JOIN opm_usergroups AS U4
					ON U4.usergroupid = U3.parentid
				LEFT JOIN opm_usergroups AS U5
					ON U5.usergroupid = U4.parentid
				WHERE U1.usergroupid = '".$usergroupid."'";
				
		$query = $this->db->query($sql);
		
		
		$row = $query->result_array();
					
		for ($x=1;$x<=4;$x++) {
		
			if (isset($row[0]['parent'.$x]))
				$parentids[] = $row[0]['parent'.$x];
		
		}
		

		return $parentids;


    }
    
    function getChildren($usergroupid) { // returns array of children for a given usergroupid
    	
    	global $arrChildIDs;
    	
    	$arrChildIDs = array();
    	
    	if ($usergroupid != 0) // this gets rid of unintentional behavior when ugid is set to 0 (which means none, not parent of all!)
    		$this->fetchChildren($usergroupid);
    	
    	return $arrChildIDs;
    
    }
    
   function fetchChildren($usergroupid, $level = 0) {
   
		global $arrChildIDs;
   		
	   // retrieve all children of $parent
	   
	 	$sql = "SELECT usergroup,usergroupid FROM opm_usergroups WHERE parentid = '".$usergroupid."'";
		$query = $this->db->query($sql);

	   foreach ($query->result() as $row) {
		   $arrChildIDs[] = $row->usergroupid;	
		   $this->fetchChildren($row->usergroupid, $level+1);
	   }
	    
	}
	
    
    function fetchUsergroup($usergroupid) // if verbose=false, it just returns userid
    {  	
       	       	
       	$sql = "SELECT opm_usergroups.*
       			FROM opm_usergroups 
       			WHERE usergroupid = '".$usergroupid."'";
		
		$query = $this->db->query($sql);
		$usergroup = $query->row();
		
		// get usergroupids of parents!
		
		$parentids = $this->getParents($usergroupid);
		
		if (is_array($parentids))
			$strParentids = "'" . implode("','",$parentids) . "'";
		else
			$strParentids = "''";
		
		// let's get all perms, with info about inherited and non-inherited
		
		$sql = "SELECT opm_permgroups.permgroup, opm_perms.*, ugp1.permid AS has_perm_inherited, ugp2.permid AS has_perm
				FROM opm_perms
				LEFT JOIN opm_permgroups ON opm_permgroups.permgroupid = opm_perms.permgroupid
				LEFT JOIN opm_usergroup_perms AS ugp1 ON ugp1.permid = opm_perms.permid AND ugp1.usergroupid IN ($strParentids)
				LEFT JOIN opm_usergroup_perms AS ugp2 ON ugp2.permid = opm_perms.permid AND ugp2.usergroupid = $usergroupid
				ORDER BY opm_permgroups.displayorder,opm_perms.displayorder";
       			
       	$usergroup->permissions = $this->db->query($sql);
       
       
       
       return $usergroup;		
			 	
        
    }
    
    function getUsergroupName($usergroupid)
    {  	
       	       	
       	$sql = "SELECT opm_usergroups.usergroup
       			FROM opm_usergroups 
       			WHERE usergroupid = '".$usergroupid."'";
       	
       	$result = $this->db->query($sql);
       	$row = $result->row();
       	
       	$usergroup = $row->usergroup;
       
       return $usergroup;		
			 	
        
    }
    

	function saveUsergroupPerms($id, $perms, $checked,$usergroup) {
	
		// save usergroup name
		
		$sql = "UPDATE opm_usergroups SET usergroup = ".$this->db->escape($usergroup)." WHERE usergroupid = " . $this->db->escape($id); 
			
		$this->db->query($sql);
		
		
	
		// save usergroup permissions per usergroupid
		
		if(is_array($perms)) {
		
			$permlist = array_keys($perms);
			$permlist = implode(", ", $permlist);
			
			$inserts = "";
			
			if(is_array($checked)) {
			
				$chks = array_keys($checked);
				foreach($chks as $c) {
				
					$inserts .= "(".$id.", ".$c."), ";
				
				}
				
				$inserts = substr($inserts, 0, -2);
			
			}
			
			// delete $perms
	
			$sql = "DELETE
					FROM opm_usergroup_perms
					WHERE usergroupid = '".$id."'
					AND permid IN (".$permlist.")";
			
			$this->db->query($sql);
	
			
			// insert $checked
			
			$sql = "INSERT INTO opm_usergroup_perms (usergroupid, permid)
					VALUES ".$inserts;
			
			if(is_array($checked))
				$this->db->query($sql);
		
		}
	
	}
	
	function addUsergroup($usergroup,$parentusergroupid) {
	
		$sql = "INSERT INTO opm_usergroups (usergroup,parentid)
				VALUES (".$this->db->escape($usergroup).",".$this->db->escape($parentusergroupid).")";
				
		if($this->db->query($sql))
			return true;
		else
			return false;

	
	}
	
	
	function saveUsergroupProperties($usergroupID,$propertyIDs) {
	
		//echo "<pre>";
		//print_r($propertyIDs);
    	//die();
	
		// first delete old assignments
	
		$sql = "DELETE FROM opm_usergroup_properties WHERE usergroupid = " . $this->db->escape($usergroupID);
		
		$this->db->query($sql);
		
		// now insert new!	
		
		if (is_array($propertyIDs)) {
		
			foreach($propertyIDs as $propid) {
			
				$sql = "INSERT INTO opm_usergroup_properties (usergroupid,propertyid)
						VALUES(".$this->db->escape($usergroupID).",".$this->db->escape($propid).")";
						
				$this->db->query($sql);
			
			}
		
		}
				
		return true;

	
	}
	
	function fetchLicensees($inc_inactive = false, $opm_productid = null) {
    
    	// if we are searching for a ugid, we must also include children in the search!
    	
    	$licusergroupid = $this->config->item('licenseeGroupID');
    	$query_where = "";
    	
    	if ($inc_inactive) {
    		
    		$query_where = " ";
    		
    	} else { 

    		// if we are querying for a product, we need to include assigned licensees
    		
    		/*if ($opm_productid) 
    			$query_where .= "OR opm_products_designers.lineid IS NOT NULL )";
    		else
    			$query_where .= ")";
    	*/
    	}
    	
    	$sql = "SELECT opm_usergroups.*,opm_products_licensees.lineid AS isassigned
    			FROM opm_usergroups
    			LEFT JOIN opm_products_licensees ON opm_products_licensees.usergroupid = opm_usergroups.usergroupid ";
    			
    			if ($opm_productid || $opm_productid == 0)
    				$sql .=" AND opm_products_licensees.opm_productid = '".$opm_productid."' ";
    			
    			$sql .= "
    			WHERE ( opm_usergroups.parentid = ".$licusergroupid.")
    			$query_where
    			GROUP BY opm_usergroups.usergroupid
    			ORDER BY opm_usergroups.usergroup";
 
 		//echo $sql;
 
    	$query = $this->db->query($sql);
    	return $query;

    
    }
	

}

?>