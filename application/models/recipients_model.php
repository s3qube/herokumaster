<?

class Recipients_model extends CI_Model {

    function RecipientsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }


	function fetchRecipients($opm_productid = 0) {
	
		$sql = "SELECT *
				FROM `opm_products_usergroups`
				LEFT JOIN opm_usergroups ON opm_usergroups.usergroupid = opm_products_usergroups.usergroupid
				WHERE opm_products_usergroups.opm_productid = '".$opm_productid."'";
	
		$recipients = $this->db->query($sql);
	
		return $recipients
	
	}
	
?>