<?

class Forum_model extends CI_Model {

    function ForumModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchForumEntries($opm_productid,$limit = 0,$offset = 0)
    {
    
		$sql = "SELECT opm_forum.*
				FROM opm_forum
				WHERE opm_forum.opm_productid = '".$opm_productid."'
				ORDER BY timestamp";
				
		if ($limit) {
		
			$sql .= " LIMIT $offset, $limit";
		
		}
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    
    function fetchLatestForumEntry($opm_productid)
    {
    
		$sql = "SELECT opm_forum.*
				FROM opm_forum
				WHERE opm_forum.opm_productid = '".$opm_productid."'
				ORDER BY timestamp DESC
				LIMIT 1";
        	
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    function addForumEntry($opm_productid,$userid,$subject,$body)
    {
    	
    	// get name of poster
    	
    	$sql = "SELECT username FROM users WHERE userid = '".$userid."'";
    	$result = $this->db->query($sql);
    	$row = $result->row();

		
		$sql = "INSERT INTO opm_forum (opm_productid,userid,postname,posttitle,post,timestamp)
				VALUES (".$this->db->escape($opm_productid).",".$this->db->escape($userid).",".$this->db->escape($row->username).",".$this->db->escape($subject).",".$this->db->escape($body).",".mktime().")";
		
		
		if($this->db->query($sql))
			return true;
		else
			return false;
				
				
	}
    
   
}

?>