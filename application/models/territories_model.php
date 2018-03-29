<?

class Territories_model extends CI_Model {

    function TerritoriesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchTerritories($opm_productid = 0) {
    
    	// first we need property id for that product.
    	
    	if ($opm_productid != 0) {
    	
	    	$sql = "SELECT propertyid FROM opm_products WHERE opm_productid = " . $opm_productid;
	    	$query = $this->db->query($sql);
	    	$propertyid = $query->row()->propertyid;
    	
    	} else {
    	
    		$propertyid = 0;
    	
    	}
    
		$sql = "SELECT opm_territories.id, opm_territories.territory,
				opm_products_territories.id AS isassigned,
				opm_products_territories.isexception AS isexception,
				opm_property_territories.id AS isdefault
				FROM opm_territories
				LEFT JOIN opm_products_territories ON opm_territories.id = opm_products_territories.territoryid AND opm_products_territories.opm_productid = ".$opm_productid . "
				LEFT JOIN opm_property_territories ON opm_territories.id = opm_property_territories.territoryid AND opm_property_territories.propertyid = ".$propertyid."
				GROUP BY opm_territories.id";
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
					
			$territories[$x]['territoryid'] = $data->id;
			$territories[$x]['territory'] = $data->territory;
			$territories[$x]['isexception'] = false;
			
			if ($opm_productid != 0) {
			
				if(isset($data->isassigned)) {
					$territories[$x]['isassigned'] = true; }
				else {
					$territories[$x]['isassigned'] = false; }
					
				if(isset($data->isdefault)) {
					$territories[$x]['isdefault'] = true; }
				else {
					$territories[$x]['isdefault'] = false; }
					
				if ($data->isexception) {
				
					$territories[$x]['isexception'] = true;
					$territories[$x]['isassigned'] = false;
			
				}
				
			}
				
			$x++;
					
		}

		
		return($territories);
    
    }
    
    
    function fetchPropertyTerritories($propertyid) {
    
		$sql = "SELECT opm_territories.id, opm_territories.territory, opm_property_territories.id AS isassigned
				FROM opm_territories
				LEFT JOIN opm_property_territories
				ON opm_territories.id = opm_property_territories.territoryid
				AND opm_property_territories.propertyid = ".$propertyid;
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
					
			$territories[$x]['territoryid'] = $data->id;
			$territories[$x]['territory'] = $data->territory;
			
			if(isset($data->isassigned)) {
				$territories[$x]['isassigned'] = true; }
			else {
				$territories[$x]['isassigned'] = false; }
				
			$x++;
					
		}
		
		return($territories);
    
    }
    
    function fetchOfficeTerritories($officeid) {
    
    	$CI =& get_instance();
		$CI->load->model('offices_model');
    	    
    	// need to get office parent info... to find out about inherited terrrrritories.
		
		$parentids = $CI->offices_model->getParents($officeid);
		
		if (is_array($parentids))
			$strParentids = "'" . implode("','",$parentids) . "'";
		else
			$strParentids = "''";
			    
    
		$sql = "SELECT opm_territories.id, opm_territories.territory, opm_office_territories.id AS isassigned, oot2.id AS isinherited
				FROM opm_territories
				LEFT JOIN opm_office_territories
				ON opm_territories.id = opm_office_territories.territoryid AND opm_office_territories.officeid = ".$this->db->escape($officeid)."
				LEFT JOIN opm_office_territories AS oot2 ON oot2.territoryid = opm_territories.id AND oot2.officeid IN (".$strParentids.")
				GROUP BY opm_territories.id
				";
		
		//echo $sql . "<br><br>";
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
			
			//echo "<pre>";
			//print_r($data);
					
			$territories[$x]['territoryid'] = $data->id;
			$territories[$x]['territory'] = $data->territory;
			
			if(isset($data->isassigned)) {
				$territories[$x]['isassigned'] = true; }
			else {
				$territories[$x]['isassigned'] = false; }
				
			if(isset($data->isinherited)) {
				$territories[$x]['isinherited'] = true; }
			else {
				$territories[$x]['isinherited'] = false; }
				
			$x++;
					
		}
		
		/*echo "<pre>";
		print_r($territories);
		die();*/
		
		return($territories);
    
    }
    
	function fetchTerritory($id)
    {
    
		$sql = "SELECT opm_territories.* FROM opm_territories WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Territory Not Found!");
       		return false;
       	
       	}
        
    }
    
    function createException($opm_productid,$territoryid) {
    
    
    	// first delete any records from product_terr table where productid + terrid are present
    	
    	$sql = "DELETE FROM opm_products_territories WHERE opm_productid = " . $this->db->escape($opm_productid) . " AND territoryid = " . $this->db->escape($territoryid);
       	$query = $this->db->query($sql);
    	
    	
    	// then create exception record
    	
    	$sql = "INSERT INTO opm_products_territories (opm_productid,territoryid,isexception) 
    			VALUES(" . $this->db->escape($opm_productid) . "," . $this->db->escape($territoryid) . ",1)";
       	
       	if ($query = $this->db->query($sql))
       		return true;
       	else
       		return false;
    
    }
    
    function cancelException($opm_productid,$territoryid) {
   
    	//  delete any records from product_terr table where productid + terrid are present
    	
    	$sql = "DELETE FROM opm_products_territories WHERE opm_productid = " . $this->db->escape($opm_productid) . " AND territoryid = " . $this->db->escape($territoryid);
       	$query = $this->db->query($sql);
    
       	
       	if ($query = $this->db->query($sql))
       		return true;
       	else
       		return false;
    
    }
    
    
}

?>
