<?

class Navimport_model extends CI_Model {

    function NavimportModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchNavisionMatches($returnTotal = false, $offset = 0, $perPage = null, $propertyid) {
    
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT navision_product_import.* ";
    	else 
    		$sql = "SELECT navision_product_import.id";

    			
    	$sql .=	" FROM navision_product_import
    			  LEFT JOIN opm_products ON opm_products.designcode = navision_product_import.designcode  ";
    	
      			
    	$sql .= " WHERE opm_products.opm_productid IS NULL
    			  AND navision_product_import.propertyid = " . $this->db->escape($propertyid);
    
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";

    	$query = $this->db->query($sql);
    
    	
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    function matchProductCodes($propertyID) {
    
    	$numAssigned = 0;
    
    	$sql = "
    		
    		SELECT opm_products.opm_productid,opm_products.productcode
    		
    		FROM opm_products			
			
			WHERE opm_products.propertyid = ".$this->db->escape($propertyID)."
			AND opm_products.productcode <> ''
			AND opm_products.designcode = 0
			
			";
			
		//die($sql);
			
		$query = $this->db->query($sql);
		
		foreach ($query->result() as $row) {
		
			$choppedCode = substr($row->productcode,3,4);
		
			if (is_numeric($choppedCode) && (strlen($row->productcode) == 7)) {
			
				// assign the last four digits to design code field.
				
				
				$sql = "UPDATE opm_products SET designcode = ".$this->db->escape($choppedCode)."
						WHERE opm_productid = " . $this->db->escape($row->opm_productid);
			
				
			
				$this->db->query($sql);
				$numAssigned++;
			}
		
		}
			
			
		return $numAssigned;
    
    }
    

}

?>