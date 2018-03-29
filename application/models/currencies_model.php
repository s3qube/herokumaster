<?

class Currencies_model extends CI_Model {

    function CurrenciesModel()
    { 
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchCurrencies()
    {
    
		$sql = "SELECT opm_currencies.*
				FROM opm_currencies
				";

		$sql .=" ORDER BY opm_currencies.displayorder";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchCurrency($id)
    {
    
		$sql = "SELECT opm_currencies.* FROM opm_currencies WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Currency Not Found!");
       		return false;
       	
       	}
        
    }
    

}

?>