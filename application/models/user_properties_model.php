<?

class User_Properties_model extends CI_Model {

    function User_Properties_Model()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchApprovalProperties($userid) {
    	
    
    	// first we must query to get the total # of results
    
    	$sql = "SELECT * FROM opm_user_app_properties
    			LEFT JOIN properties ON properties.propertyid = opm_user_app_properties.propertyid
    			WHERE opm_user_app_properties.userid = '".$userid."'
    			ORDER BY properties.property
    			";

    	$query = $this->db->query($sql);

    	return $query;
    	
    
    }
    
     function addApprovalProperty($postdata) {
     
     	// make sure property exists!
     	
     	$sql = "SELECT * FROM properties WHERE propertyid = '".$postdata['propertyid']."'";
     	$query = $this->db->query($sql);
     	
     	if ($query->num_rows == 0)
     		return false;
     
     	// make sure property isn't associated already!
     	
     	$sql = "SELECT * FROM opm_user_app_properties WHERE propertyid = '".$postdata['propertyid']."' AND userid = '".$postdata['userid']."'";
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() == 0) {
    
			// first we must query to get the total # of results
		
			$sql = "INSERT INTO opm_user_app_properties (userid,propertyid,createdby,createdate)
					VALUES ('".$postdata['userid']."','".$postdata['propertyid']."','".$this->userinfo->userid."',".mktime().")
					";
	
			$query = $this->db->query($sql);
			return true;
			
    	
    	} else {
    	
    		return false;
    	
    	}
    	
    
    }
    
    function saveApprovalProperties($postdata) {
     
     	//print_r($postdata);
     	//exit();
     	
     	foreach ($postdata['arrLineIDs'] as $lineid => $x) {
     	
     		// convert mm-dd-yyyy dates to timestamps for db insertion
     		
     		
     		
     		if ($postdata['begindate'][$lineid]) {
     		
     			$arrBeginDate = explode("-",$postdata['begindate'][$lineid]);
     			$tsBeginDate = mktime(0, 0, 0, $arrBeginDate[0], $arrBeginDate[1], $arrBeginDate[2]);
     		
     		} else {
     		
     			$tsBeginDate = 0;
     		
     		}	
     		
     		if ($postdata['enddate'][$lineid]) {
     		
     			$arrEndDate = explode("-",$postdata['enddate'][$lineid]);
     			$tsEndDate = mktime(0, 0, 0, $arrEndDate[0], $arrEndDate[1], $arrEndDate[2]);
     		
     		} else {
     		
     			$tsEndDate = 0;
     		}
     		
     		// handle checkbox
     		
     		if ($postdata['approvalrequired'][$lineid])
     			$approvalreq = 1;
     		else
     			$approvalreq = 0;
     		
			$sql = "UPDATE opm_user_app_properties
					SET approvalrequired = '".$approvalreq."',
					begindate = '".$tsBeginDate."',
					enddate = '".$tsEndDate."'
					WHERE lineid = '".$lineid."'
					";
					
			$query = $this->db->query($sql);
     	
     	}
     	
     	return true;
    
    }


}

?>