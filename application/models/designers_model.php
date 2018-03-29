<?

class Designers_model extends CI_Model {

    function DesignersModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchDesigners($inc_inactive = false, $opm_productid = 0)
    {
    
    	if (!$opm_productid) { // no product id sent, standard query.
    	
    		if (!$inc_inactive)
    			$query_where = " AND users.isactive = 1";
    		else
    			$query_where = '';
    	
			$sql = "SELECT users.*, null as isassigned FROM users
					WHERE users.usergroupid = 3
					$query_where
					ORDER BY users.username
					";
    	
    	} else { // we need to pass info about a particular product - which designers are assigned?
    	
    		
    		if (!$inc_inactive)
    			$query_where = " AND users.isactive = 1 ";
    		else
    			$query_where = "";
    	
			$sql = "SELECT users.*,opm_products_designers.lineid AS isassigned 
					FROM users
					LEFT JOIN opm_products_designers ON opm_products_designers.userid = users.userid AND opm_products_designers.opm_productid = '".$opm_productid."'
					WHERE users.usergroupid = 3
					$query_where
					ORDER BY users.username
					";
    	
    	}
        	
        $query = $this->db->query($sql);

        return $query;
    }

}

?>