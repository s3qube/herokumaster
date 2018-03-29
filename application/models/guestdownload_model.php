<?

class Guestdownload_model extends CI_Model {

    function GuestdownloadModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchDownload($randomStr)
    {
    	
    	$sql = "SELECT opm_guestdownload.*
    			FROM opm_guestdownload
    			WHERE opm_guestdownload.unique_id = " . $this->db->escape($randomStr);
    	      	
        if ($query = $this->db->query($sql)) {
        	
        	$row = $query->row();
        	return $row;
        	
        } else {
        	
        	return false;
        
        }
        
    }
    
    function checkString($randomStr)
    {
    	
    	$sql = "SELECT opm_guestdownload.*
    			FROM opm_guestdownload
    			WHERE opm_guestdownload.unique_id = " . $this->db->escape($randomStr);
    	 
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() == 0)
    		return true;
    	else
    		return false;
        
    }
    
    function saveDownload($arrPostData) {
        
        if ($arrPostData['isUpload'])
        	$isUpload = 1;
        else
        	$isUpload = 0; 	
        
		$sql = "INSERT INTO opm_guestdownload (opm_productid,fileid,filetype,isupload,unique_id,username,useremail,createdby,createdate)
				VALUES(".$this->db->escape($arrPostData['opmProductID']).",".$this->db->escape($arrPostData['fileID']).",".$this->db->escape($arrPostData['fileType']).",".$isUpload.",".$this->db->escape($arrPostData['randomString']).",".$this->db->escape($arrPostData['name']).",".$this->db->escape($arrPostData['email']).",".$this->db->escape($this->userinfo->userid).",".mktime().")";
		
		 if ($this->db->query($sql))
		 	return true;
		 else
		 	return false;
		    
    }
    
     function disableDownload($id) {
         	
		$sql = "UPDATE opm_guestdownload SET downloaddate = '" . mktime() . "' WHERE id = " . $this->db->escape($id);
		
		 if ($this->db->query($sql))
		 	return true;
		 else
		 	return false;
		    
    }
   
}

?>