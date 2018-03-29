<?

class Bodystyles_model extends CI_Model {

    function BodystylesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchBodystyles()
    {
    
		$sql = "SELECT opm_bodystyles.*
				FROM opm_bodystyles ";
				
		$sql .=" GROUP BY opm_bodystyles.id
				ORDER BY opm_bodystyles.bodystyle";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchBodystyle($id)
    {
    
		$sql = "SELECT opm_bodystyles.* FROM opm_bodystyles WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Body Style Not Found!");
       		return false;
       	
       	}
        
    }
	//Globosuck
	
	function fetchFirstLetterLinks($activeLetter = null) { //  get distinct first letters of bodystyle names!
    
    	    
    	$sql = "SELECT DISTINCT LEFT(opm_bodystyles.bodystyle, 1) AS bodystyleFirstLetter FROM opm_bodystyles
    			WHERE isdeleted = 0
    			ORDER BY bodystyle";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $row) {
    		
    		if ($activeLetter == $row->bodystyleFirstLetter)
    			$arrFL[] = "<a href=\"".base_url()."bodystyles/search/0/".$row->bodystyleFirstLetter."\" class=\"redLink\">".$row->bodystyleFirstLetter."</a>&nbsp;&nbsp;";
    		else
    			$arrFL[] = "<a href=\"".base_url()."bodystyles/search/0/".$row->bodystyleFirstLetter."\">".$row->bodystyleFirstLetter."</a>&nbsp;&nbsp;";

    	}
		
		return $arrFL;
		
		}
		
		

	function fetchListBodystyles($inc_inactive = false, $returnTotal = false, $searchText = null, $offset = 0, $perPage = null, $firstLetter = null, $inc_nopermission = false)
    {
    	$query_where = "";
    	$query_join = "";
    	
    	if (!$inc_inactive)
    		$query_where .= " AND opm_bodystyles.isdeleted = 0";
    		
    	/*if (USE_PERMISSION_QUERY) {
    	
    		$query_join .= PERMISSION_QUERY;
    		$query_where .= " AND canview.id IS NOT NULL";
    	
    	}*/
   
    	
    	if ($searchText)
    		$query_where .= " AND opm_bodystyles.bodystyle LIKE '%".$searchText."%'";
    		
    	if ($firstLetter)
    		$query_where .= " AND opm_bodystyles.bodystyle LIKE '".$firstLetter."%'";
    	
		
		$sql = "SELECT opm_bodystyles.*, categories.category
				FROM opm_bodystyles
				LEFT JOIN categories ON categories.categoryid = opm_bodystyles.categoryid
				$query_join
					WHERE opm_bodystyles.id  IS NOT NULL
					$query_where
				GROUP BY opm_bodystyles.id
				ORDER BY opm_bodystyles.bodystyle";

    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	  	
        $query = $this->db->query($sql);
    
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
	
	function saveBodystyle($arrPostData) {
	
         	
		if ($arrPostData['bodystyleid']) { // we have a bodystyleid, update
     	
     		$sql = "UPDATE opm_bodystyles
     				SET bodystyle = ". $this->db->escape($arrPostData['bodystyle']) .",
     				code = ". $this->db->escape($arrPostData['code']) . ",
     				categoryid = ". $this->db->escape($arrPostData['categoryid']);
    
     							
     				
     		$sql .= " WHERE id = '".$arrPostData['bodystyleid']."'";
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['bodystyleid'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new body style, insert
     	
     		
     		$sql = "INSERT INTO opm_bodystyles (bodystyle,code,categoryid)
    			VALUES (".$this->db->escape($arrPostData['bodystyle']).",".$this->db->escape($arrPostData['code']).",".$this->db->escape($arrPostData['categoryid']).")";
       		
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