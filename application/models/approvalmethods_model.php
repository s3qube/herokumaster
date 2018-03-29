<?

class Approvalmethods_model extends CI_Model {

    function ApprovalmethodsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchApprovalMethods() 
    {
    	
		$sql = "SELECT opm_approvalmethod.*
				FROM opm_approvalmethod";
        	
        $query = $this->db->query($sql);
      	
      	return $query;        
        
    }
    
}

?>