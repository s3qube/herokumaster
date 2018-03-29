<?

class Files_model extends CI_Model {

    function FilesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchMasterFilesByPropertyID($propertyid)
    {
    
		$sql = "SELECT opm_masterfiles.*, properties.property, opm_products.productname, opm_products.opm_productid, opm_products.default_imageid
				FROM opm_products
				LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
				LEFT JOIN opm_masterfiles ON opm_masterfiles.opm_productid = opm_products.opm_productid
				WHERE opm_products.propertyid = ".$this->db->escape($propertyid)."
				AND opm_masterfiles.fileid IS NOT NULL
				AND opm_masterfiles.confirmed = 1
				ORDER BY opm_products.timestamp";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchSeparationsByPropertyID($propertyid)
    {
    
		$sql = "SELECT opm_separations.*, opm_products.productname, opm_products.opm_productid, opm_products.default_imageid
				FROM opm_products
				LEFT JOIN opm_separations ON opm_separations.opm_productid = opm_products.opm_productid
				WHERE opm_products.propertyid = ".$this->db->escape($propertyid)."
				AND opm_separations.fileid IS NOT NULL
				AND opm_separations.confirmed = 1
				ORDER BY opm_products.timestamp";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchMasterFiles($opm_productid = null, $strFileIDs = null)
    {
    
		$sql = "SELECT opm_masterfiles.*
				FROM opm_masterfiles ";
				
		if ($opm_productid)	
			$sql .= " WHERE opm_masterfiles.opm_productid = ".$this->db->escape($opm_productid);
		else
			$sql .= " WHERE opm_masterfiles.fileid IN ($strFileIDs)";
			
		$sql .= " AND opm_masterfiles.confirmed = 1 ";
		
		$sql .=	" ORDER BY timestamp";
        	
        $query = $this->db->query($sql);
        return $query;
        
    }
    
    function fetchSeparations($opm_productid = null, $strFileIDs = null)
    {
    
		$sql = "SELECT opm_separations.*
				FROM opm_separations ";
				
		if ($opm_productid)	
			$sql .= " WHERE opm_separations.opm_productid = ".$this->db->escape($opm_productid);
		else
			$sql .= " WHERE opm_separations.fileid IN ($strFileIDs)";
			
		$sql .= " AND opm_separations.confirmed = 1 ";
		
		$sql .=	" ORDER BY timestamp";
        	
        $query = $this->db->query($sql);
        return $query;
        
    }
    
    function fetchMasterFile($fileid)
    {
    
		$sql = "SELECT opm_masterfiles.*, opm_products.productname, properties.property, categories.category
				FROM opm_masterfiles
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_masterfiles.opm_productid
				LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
				LEFT JOIN categories ON categories.categoryid = opm_products.categoryid
				WHERE opm_masterfiles.fileid = '".$fileid."'";
        	
        $query = $this->db->query($sql);
       	return $query->row();
    }
    
    function fetchSeparation($fileid)
    {
    
		$sql = "SELECT opm_separations.*, opm_products.productname, properties.property, categories.category
				FROM opm_separations
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_separations.opm_productid
				LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
				LEFT JOIN categories ON categories.categoryid = opm_products.categoryid
				WHERE opm_separations.fileid = '".$fileid."'";
        	
        $query = $this->db->query($sql);
       	return $query->row();
    }
    
     function fetchAsset($assetid)
    {
    
		$sql = "SELECT opm_assets.*
				FROM opm_assets
				WHERE opm_assets.assetid = '".$assetid."'";
        	
        $query = $this->db->query($sql);
       	return $query->row();
    }

    
    function saveMasterFile($arrFile,$opm_productid) {
   
    	
    	$sql = "INSERT INTO opm_masterfiles (opm_productid,filesize,filetype,filename,timestamp)
    			VALUES (". $this->db->escape($opm_productid).",".$this->db->escape($arrFile['size']).",".$this->db->escape($arrFile['type']).",".$this->db->escape($arrFile['name']).",'".mktime()."')";
    	
    	$query = $this->db->query($sql);
       	return $this->db->insert_id();    
    }
    
    function confirmFileUpload($fileType,$fileid) {
   
    	if ($fileType == 'masterfile') {
    	
    		$sql = "UPDATE opm_masterfiles SET confirmed = 1 WHERE fileid = " . $this->db->escape($fileid);
    	
    	} else if ($fileType == 'separation') {
    	
    		$sql = "UPDATE opm_separations SET confirmed = 1 WHERE fileid = " . $this->db->escape($fileid);
    	
    	}
    	
    	if (isset($sql)) {
    	
    		$query = $this->db->query($sql);
       	
       	} 
       	
    }
    
    function saveSeparation($arrFile,$opm_productid) {
   
    	
    	$sql = "INSERT INTO opm_separations (opm_productid,filesize,filetype,filename,timestamp)
    			VALUES (". $this->db->escape($opm_productid).",".$this->db->escape($arrFile['size']).",".$this->db->escape($arrFile['type']).",".$this->db->escape($arrFile['name']).",'".mktime()."')";
    	
    	$query = $this->db->query($sql);
       	return $this->db->insert_id();    
    }
    
    function saveAsset($arrFile,$propertyid) { // random str is the filename!
    
    	$sql = "INSERT INTO opm_assets (propertyid,assetname,filesize,filetype,filename,dimensions,timestamp)
    			VALUES (". $this->db->escape($propertyid).",".$this->db->escape($arrFile['name']).",".$this->db->escape($arrFile['size']).",".$this->db->escape($arrFile['type']).",".$this->db->escape($arrFile['name']).",".$this->db->escape($arrFile['dimensions']).",'".mktime()."')";
    	
    	$query = $this->db->query($sql);
       	return $this->db->insert_id();    
    }
    
    function saveAssetThumbnail($fileid,$filename) { 
	
    	$sql = "UPDATE opm_assets SET serverthumbnailname = " . $this->db->escape($filename) . ", hasthumbnail = 1 WHERE assetid = " . $this->db->escape($fileid);
    	
    	error_log("SAVEASSETSQL:" . $sql);
    	$query = $this->db->query($sql);  
    }
    
    function deleteMasterFile($fileid)
    {
    
		$sql = "DELETE FROM opm_masterfiles WHERE opm_masterfiles.fileid = '".$fileid."'";
        	
        if ($this->db->query($sql))
       		return true;
       	else
       		return false;
    }
    
     function deleteSeparation($fileid)
    {
    
		$sql = "DELETE FROM opm_separations WHERE opm_separations.fileid = '".$fileid."'";
        	
        if ($this->db->query($sql))
       		return true;
       	else
       		return false;
    }
    
     function deleteAsset($fileid)
    {
    
		$sql = "DELETE FROM opm_assets WHERE opm_assets.assetid = '".$fileid."'";
        	
        if ($this->db->query($sql))
       		return true;
       	else
       		return false;
    }
    
    function archiveMasterFile($fileid)
    {
    
    	//
    
		$sql = "UPDATE opm_masterfiles SET isarchived = 1 WHERE fileid = '".$fileid."'";
        	
        if ($this->db->query($sql))
       		return true;
       	else
       		return false;
    }
    
    function markFileAsArchived($filetype,$fileid) {
    
    	if ($filetype == 'M') {
    	
    		$sql = "UPDATE opm_masterfiles SET archivedate = '".mktime()."' WHERE fileid = " . $this->db->escape($fileid);
    		
    		 if ($this->db->query($sql))
	       		return true;
	       	else
	       		return false;
    	
    	} else if ($filetype == 'S') {
    	
    		$sql = "UPDATE opm_separations SET archivedate = '".mktime()."' WHERE fileid = " . $this->db->escape($fileid);
    		
    		 if ($this->db->query($sql))
	       		return true;
	       	else
	       		return false;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }

}

/*

SELECT properties.property,opm_products.productname,opm_masterfiles.fileid,opm_masterfiles.filename
FROM `opm_masterfiles`
LEFT JOIN opm_products ON opm_products.opm_productid = opm_masterfiles.opm_productid
LEFT JOIN properties ON properties.propertyid = opm_products.propertyid

WHERE properties.propertyid = 12 AND opm_masterfiles.confirmed = 1


*/

?>