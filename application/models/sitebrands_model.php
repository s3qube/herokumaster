<?

class Sitebrands_model extends CI_Model {

    function SitebrandsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchSitebrands() {
    
		$sql = "SELECT opm_ws_sitebrands.*
				FROM opm_ws_sitebrands
				";

		$sql .= " 
				ORDER BY opm_ws_sitebrands.sitebrand";
        	
       
        $query = $this->db->query($sql);
        
    	return $query;
        
    }
    
    
    function fetchSitebrand($id) {
    
		$sql = "SELECT opm_ws_sitebrands.* FROM opm_ws_sitebrands WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Site Brand Not Found!");
       		return false;
       	
       	}
        
    }

	

}
//
?>