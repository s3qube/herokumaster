<?

class Colors_model extends CI_Model {

    function ColorsModel()
    { 
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchColors()
    {
    
		$sql = "SELECT opm_colors.*
				FROM opm_colors
				";

		$sql .=" ORDER BY opm_colors.color";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchColor($id)
    {
    
		$sql = "SELECT opm_colors.* FROM opm_colors WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Color Not Found!");
       		return false;
       	
       	}
        
    }
    
    function addColorToProduct($opm_productid,$colorid)
    {
    
		$sql = "INSERT INTO opm_products_colors (opm_productid,colorid)
				VALUES (".$this->db->escape($opm_productid).",".$this->db->escape($colorid).")";
			
		if ($this->db->query($sql))
			return true;
		else
			return false;
        
    }
    
    function removeColorFromProduct($opm_productid,$colorid)
    {
    
		$sql = "DELETE FROM opm_products_colors WHERE opm_productid = ".$this->db->escape($opm_productid)." AND colorid = ".$this->db->escape($colorid);
			
		if ($this->db->query($sql))
			return true;
		else
			return false;
        
    }

}

?>