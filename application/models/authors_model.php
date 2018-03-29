<?

class Authors_model extends CI_Model {

    function AuthorsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchAuthors() {
    
		$sql = "SELECT opm_asset_authors.*
				FROM opm_asset_authors";
				
		$sql .=" GROUP BY opm_asset_authors.id
				ORDER BY opm_asset_authors.author";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
       
    function fetchAuthor($id) {
    
		$sql = "SELECT opm_asset_authors.* FROM opm_asset_authors WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Author Not Found!");
       		return false;
       	
       	}
        
    }

}
//
?>