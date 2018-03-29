<?

class Companies_model extends CI_Model {

    function CompaniesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchCompanies()
    {
    
		$sql = "SELECT opm_companies.*
				FROM opm_companies";
				
		//if (USE_PERMISSION_QUERY)
    	//	$sql .= PERMISSION_QUERY . " WHERE canview.id IS NOT NULL";
				
		$sql .=" GROUP BY opm_companies.id
				ORDER BY opm_companies.name";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchCompany($id)
    {
    
		$sql = "SELECT opm_companies.* FROM opm_companies WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Company Not Found!");
       		return false;
       	
       	}
        
    }

}

?>