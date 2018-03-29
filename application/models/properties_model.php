<?

class Properties_model extends CI_Model {

    function PropertiesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchProperties($inc_inactive = false, $inc_noproducts = false, $returnTotal = false, $searchText = null, $offset = 0, $perPage = null, $firstLetter = null, $inc_nopermission = false, $usePropAssignments = false)
    {
    	$query_where = "";
    	$query_join = "";
    	
    	if (!$inc_inactive)
    		$query_where .= " AND properties.isactive = 1";
    		
    	if (USE_PERMISSION_QUERY && !$usePropAssignments && !$inc_nopermission) {
    	
    		$query_join .= PERMISSION_QUERY;
    		$query_where .= " AND canview.id IS NOT NULL";
    	
    	} elseif ($usePropAssignments) {
    	
    		$CI =& get_instance();
    		
    		$usergroupIDs = $CI->userinfo->usergroupid . "," . $CI->userinfo->usergroupid2;
    	
    		$query_join = " LEFT JOIN opm_usergroup_properties ON opm_usergroup_properties.propertyid = properties.propertyid AND opm_usergroup_properties.usergroupid IN (".$usergroupIDs.")";
    		$query_where =" AND opm_usergroup_properties.id IS NOT NULL ";
    	}
    	
    	
    	if ($searchText)
    		$query_where .= " AND properties.property LIKE '%".$searchText."%'";
    		
    	if ($firstLetter)
    		$query_where .= " AND properties.property LIKE '".$firstLetter."%'";
    	
    	if (!$inc_noproducts) { // hide properties w/o products
    		
    		$sql = "SELECT DISTINCT properties.propertyid, properties.property, properties.nv_propid 
					FROM properties,opm_products 
					$query_join
					WHERE properties.propertyid = opm_products.propertyid
					$query_where
					ORDER BY property";
    	} else {
    	
			
			$sql = "SELECT properties.*, count(opm_products.opm_productid) as numProducts
					FROM properties
					LEFT JOIN opm_products ON opm_products.propertyid = properties.propertyid
					$query_join
					WHERE properties.propertyid IS NOT NULL
					$query_where
					GROUP BY properties.propertyid
					ORDER BY property";

    	
    	}
    	
    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	
    	//die($sql);
        	
        $query = $this->db->query($sql);
    
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
    
    
    
    function fetchPropertiesWithUGAssignments($inc_inactive = false, $usergroupid)
    {
    	$query_where = "";
    	$query_join = "";
    	
    	if (!$inc_inactive)
    		$query_where .= " AND properties.isactive = 1";	


		$sql = "SELECT properties.*, opm_usergroup_properties.id AS isassigned
				FROM properties
				LEFT JOIN opm_products ON opm_products.propertyid = properties.propertyid
				LEFT JOIN opm_usergroup_properties ON opm_usergroup_properties.propertyid = properties.propertyid AND opm_usergroup_properties.usergroupid = ".$this->db->escape($usergroupid)."
				$query_join
				WHERE properties.propertyid IS NOT NULL
				$query_where
				GROUP BY properties.propertyid
				ORDER BY property";
	        	
        $query = $this->db->query($sql);
        
        return $query;

    
    }
    
    function fetchApprovalProperties() // get all properties w/ approval contacts!
    {
    		
    	$sql = "SELECT DISTINCT properties.propertyid, properties.property 
				FROM properties,opm_user_app_properties 
				WHERE properties.propertyid = opm_user_app_properties.propertyid
				AND properties.isactive = 1
				ORDER BY property";
    	
    
        	
        $query = $this->db->query($sql);
		return $query;
        
    }
    
    
    function fetchUserProperties($userid) // get all properties assigned to a particular user (in preferences)
    {
    		
    	$sql = "SELECT DISTINCT properties.propertyid, properties.property 
				FROM opm_user_properties
				LEFT JOIN properties ON properties.propertyid = opm_user_properties.propertyid
				WHERE opm_user_properties.userid = ".$this->db->escape($userid)."
				ORDER BY properties.property";
					
        $query = $this->db->query($sql);
		return $query;
        
    }
    
    
    function searchProperties($searchTerms) {
    
    	$sql = "SELECT propertyid
    			FROM properties
    			WHERE property LIKE '$searchTerms'
    			LIMIT 1";
    			
        $query = $this->db->query($sql);
        
		$row = $query->row(); 

   		if ($query->num_rows() > 0) {
   		
   			return $row->propertyid;
   			
   		} else {
   		
   			return null;
   		
   		}
    
    }
    
    function fetchPropertyInfo($id) {
    
    	$sql = "SELECT * FROM properties WHERE propertyid = '".$id."'";
    	$query = $this->db->query($sql);
    	$result = $query->row();
    	
    	return $result;
    
    }
    
    function fetchFirstLetterLinks($activeLetter = null) { //  get distinct first letters of property names!
    
    	    
    	$sql = "SELECT DISTINCT LEFT(properties.property, 1) AS propertyFirstLetter FROM properties
    			WHERE isactive = 1
    			ORDER BY property";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $row) {
    		
    		if ($activeLetter == $row->propertyFirstLetter)
    			$arrFL[] = "<a href=\"".base_url()."properties/search/0/".$row->propertyFirstLetter."\" class=\"redLink\">".$row->propertyFirstLetter."</a>&nbsp;&nbsp;";
    		else
    			$arrFL[] = "<a href=\"".base_url()."properties/search/0/".$row->propertyFirstLetter."\">".$row->propertyFirstLetter."</a>&nbsp;&nbsp;";

    	}
    			
    	return $arrFL;
    
    }
    
    function savePropertyInfo($arrPostData) {
    
    	if ($arrPostData['isactive'] == 'on')
     			$arrPostData['isactive'] = 1;
     		else
     			$arrPostData['isactive'] = 0;
     			
     	if ($arrPostData['isharley'] == 'on')
     			$arrPostData['isharley'] = 1;
     		else
     			$arrPostData['isharley'] = 0;
         	
		if ($arrPostData['propertyid']) { // we have a propertyid, update
     	
     		$sql = "UPDATE properties
     				SET property = ". $this->db->escape($arrPostData['property']).",
     				approval_methodid = ".$this->db->escape($arrPostData['approval_methodid']).",
     				isactive = ".$this->db->escape($arrPostData['isactive']).",
     				isharley = ".$this->db->escape($arrPostData['isharley']).",
     				default_productdesc = ".$this->db->escape($arrPostData['default_productdesc']).",
     				nv_propid = ".$this->db->escape($arrPostData['nv_propid']).",
     				copyright = ".$this->db->escape($arrPostData['copyright'])." ";
     				
     		if (isset($arrPostData['image_path']))
     			$sql .= ", image_path = '".$arrPostData['image_path']."'";
     				
     		$sql .= " WHERE propertyid = '".$arrPostData['propertyid']."'";
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['propertyid'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new property, insert
     	
     		
     		$sql = "INSERT INTO properties (property,approval_methodid,copyright,default_productdesc,nv_propid,createdby,createdate,isactive,isharley)
    			VALUES (". $this->db->escape($arrPostData['property']).",".$this->db->escape($arrPostData['approval_methodid']).",".$this->db->escape($arrPostData['copyright']).",".$this->db->escape($arrPostData['default_productdesc']).",".$this->db->escape($arrPostData['nv_propid']).",'".$this->userinfo->userid."','".mktime()."',1,".$this->db->escape($arrPostData['isharley']).")";
       		
       		if ($query = $this->db->query($sql)) {
       		
       			return $this->db->insert_id(); 
       		
       		} else {
       		
       			return false;
       		
       		}
       		
     	
     	}
    
    }
    
    function savePropertyBillingInfo($arrPostData) {
    
       	$propId = $arrPostData['propertyid'];
    	
    	// first delete all existing property percentages...
    	
    	$sql = "DELETE FROM opm_invoice_channel_rates WHERE propertyid = " . $this->db->escape($propId);
    	$this->db->query($sql);
    	
    	// insert the new dataaa!
    	
    	foreach ($arrPostData['channelPercentage'] as $channelcode => $rate) {
    	
    		$sql = "INSERT INTO opm_invoice_channel_rates (propertyid,channelcode,rate)
    				VALUES (".$this->db->escape($propId).",".$this->db->escape($channelcode).",".$this->db->escape($rate).")";
    	
    	
    		$this->db->query($sql);
    	}
    	
    	return true;    	
    
    }
    
    function saveAsset($arrPostData) {
    
    	/*echo "<pre>";
    	print_r($arrPostData);
    	die();*/
         
        $sql = "UPDATE opm_assets 
        		SET assetname = ".$this->db->escape($arrPostData['assetName']).",
        		assettypeid = ".$this->db->escape($arrPostData['assetTypeId']).",
        		assetdetail = ".$this->db->escape($arrPostData['assetDetail'])." ";
        		
        if (isset($arrPostData['resizedThumbnail'])) { // we have a thumbnail! 
        	$sql .= ", assetthumbnail = ".$this->db->escape($arrPostData['resizedThumbnail']).",
        			assetthumbnail_type = ".$this->db->escape($arrPostData['image_type'])." ";
        }
        
        $sql .= " WHERE serverfilename = " . $this->db->escape($arrPostData['random_str']);
		
		//die($sql);
		
		$this->db->query($sql);
		
		if ($this->db->affected_rows() > 0) {
			
			return true;
		
		} else {

			return false;
			
		}
    
    }
    
    function fetchAssets($propertyid) {
         
        $sql = "SELECT opm_assets.*,IFNULL(opm_asset_types.assettype,'Uncategorized') as assettype 
        		FROM opm_assets 
        		LEFT JOIN opm_asset_types ON opm_asset_types.id = opm_assets.assettypeid
        		WHERE opm_assets.propertyid = " . $this->db->escape($propertyid) . " 
        		
        		ORDER BY opm_asset_types.displayorder";
        
        $query = $this->db->query($sql);
        
        return $query;
    
    }
    
    function fetchAssetThumbnail($assetid) {
         
        $sql = "SELECT * FROM opm_assets WHERE assetid = " . $this->db->escape($assetid);
                
        $query = $this->db->query($sql);
        $row = $query->row();
                
        return $row;

        
    
    }
    
    function deletePropertyImage($propertyid) {
         
        $sql = "SELECT * FROM properties WHERE propertyid = " . $this->db->escape($propertyid);
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
        
        	$row = $query->row();
        	
        	if ($row->image_path) {
        	
				if (unlink($this->config->item('fileUploadPath') . "propertyimages/" . $row->image_path))
					return true;
				else
					return false;
					
			} else {
			
				return false;
			
			}
        
        } else {
        
        	return false;
        
        }

    }
    
    function checkForDuplicateNavisionPropID($nv_propid) {
    
    	$sql = "SELECT propertyid FROM properties WHERE nv_propid = " . $this->db->escape($nv_propid);
    	$query = $this->db->query($sql);
    	
    	if ($query->num_rows() > 0 && $nv_propid != '9999')
    		return true;
    	else
    		return false;
    
    }
    
    
    function fetchPropertyReportData() {
    	
    	$data = array();
    
	    $sql = "SELECT properties.propertyid, properties.property, opm_invoice_channels.channelcode, opm_invoice_channels.channel,
				IFNULL(opm_invoice_channel_rates.rate,100) AS rate
				FROM properties
				LEFT JOIN opm_invoice_channels ON opm_invoice_channels.channelcode IS NOT NULL
				LEFT JOIN opm_invoice_channel_rates ON opm_invoice_channel_rates.propertyid = properties.propertyid AND opm_invoice_channel_rates.channelcode = opm_invoice_channels.channelcode
				ORDER BY properties.property";
				
	    $query = $this->db->query($sql);
	    $lastPropertyID = "0";
	    
	    foreach ($query->result() as $row) {
	    
	    	if ($row->propertyid != $lastPropertyID) {
	    	
	    		$data[$row->propertyid]['id'] = $row->propertyid;
	    		$data[$row->propertyid]['property'] = $row->property;
	    		
	    	
	    	}
	    
	    	$data[$row->propertyid][$row->channelcode] = $row->rate;
	    
	    	$lastPropertyID = $row->propertyid;
	    }
	    
	    return $data;
	    
	   /* echo "<pre>";
	    print_r($data);
	    die();*/
    
    }

}

?>