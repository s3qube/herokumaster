<?

class Productlines_model extends CI_Model {

    function ProductlinesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchProductLines($propertyid, $inc_inactive = false, $opm_productid = 0)
    {
    
    	if (!$opm_productid) { // no product id sent, standard query.
    	
    		if (!$inc_inactive)
    			$query_where = " AND opm_productlines.isactive = 1";
    		else
    			$query_where = '';
    	
			$sql = "SELECT opm_productlines.*, null as isassigned FROM opm_productlines
					WHERE opm_productlines.propertyid = '".$propertyid."'
					$query_where
					ORDER BY opm_productlines.productline
					";
    	
    	} else { // we need to pass info about a particular product - which pls are assigned?
    	
    		
    		if (!$inc_inactive)
    			$query_where = " AND opm_productlines.isactive = 1 ";
    		else
    			$query_where = "";
    	
			$sql = "SELECT opm_productlines.*,opm_products_productlines.lineid as isassigned 
					FROM opm_productlines
					LEFT JOIN opm_products_productlines ON opm_products_productlines.productlineid = opm_productlines.productlineid AND opm_products_productlines.opm_productid = '".$opm_productid."'
					WHERE opm_productlines.propertyid = '".$propertyid."'
					$query_where
					GROUP BY opm_productlines.productlineid
					ORDER BY opm_productlines.productline
					";
    	
    	}
    	
    	//die($sql);
        	
        $query = $this->db->query($sql);
        
  //      die($sql);
        return $query;
    }
    
    function saveProductLines($postdata) {
    
    	// first, set all product lines to inactive. it is the only way!
    	
    	$sql = "UPDATE opm_productlines SET isactive = 0 WHERE propertyid = " . $this->db->escape($postdata['propertyid']);
    	$this->db->query($sql);
    
    	foreach ($postdata['arrProductLineIDs'] as $plid=>$x) {
    	
    		if (isset($postdata['isactive'][$plid])) {
    		
    			$sql = "UPDATE opm_productlines SET isactive = 1 WHERE productlineid = " . $this->db->escape($plid);
    			$this->db->query($sql);
    			
    		}
    	
    	}
    	
    	return true;
    
    }
    
    function addProductLine($propertyid,$productline) {
    
    	// first, set all product lines to inactive. it is the only way!
    	
    	if ($propertyid && $productline) {
    	
    		$sql = "INSERT INTO opm_productlines (propertyid,productline,isactive) 
    		VALUES (".$this->db->escape($propertyid).",".$this->db->escape($productline).",1)";
     
    		if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    	
    	} else {
    		return false;
    	} 
    
    }
    
   /* function deleteProductLine($productlineid) {

    	if ($productlineid) {
    	
            $sql="DELETE FROM opm_productlines WHERE productlineid = " . $this->db->escape($productlineid);
    	
    		if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    	
    	} else {
    	
    		return false;
    	
    	} 
    
    }*/
	
	function fetchProductLine($productlineid) {
    
		$sql = "SELECT productlineid,productline,propertyid from opm_productlines where productlineid=".$this->db->escape($productlineid)."";
    	$query = $this->db->query($sql);
        
        return $query;	
    
    }

	function updateProductLine($productlineid,$productline) {
               
		if ($productlineid && $productline) {
    		
			$sql = "UPDATE opm_productlines SET productline = " . $this->db->escape($productline) . " WHERE productlineid = " . $this->db->escape($productlineid);
     
    		if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    	
    	} else {
    	
    		return false;
    	
    	} 
    	
    
    }
    
    function checkForProducts($productLineID) {
    
    	$sql = "SELECT opm_productid FROM opm_products_productlines WHERE productlineid = " . $this->db->escape($productLineID);
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() > 0)
    		return true;
    	else
    		return false;

    
    }
    
    function deleteProductLine($productlineid) {

    	if ($productlineid) {
    	
            $sql = "DELETE FROM opm_productlines WHERE productlineid = " . $this->db->escape($productlineid);
    	
    		if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    	
    	} else {
    	
    		return false;
    	
    	} 
    
    }


}

?>