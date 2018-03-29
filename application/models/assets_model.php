<?

class Assets_model extends CI_Model {

    function AssetsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchAssets($returnTotal = false, $offset = 0, $perPage = null, $propertyid = null, $authorid = null, $tags = null) {
    	
	
		$sql = "
		
				SELECT opm_assets.*, properties.property, GROUP_CONCAT(opm_asset_tags.tag) as tags, opm_asset_authors.author
				FROM opm_assets
				LEFT JOIN properties ON properties.propertyid = opm_assets.propertyid
				LEFT JOIN opm_asset_authors ON opm_asset_authors.id = opm_assets.authorid
				LEFT JOIN opm_asset_tags_join ON opm_asset_tags_join.assetid = opm_assets.assetid
				LEFT JOIN opm_asset_tags ON opm_asset_tags.id = opm_asset_tags_join.tagid
				WHERE opm_assets.assetid IS NOT NULL

				
		";
				
		
		if ($propertyid) {
			
			
			$sql .=" AND opm_assets.propertyid = " . $this->db->escape($propertyid);
			
			
		}
		
		if ($authorid) {
			
			
			$sql .=" AND opm_assets.authorid = " . $this->db->escape($authorid);
			
			
		}
		
		
		if ($tags) {
			
			
			$sql .=" AND opm_assets.authorid = " . $this->db->escape($authorid);
			
			
		}

						
				
		$sql .="
				
				GROUP BY opm_assets.assetid
				ORDER BY property
				
				";

    	
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	
    	//die($sql);
        	
        $query = $this->db->query($sql);
    
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    function fetchAssetInfo($id) {
    
    	$sql = "SELECT opm_assets.*, GROUP_CONCAT(opm_asset_tags.tag) AS tags
    	 		FROM opm_assets
    			LEFT JOIN opm_asset_tags_join ON opm_asset_tags_join.assetid = opm_assets.assetid
				LEFT JOIN opm_asset_tags ON opm_asset_tags.id = opm_asset_tags_join.tagid
    			WHERE opm_assets.assetid = " . $this->db->escape($id) ."
    			GROUP BY opm_assets.assetid
    			
    			";
    	
    	$query = $this->db->query($sql);
    	$result = $query->row();
    	
    	return $result;
    
    }
    
    function doesTagExist($tag) { // if yes, returns tagid, otherwise returns false
	    
	    $sql = "SELECT id FROM opm_asset_tags WHERE tag = " . $this->db->escape($tag);
	    $query = $this->db->query($sql);
	    
	    if ($row = $query->row()) {
		    
		    return $row->id;
		    
	    } else {
		    
		    return false;
		    
	    }
	    
    }
    
    function writeTag($tag) { // write tag and return id
	    
	    $sql = "INSERT INTO opm_asset_tags (tag) VALUES(".$this->db->escape($tag).")";
	    
	    if ($this->db->query($sql)) {
		      
	    	return $this->db->insert_id();
		    
	    }
	   
    }
    
    function clearTagsFromAsset($assetid) { // write tag and return id
	    
	    $sql = "DELETE FROM opm_asset_tags_join WHERE assetid = " . $this->db->escape($assetid);
	    
	    if ($this->db->query($sql)) {
		      
	    	return true;
		    
	    } else {
		    
		    return false;
		    
	    }
	   
    }
            
    function saveAssetInfo($arrPostData) {
    
    	/*echo "<pre>stuff<br>";
    	print_r($arrPostData);
    	
    	echo "<br><br><br>";
    	
    	// parse tags
    	die();*/
    	
    	if (!$arrPostData['assetid']) {
	    	
	    	$this->opm->displayError("No Asset ID given!");
	    	return false;
	    	
    	}
    	
    	$arrTags = explode(",",$arrPostData['tags']);
    	$arrTags = array_map("trim", $arrTags);
    	
    	$arrTags = array_map("trim", $arrTags);
    	$arrTags = array_map("strtolower", $arrTags);    	
    	
		
		$this->clearTagsFromAsset($arrPostData['assetid']);
		
		foreach ($arrTags as $tag) {
			
			if (!$tagid = $this->doesTagExist($tag)) {
				
				$tagid = $this->writeTag($tag);
				
			}
			
			$sql = "INSERT INTO opm_asset_tags_join (tagid,assetid) VALUES (".$tagid.",".$this->db->escape($arrPostData['assetid']).")";
			$this->db->query($sql);
			
		}
		    	
    	//die();
         
        $sql = "UPDATE opm_assets 
        		SET assetname = ".$this->db->escape($arrPostData['assetName']).",
        		authorid = ".$this->db->escape($arrPostData['authorid']).",
        		assetdetail = ".$this->db->escape($arrPostData['assetDetail'])." ";
        		
        /*if (isset($arrPostData['resizedThumbnail'])) { // we have a thumbnail! 
        	$sql .= ", assetthumbnail = ".$this->db->escape($arrPostData['resizedThumbnail']).",
        			assetthumbnail_type = ".$this->db->escape($arrPostData['image_type'])." ";
        }*/
        
        $sql .= " WHERE assetid = " . $this->db->escape($arrPostData['assetid']);
		
		//die($sql);
		
		//$this->db->query($sql);
		
		if ($this->db->query($sql)) {
			
			return $arrPostData['assetid'] ;
		
		} else {

			return false;
			
		}
    
    }
    
   /* function fetchAssets($propertyid) {
         
        $sql = "SELECT opm_assets.*,IFNULL(opm_asset_types.assettype,'Uncategorized') as assettype 
        		FROM opm_assets 
        		LEFT JOIN opm_asset_types ON opm_asset_types.id = opm_assets.assettypeid
        		WHERE opm_assets.propertyid = " . $this->db->escape($propertyid) . " 
        		
        		ORDER BY opm_asset_types.displayorder";
        
        $query = $this->db->query($sql);
        
        return $query;
    
    }*/
    
    function fetchAssetThumbnail($assetid) {
         
        $sql = "SELECT * FROM opm_assets WHERE assetid = " . $this->db->escape($assetid);
                
        $query = $this->db->query($sql);
        $row = $query->row();
                
        return $row;

        
    
    }
   
}

?>