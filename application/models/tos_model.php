<?

class Tos_model extends CI_Model {

    function TosModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
	function fetchTosList($returnTotal = false, $offset = 0, $perPage = null, $usergroupID = null, $includeParents = null, $showOnlyActive = null, $orderby = null) {
		
		/*if ($usergroupid) {
    	
    		$CI =& get_instance();
			$CI->load->model('usergroups_model');
			$childrenIDs = $CI->usergroups_model->getChildren($usergroupid);
			$childrenIDs[] = $usergroupid;

			$strUsergroupIDs = implode(",",$childrenIDs);
				
    	}*/
    
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT opm_tos.*, opm_usergroups.usergroup ";
    	else 
    		$sql = "SELECT opm_tos.id";

    			
    	$sql .=	" FROM opm_tos 
    			  LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = opm_tos.usergroupid
    			  ";
    	
    			
    	$sql .= " WHERE opm_tos.id <> 0 ";
    	
    	if ($usergroupID)
    		$sql .= " AND opm_tos.usergroupid = '" . $usergroupID . "'";
    	
    	if ($orderby)
    		$sql .=" ORDER BY $orderby";
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";

    	
    	$query = $this->db->query($sql);

    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    
    function fetchTOS($id) {
    
		$sql = "SELECT opm_tos.* FROM opm_tos WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("TOS Not Found!");
       		return false;
       	
       	}
        
    }

	function saveTOS($arrPostData) {
	
		/*echo "<pre>";
		print_r($arrPostData);
		die();*/
		
		// deal w/ isactive checkbox
     		
 		if ($arrPostData['isactive'] == 'on')
 			$arrPostData['isactive'] = 1;
 		else
 			$arrPostData['isactive'] = 0;
         	
		if (isset($arrPostData['id'])) { // we have a tosid, update
     	
     		$sql = "UPDATE opm_tos
     				SET usergroupid = ". $this->db->escape($arrPostData['usergroupid']). ",
     				tosname = ". $this->db->escape($arrPostData['tosname']). ",
     				tostext = ". $this->db->escape($arrPostData['tostext']). ",
     				isactive = ". $this->db->escape($arrPostData['isactive']). ",
     				effectivedate = ". $this->db->escape($arrPostData['effectivedate']);
     				
     		$sql .= " WHERE id = " . $this->db->escape($arrPostData['id']);
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['id'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new tos, insert
     	
     		
     		$sql = "INSERT INTO opm_tos (usergroupid,tosname,tostext,effectivedate,isactive,createdby,datecreated)
    			VALUES (".$this->db->escape($arrPostData['usergroupid']).",".$this->db->escape($arrPostData['tosname']).",".$this->db->escape($arrPostData['tostext']).",".$this->db->escape($arrPostData['effectivedate']).",".$this->db->escape($arrPostData['isactive']).",".$this->userinfo->userid.",".mktime().")";
       		
       		if ($query = $this->db->query($sql)) {
       		
       			return $this->db->insert_id(); 
       		
       		} else {
       		
       			return false;
       		
       		}
       		
     	
     	}
    
    }
    
    function fetchNeededTos($userid) { // get tos(es) that a user hasn't agreed to and needs to.
    
    	// first, lets figure out what usergroupids apply to user.
    	
    	$CI =& get_instance();
			
		$CI->load->model('users_model');
    	
    	$user = $CI->users_model->fetchUserInfo($userid);
    	
    	$CI->load->model('usergroups_model');
		$parentids = $CI->usergroups_model->getParents($user->usergroupid);
		$parentids[] = $user->usergroupid;
		
		$parentids[] = 0; // tos(s) with ID 0 apply to all users.
		
		if($user->usergroupid2) {
			
			$parentids2 = $CI->usergroups_model->getParents($user->usergroupid2);
			$parentids2[] = $user->usergroupid2;
			
			$parentids = array_merge($parentids,$parentids2);
		
		}
		
		$ugids = implode(",",$parentids);
    	    	
    	$sql = "
    			
    			SELECT opm_tos.*
    			FROM opm_tos
    			LEFT JOIN opm_tos_agreements ON opm_tos_agreements.tosid = opm_tos.id AND opm_tos_agreements.userid = ".$this->db->escape($user->userid)."
    			WHERE (opm_tos.usergroupid IN (".$ugids.") OR opm_tos.usergroupid = 0)
    			AND opm_tos_agreements.id IS NULL
    			AND opm_tos.isactive = 1
    			AND opm_tos.effectivedate <= " . mktime();
    	
    	/*if ($this->userinfo->userid != 1)
    		$sql = "SELECT opm_tos.* FROM opm_tos WHERE id = 99999";*/
    	
    	$query = $this->db->query($sql);
    	
    	//return 0;
    	return $query;
    
    
    }
    
    function recordAgreements($userid,$tosids) {
    
    	$arrTosIds = explode(",",$tosids);
    
    	foreach ($arrTosIds as $tosid) {
    	
    		$sql = "INSERT INTO opm_tos_agreements (tosid,userid,timestamp)
    				VALUES (".$this->db->escape($tosid).",".$this->db->escape($userid).",".mktime().")";
    				
    		$this->db->query($sql);
    	
    	}
    	
    	return true;
    
    	
    
    }
	

}
//
?>