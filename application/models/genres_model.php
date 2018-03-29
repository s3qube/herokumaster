<?

class Genres_model extends CI_Model {

    function GenresModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchGenres($returnTotal = false, $offset = 0, $perPage = null, $propertyid = 0) {
    
		$sql = "SELECT opm_genres.*
				FROM opm_genres
				";
				
		if ($propertyid) {
			
			$sql .= "LEFT JOIN opm_property_genres ON opm_property_genres.genreid = opm_genres.id ";
			
		}
		
		$sql .= " WHERE opm_genres.id <> 0  ";
		
		if ($propertyid) {
			
			$sql .= " AND opm_property_genres.propertyid = " . $this->db->escape($propertyid);
			
		}
		
		

		$sql .= " 
				ORDER BY opm_genres.genre";
        	
       
        $query = $this->db->query($sql);
        
        
        if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
        
    }
    
    
    function fetchGenre($id) {
    
		$sql = "SELECT opm_genres.* FROM opm_genres WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Genre Not Found!");
       		return false;
       	
       	}
        
    }

	
	
	
	function saveGenreInfo($arrPostData) {
         	
		if (isset($arrPostData['genreid'])) { // we have a genreid, update
     	
     		$sql = "UPDATE opm_genres
     				SET genre = ". $this->db->escape($arrPostData['genre']) .",
     				parentid = " . $this->db->escape($arrPostData['parentgenreid']);
     							
     				
     		$sql .= " WHERE genreid = '".$arrPostData['genreid']."'";
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['genreid'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new genre, insert
     	
     		
     		$sql = "INSERT INTO opm_genres (parentid,genre,isactive)
    			VALUES (".$this->db->escape($arrPostData['parentgenreid']).",".$this->db->escape($arrPostData['genre']).",'Y')";
       		
       		if ($query = $this->db->query($sql)) {
       		
       			return $this->db->insert_id(); 
       		
       		} else {
       		
       			return false;
       		
       		}
       		
     	
     	}
    
    }
	

}
//
?>