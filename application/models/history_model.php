<?

class History_model extends CI_Model {

    function HistoryModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchHistory($opm_productid,$limit = null)
    {
    
		$sql = "SELECT opm_history.*
				FROM opm_history
				WHERE opm_history.opm_productid = '".$opm_productid."'
				ORDER BY timestamp DESC";
				
		if ($limit) {
		
			$sql .= " LIMIT $limit";
		
		}
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function addHistoryItem($opm_productid,$event)
    {
    
		
		$sql = "INSERT INTO opm_history (opm_productid,event,timestamp)
				VALUES (".$this->db->escape($opm_productid).",".$this->db->escape($event).",".mktime().")";
		
		
		if($this->db->query($sql))
			return true;
		else
			return false;
				
				
	}
	
	function fetchInvoiceHistory($invoiceid,$limit = null)
    {
    
		$sql = "SELECT opm_invoice_history.*
				FROM opm_invoice_history
				WHERE opm_invoice_history.invoiceid = '".$invoiceid."'
				ORDER BY timestamp DESC";
				
		if ($limit) {
		
			$sql .= " LIMIT $limit";
		
		}
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function addInvoiceHistoryItem($invoiceid,$event)
    {
		
		$sql = "INSERT INTO opm_invoice_history (invoiceid,event,timestamp)
				VALUES (".$this->db->escape($invoiceid).",".$this->db->escape($event).",".mktime().")";
		
		
		if($this->db->query($sql))
			return true;
		else
			return false;
				
				
	}
    

}

?>