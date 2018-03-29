<?

class Accounts_model extends CI_Model {

    function AccountsModel() {
    
        // Call the Model constructor
		parent::__construct();   
    }
    
    // 20111220 mark
    function fetchPurchase($postdata) {
    	    	
    	$sql = "
    		SELECT
    			opa.id,
    			opa.purchasetypeid,
    			opa.opm_productid,
    			opa.account_id
    		FROM
    			opm_products_accounts opa
    		WHERE
    			opa.id = " . intval($postdata['id']);
    	
       	$query = $this->db->query($sql);
    	
      	if ($query->num_rows() > 0) {
       	
       		return $query->row();
    	
    	} else {
    		
    		return false;
    	
    	}
   
    }
    // 20111220 mark
    
    // 20111220 mark
    function releasePurchase($postdata) {
    	    	
    	$now = mktime();
    	
    	$sql = "
    		UPDATE 
    			opm_products_accounts
    		SET
    			enddate = $now
    		where
    			id = " . intval($postdata['id']);
    	
    	if ($query = $this->db->query($sql)) {
    		
    		if ($this->db->affected_rows() == 1) {
    			
    			return $now;
    		
    		}
    	
    	}
    	
    	return false;
   
    }
    // 20111220 mark
    
    function fetchAccounts($returnTotal = false, $offset = 0, $perPage = null, $account = null, $includeDisabled = false, $orderby = null) {
    
    	// first we must query to get the total # of results
    
    	if (!$returnTotal)
    		$sql = "SELECT opm_accounts.* ";
    	else 
    		$sql = "SELECT opm_accounts.accountid";

    			
    	$sql .=	" FROM opm_accounts ";
    	
    			
    	$sql .= " WHERE opm_accounts.accountid <> 0 ";
    			

    	if ($account)
    		$sql .= " AND opm_accounts.account LIKE '%".$account."%'";
    		
    	if (!$includeDisabled)
    		$sql .= " AND opm_accounts.isactive = 1";
    	
    	if ($orderby)
    		$sql .=" ORDER BY $orderby";
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	
    	$query = $this->db->query($sql);

    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    function fetchAccount($id) {
    
		$sql = "SELECT opm_accounts.* FROM opm_accounts WHERE accountid = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Account Not Found!");
       		return false;
       	
       	}
        
    }
    
    function fetchFirstLetterLinks($activeLetter = null) { //  get distinct first letters of account names!
    
    	    
    	$sql = "SELECT DISTINCT LEFT(opm_accounts.account, 1) AS accountFirstLetter FROM opm_accounts
    			WHERE isactive = 1
    			ORDER BY account";
    	
    	$query = $this->db->query($sql);
    	
    	$arrFL = array();
    	
    	foreach ($query->result() as $row) {
    		
    		if ($activeLetter == $row->accountFirstLetter)
    			$arrFL[] = "<a href=\"".base_url()."accounts/search/0/".$row->accountFirstLetter."\" class=\"redLink\">".$row->accountFirstLetter."</a>&nbsp;&nbsp;";
    		else
    			$arrFL[] = "<a href=\"".base_url()."accounts/search/0/".$row->accountFirstLetter."\">".$row->accountFirstLetter."</a>&nbsp;&nbsp;";

    	}
		
		return $arrFL;
		
	}
    
    function savePurchase($postdata) {
    
    	$CI =& get_instance();
    
    	if ($postdata['isexclusive'] == 'on')
 			$postdata['isexclusive'] = 1;
 		else
 			$postdata['isexclusive'] = 0;
    
   
		$sql = "INSERT INTO opm_products_accounts (purchasetypeid,opm_productid,account_id,user_id,isexclusive,enddate,timestamp)
				VALUES (".$this->db->escape($postdata['purchasetypeid']).",".$this->db->escape($postdata['opm_productid']).",".$this->db->escape($postdata['accountid']).",".$this->db->escape($CI->userinfo->userid).",".$this->db->escape($postdata['isexclusive']).",".$this->db->escape($postdata['enddate']).",".mktime().")";      	
				
		if ($query = $this->db->query($sql)) {
		
			$lastPurchaseID = $this->db->insert_id();
		
			// now insert last purchase id into products table - make searches easier!!
			
			$sql = "UPDATE opm_products SET lastpurchaseid = " . $this->db->escape($lastPurchaseID) . " WHERE opm_productid = " . $this->db->escape($postdata['opm_productid']);      	
				
			$query = $this->db->query($sql);
		
			return true;
		
		} else {
		
			return false;
       	
       	}
       	 
    }
    
    function fetchPurchaseTypes() {
    
		$sql = "SELECT opm_purchase_types.*
				FROM opm_purchase_types ";

		$sql .=" ORDER BY opm_purchase_types.id";
        	
        $query = $this->db->query($sql);
        return $query;
    
    }
    
    function fetchPurchaseType($id) {
    
		$sql = "SELECT opm_purchase_types.* FROM opm_purchase_types WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Purchase Type Not Found!");
       		return false;
       	
       	}
        
    }
    
    function saveAccountInfo($postdata) {
    
   
		$sql = "INSERT INTO opm_accounts (account,isactive,createdby,createdate)
				VALUES (".$this->db->escape($postdata['account']).",1,".$this->db->escape($this->userinfo->userid).",".mktime().")";      	
				
		if ($query = $this->db->query($sql)) {
		
			return true;
		
		} else {
		
			return false;
       	
       	}
    	
    	
    
    }
    
    function changeAccountStatus($accountid, $isactive) {
    
    	$sql = "UPDATE opm_accounts SET isactive = " . $this->db->escape($isactive) . " WHERE accountid = " . $this->db->escape($accountid);
    
    	if ($query = $this->db->query($sql)) {
		
			return true;
		
		} else {
		
			return false;
       	
       	}
    
    }

}

?>