<?

class Offices_model extends CI_Model {

    function OfficesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    
    function fetchOffices() // options allow for pulling usergroups w/ associations
    {
    
    	// declare vars
    	
    	$query_join = "";
    	$query_select = "";
		
		// build array of usergroups, parent child style  
    	
    	$refs = array();
		$list = array();

		$sql = "SELECT opm_offices.id, opm_offices.parentid, opm_offices.office
				$query_select
				FROM opm_offices 
				$query_join
				ORDER BY opm_offices.office";
				
				
		$query = $this->db->query($sql);
		
		// the code below puts the groups into a neat parent/child array
		
		foreach ($query->result() as $data) {
			$thisref = &$refs[ $data->id ];
			
			$thisref['id'] = $data->id;
			$thisref['parentid'] = $data->parentid;
			$thisref['office'] = $data->office;
			
			//if(isset($data->isassigned))
			//	$thisref['isassigned'] = true;
		
			if ($data->parentid == 0) {
				$list[ $data->id ] = &$thisref;
			} else {
				$refs[ $data->parentid ]['children'][ $data->id ] = &$thisref;
			}
		}
		
		return $list;
		 	
        
    }
    
    
    function getParents($officeid) { // returns array of parents for a given officeid
    	
    	$parentids = array();
    
    	$sql = "SELECT O2.id AS parent1,O3.id AS parent2,O4.id AS parent3,O5.id AS parent4
				FROM opm_offices AS O1
				LEFT JOIN opm_offices AS O2
					ON O2.id = O1.parentid
				LEFT JOIN opm_offices AS O3
					ON O3.id = O2.parentid
				LEFT JOIN opm_offices AS O4
					ON O4.id = O3.parentid
				LEFT JOIN opm_offices AS O5
					ON O5.id = O4.parentid
				WHERE O1.id = '".$officeid."'";
				
		$query = $this->db->query($sql);
		
		
		$row = $query->result_array();
					
		for ($x=1;$x<=4;$x++) {
		
			if (isset($row[0]['parent'.$x]))
				$parentids[] = $row[0]['parent'.$x];
		
		}
		

		return $parentids;


    }
    
    function getChildren($officeid) { // returns array of children for a given id
    	
    	global $arrChildIDs;
    	
    	$arrChildIDs = array();
    	
    	if ($id != 0) // this gets rid of unintentional behavior when ugid is set to 0 (which means none, not parent of all!)
    		$this->fetchChildren($officeid);
    	
    	return $arrChildIDs;
    
    }
    
   function fetchChildren($officeid, $level = 0) {
   
		global $arrChildIDs;
   		
	   // retrieve all children of $parent
	   
	 	$sql = "SELECT opm_offices,id FROM opm_offices WHERE parentid = '".$officeid."'";
		$query = $this->db->query($sql);

	   foreach ($query->result() as $row) {
		   $arrChildIDs[] = $row->id;	
		   $this->fetchChildren($row->id, $level+1);
	   }
	    
	}
	
    
    function fetchOffice($officeid) {  	
       	       	
       	$sql = "SELECT opm_offices.*
       			FROM opm_offices 
       			WHERE id = ".$this->db->escape($officeid);
		
		$query = $this->db->query($sql);
		$office = $query->row();
		
		// get ids of parents!
		
		$parentids = $this->getParents($officeid);
		
		if (is_array($parentids))
			$strParentids = "'" . implode("','",$parentids) . "'";
		else
			$strParentids = "''";
		
       
       
       
       return $office;		
			 	
        
    }
    
    function getOfficeName($officeid)
    {  	
       	       	
       	$sql = "SELECT opm_offices.usergroup
       			FROM opm_offices 
       			WHERE id = '".$this->db->escape($officeid)."'";
       	
       	$result = $this->db->query($sql);
       	$row = $result->row();
       	
       	$office = $row->office;
       
       return $office;		
			 	
        
    }
    

	
	
	function addOffice($office,$parentid) {
	
		$sql = "INSERT INTO opm_offices (office,parentid)
				VALUES (".$this->db->escape($office).",".$this->db->escape($parentid).")";
				
		if($this->db->query($sql))
			return true;
		else
			return false;

	
	}
	
	function saveOffice($officeid,$postdata) {
	
		$sql = "INSERT INTO opm_offices (office,parentid)
				VALUES (".$this->db->escape($office).",".$this->db->escape($parentid).")";
				
		if($this->db->query($sql))
			return true;
		else
			return false;

	
	}
	

}

?>