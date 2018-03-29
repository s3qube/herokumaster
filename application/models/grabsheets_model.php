<?

class Grabsheets_model extends CI_Model {

    function GrabsheetsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchGrabsheets($returnTotal = false, $offset = 0, $perPage = null, $propertyid = null, $grabsheetgroupid = null, $grabsheettemplateid = null) {
    
    
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT opm_grabsheet.*, opm_grabsheet_templates.*";
    	else 
    		$sql = "SELECT opm_grabsheet.grabsheetid";

    			
    	$sql .=	" FROM opm_grabsheet 
    			  LEFT JOIN opm_grabsheet_groups ON opm_grabsheet_groups.grabsheetgroupid = opm_grabsheet.grabsheetgroupid
    			  LEFT JOIN opm_grabsheet_templates ON opm_grabsheet_templates.grabsheettemplateid = opm_grabsheet.grabsheettemplateid
    			  LEFT JOIN opm_grabsheet_detail ON opm_grabsheet_detail.grabsheetid = opm_grabsheet.grabsheetid
    			  LEFT JOIN opm_products ON opm_products.opm_productid = opm_grabsheet_detail.opm_productid";
    			
    	$sql .= " WHERE opm_grabsheet.grabsheetid <> 0 ";
    	
    	if ($propertyid)
    		$sql .= " AND opm_products.propertyid = '".$propertyid."'";
    		
    	if ($grabsheetgroupid)
    		$sql .= " AND opm_grabsheet.grabsheetgroupid = '".$grabsheetgroupid."'";
    		
    	if ($grabsheettemplateid)
    		$sql .= " AND opm_grabsheet.grabsheettemplateid = '".$grabsheettemplateid."'";
    			
    	$sql .= " GROUP BY opm_grabsheet.grabsheetid";
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	
    	$query = $this->db->query($sql);
    	
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
	function fetchGrabsheetInfo($id) {
    
    	$sql = "SELECT * FROM opm_grabsheet WHERE grabsheetid = " . $this->db->escape($id);
    	
    	$query = $this->db->query($sql);
    	
    	if (!$grabsheet = $query->row())
    		return false;
    	
    	$sql = "SELECT opm_grabsheet_detail.*, opm_products.productname, properties.property, opm_approvalstatuses.approvalstatus
    			FROM opm_grabsheet_detail
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_grabsheet_detail.opm_productid
    			LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_products.approvalstatusid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			WHERE grabsheetid =  " . $this->db->escape($id) . "
    			ORDER BY opm_grabsheet_detail.displayorder";
    	
    	$query = $this->db->query($sql);    	
    	
    	$arrTemp = array();
    	
    	foreach ($query->result() as $row)
    		$arrTemp[] = $row;
    			
    	$grabsheet->items = $arrTemp;
    	
        return $grabsheet;
    
    }
    
    function fetchGrabsheetGroups()
    {
    
		$sql = "SELECT opm_grabsheet_groups.*
				FROM opm_grabsheet_groups
				ORDER BY opm_grabsheet_groups.grabsheetgroup";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchGrabsheetTemplates()
    {
    
		$sql = "SELECT opm_grabsheet_templates.*
				FROM opm_grabsheet_templates
				ORDER BY opm_grabsheet_templates.displayorder";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function saveGrabsheet($postdata) {
    
    	if (!$postdata['grabsheetid']) { // we are in add mode
    	
    		 $sql = "INSERT INTO opm_grabsheet (grabsheetgroupid,grabsheettemplateid,headerimageid,grabsheettitle,property_imageid,createdby,createdate)
    	 		VALUES (".$this->db->escape($postdata['grabsheetgroupid']).",".$this->db->escape($postdata['grabsheettemplateid']).",".$this->db->escape($postdata['headerimageid']).",".$this->db->escape($postdata['title']).",".$this->db->escape($postdata['property_imageid']).",".$this->userinfo->userid.",".mktime().")";
    	 		
    	 	$this->db->query($sql);
    	 	$grabsheetid = $this->db->insert_id();
    	
    	} else { //  edit mode , update
    	
    		$sql ="	UPDATE opm_grabsheet
    				
    				SET grabsheetgroupid = ".$this->db->escape($postdata['grabsheetgroupid']).",
    				grabsheettemplateid = ".$this->db->escape($postdata['grabsheettemplateid']).",
    				headerimageid = ".$this->db->escape($postdata['headerimageid']).",
    				grabsheettitle = ".$this->db->escape($postdata['title']).",
    				showproductcodes = ".$this->db->escape($postdata['showProductCodes']).",
    				property_imageid = ".$this->db->escape($postdata['property_imageid'])."
    				
    				WHERE grabsheetid = " . $this->db->escape($postdata['grabsheetid']);
    		
    		$this->db->query($sql);
    		
    		// now clear out detail
    		
    		$sql = "DELETE FROM opm_grabsheet_detail WHERE grabsheetid = " . $this->db->escape($postdata['grabsheetid']);
    		$this->db->query($sql);
    		
    		$grabsheetid = $postdata['grabsheetid'];
    	
    	}

    	 
    	if ($grabsheetid) {
		 	// save grabsheet detail!
		 	
		 	$arrImageIDs = explode("|",$postdata['itemids']);
		 	$displayorder = 10;
		 	
		 	// get all opm_productids for images!
		 	
		 	$arrImageIDs = array_filter($arrImageIDs);
		 	
		 	$strImageIDs = implode(",",$arrImageIDs);
		 	
		 	// trim last comma (fun!)
		 	
		 	//$strImageIDs = substr(string string, int start,strlen($strImageIDs)-1);
		 	
		 	$sql = "SELECT imageid,opm_productid FROM opm_images WHERE opm_images.imageid IN ($strImageIDs)";
		 	$query = $this->db->query($sql);
		 	
		 	foreach ($query->result() as $row)
		 		$arrProductIDs[$row->imageid] = $row->opm_productid; 
		 		
		 	// put the shit in the DB
		 	
		 	foreach ($arrImageIDs as $imageid) {
		 	
		 		if (isset($arrProductIDs[$imageid])) {
		 	
			 		if ($this->input->post('gsItemComment_'.$imageid)) {
			 		
			 			$itemComment = $this->input->post('gsItemComment_'.$imageid);
			 		
			 		} else {
			 		
			 			$itemComment = "";
			 		
			 		}
			 		
			 	
			 		$sql = "INSERT INTO opm_grabsheet_detail (grabsheetid,opm_productid,imageid,comment,displayorder)
			 				VALUES ('".$grabsheetid."','".$arrProductIDs[$imageid]."','".$imageid."',".$this->db->escape($itemComment).",'".$displayorder."')";
			 				
			 		$this->db->query($sql);
			 		
			 		$displayorder += 10;
		 		
		 		}
		 	
		 	}
		 	
		 	
		 	return $grabsheetid;
		 
		 } else {
		 
		 	return false;
		 
		 }


    }
    
    function createGroup($groupName) {
    
    	// first get last display order, and put this one 10 ahead of it!
    	
    	$sql = "SELECT displayorder FROM opm_grabsheet_groups ORDER BY displayorder DESC LIMIT 1";
    	$query = $this->db->query($sql);
    	$row = $query->row();
    	$displayorder = $row->displayorder + 10;
    	
    
    	 $sql = "INSERT INTO opm_grabsheet_groups (grabsheetgroup,displayorder)
    	 		VALUES (".$this->db->escape($groupName).",".$this->db->escape($displayorder).")";
    	 		
    	 if ($this->db->query($sql)) {
    	 	
    	 	return true;
    	 
    	 } else {
    	 
    	 	return false;
    	 	
    	 }

    }
    
    function setGrabToFile($grabsheetid) {
    	
    	$sql ="	UPDATE opm_grabsheet
    				
				SET isfile = 1
				
				WHERE grabsheetid = " . $this->db->escape($grabsheetid);
		
		if ($this->db->query($sql))
			return true;
		else
			return false;
    
    }
    
    
    function fetchHeaderImages()
    {
    
		$sql = "SELECT opm_grabsheet_headerimages.*
				FROM opm_grabsheet_headerimages
				ORDER BY opm_grabsheet_headerimages.id";
        	
        $query = $this->db->query($sql);
        return $query;
    }

}

?>