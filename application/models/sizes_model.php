<?

class Sizes_model extends CI_Model {

    function SizesModel()
    { 
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchSizes($opm_productid = null)
    {
    	
    	if ($opm_productid)
    		$sql = "SELECT opm_sizes.*,opm_products_sizes.id AS ischecked";
    	else
    		$sql = "SELECT opm_sizes.*";
    
		$sql .= " FROM opm_sizes ";
		
		if ($opm_productid) {
		
			$sql .= " LEFT JOIN opm_products_sizes ON opm_products_sizes.sizeid = opm_sizes.id AND opm_products_sizes.opm_productid = " . $this->db->escape($opm_productid);
		
		}

		$sql .=" ORDER BY opm_sizes.id";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchSize($id)
    {
    
		$sql = "SELECT opm_sizes.* FROM opm_sizes WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Size Not Found!");
       		return false;
       	
       	}
        
    }
    
    function saveSizes($opm_productid,$sizes) {
    
    	// first delete all sizes
    	
    	if ($opm_productid) {
    	
    		$sql = "DELETE FROM opm_products_sizes WHERE opm_productid = " . $this->db->escape($opm_productid);
    		$this->db->query($sql);
    		
    		foreach ($sizes as $sizeid=>$onoff) {
    			
    			$sql = "INSERT INTO opm_products_sizes (opm_productid,sizeid) VALUES (".$this->db->escape($opm_productid).",".$this->db->escape($sizeid).")";
    			$this->db->query($sql);
    		
    		}
    		
    		return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    
    }

}

?>