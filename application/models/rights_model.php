<?

class Rights_model extends CI_Model {

    function RightsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchRights($opm_productid = 0) {
    
    	// first we need property id for that product.
    	
    	if ($opm_productid != 0) {
    	
	    	$sql = "SELECT propertyid FROM opm_products WHERE opm_productid = " . $opm_productid;
	    	$query = $this->db->query($sql);
	    	$propertyid = $query->row()->propertyid;
    	
    	} else {
    	
    		$propertyid = 0;
    	
    	}
    
		$sql = "SELECT opm_rights.id, opm_rights.right,
				opm_products_rights.id AS isassigned,
				opm_products_rights.isexception AS isexception,
				opm_property_rights.id AS isdefault
				FROM opm_rights
				LEFT JOIN opm_products_rights ON opm_rights.id = opm_products_rights.rightid AND opm_products_rights.opm_productid = ".$opm_productid . "
				LEFT JOIN opm_property_rights ON opm_rights.id = opm_property_rights.rightid AND opm_property_rights.propertyid = ".$propertyid."
				GROUP BY opm_rights.id";
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
					
			$rights[$x]['rightid'] = $data->id;
			$rights[$x]['right'] = $data->right;
			$rights[$x]['isexception'] = false;
			
			if ($opm_productid != 0) {
			
				if(isset($data->isassigned)) {
					$rights[$x]['isassigned'] = true; }
				else {
					$rights[$x]['isassigned'] = false; }
					
				if(isset($data->isdefault)) {
					$rights[$x]['isdefault'] = true; }
				else {
					$rights[$x]['isdefault'] = false; }
					
				if ($data->isexception) {
				
					$rights[$x]['isexception'] = true;
					$rights[$x]['isassigned'] = false;
			
				}
				
			}
				
			$x++;
					
		}

		
		return($rights);
    
    }
    
    
    function fetchPropertyRights($propertyid) {
    
		$sql = "SELECT opm_rights.id, opm_rights.right, opm_property_rights.id AS isassigned
				FROM opm_rights
				LEFT JOIN opm_property_rights
				ON opm_rights.id = opm_property_rights.rightid
				AND opm_property_rights.propertyid = ".$propertyid;
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
					
			$rights[$x]['rightid'] = $data->id;
			$rights[$x]['right'] = $data->right;
			
			if(isset($data->isassigned)) {
				$rights[$x]['isassigned'] = true; }
			else {
				$rights[$x]['isassigned'] = false; }
				
			$x++;
					
		}
		
		return($rights);
    
    }
    
	function fetchRight($id)
    {
    
		$sql = "SELECT opm_rights.* FROM opm_rights WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("right Not Found!");
       		return false;
       	
       	}
        
    }
    
    function createException($opm_productid,$rightid) {
    
    
    	// first delete any records from product_terr table where productid + terrid are present
    	
    	$sql = "DELETE FROM opm_products_rights WHERE opm_productid = " . $this->db->escape($opm_productid) . " AND rightid = " . $this->db->escape($rightid);
       	$query = $this->db->query($sql);
    	
    	
    	// then create exception record
    	
    	$sql = "INSERT INTO opm_products_rights (opm_productid,rightid,isexception) 
    			VALUES(" . $this->db->escape($opm_productid) . "," . $this->db->escape($rightid) . ",1)";
       	
       	if ($query = $this->db->query($sql))
       		return true;
       	else
       		return false;
    
    }
    
    function cancelException($opm_productid,$rightid) {
   
    	//  delete any records from product_terr table where productid + terrid are present
    	
    	$sql = "DELETE FROM opm_products_rights WHERE opm_productid = " . $this->db->escape($opm_productid) . " AND rightid = " . $this->db->escape($rightid);
       	$query = $this->db->query($sql);
    
       	
       	if ($query = $this->db->query($sql))
       		return true;
       	else
       		return false;
    
    }
    
    
}

?>
