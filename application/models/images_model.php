<?

class Images_model extends CI_Model {

    function ImagesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchImages($opm_productid)
    {
    
		$sql = "SELECT opm_images.*, opm_products.opm_productid AS isdefault
				FROM opm_images
				LEFT JOIN opm_products ON opm_products.default_imageid = opm_images.imageid
				WHERE opm_images.opm_productid = '".$opm_productid."' AND opm_images.deletedate = 0
				ORDER BY imageid";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function setDefaultImageID($opm_productid,$imageid = null)
    {
    
    	if ($imageid) { // we have specified a particular image id to set it to
    	
    		$sql = "UPDATE opm_products SET default_imageid = '".$imageid."' WHERE opm_productid = '".$opm_productid."'";
    		
    		if($query = $this->db->query($sql))
       			return true;
       		
    	} else { // it's up to the script to figure it out.
    	
    		// first check if there already is one and if it is valid?!
    		
    		$sql = "SELECT opm_products.default_imageid, opm_images.imageid
    				FROM opm_products
    				LEFT JOIN opm_images ON opm_images.imageid = opm_products.default_imageid AND opm_images.deletedate = 0
    				WHERE opm_products.opm_productid = '".$opm_productid."'
    				AND opm_images.imageid IS NOT NULL";
    				
    		$query = $this->db->query($sql);
    		
    		if ($query->num_rows == 0) { // we must use the earliest imageid as default!
    		
    			$sql = "SELECT imageid FROM opm_images WHERE opm_productid = '".$opm_productid."' AND deletedate = 0 ORDER BY opm_images.imageid LIMIT 1";
    			$query = $this->db->query($sql);
    			
    			if ($row = $query->row()) { // there are images which qualify

    				$sql = "UPDATE opm_products SET default_imageid = '".$row->imageid."' WHERE opm_productid = '".$opm_productid."'";
    				$this->db->query($sql);
    				return true;
    				
    			} else {
    				
    				$sql = "UPDATE opm_products SET default_imageid = 0 WHERE opm_productid = '".$opm_productid."'";
    				$this->db->query($sql);
    				return true;
    				
    			}
    		
    		}
    	
    	}
    	
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function saveImage($postdata)
    {
    
    	$sql = "INSERT INTO opm_images (opm_productid,filename,image_type,image_label,createdby,createdate)
    			VALUES ('".$postdata['opm_productid']."','".$postdata['filename']."','".$postdata['image_type']."','".$postdata['image_label']."','".$this->userinfo->userid."',".mktime().")";
		
		if ($query = $this->db->query($sql)) {
			
			$imageId = $this->db->insert_id();
			
			$this->setDefaultImageID($postdata['opm_productid']);
			
			return $imageId;
			
		
		} else {
		
			return false;
		
		}
	
    }
    
    function deleteImage($imageid) {
    
    	// first, let's figure out what product we're dealing with.
    	
    	$sql = "SELECT opm_productid,imageid FROM opm_images WHERE imageid = " . $this->db->escape($imageid);
    	$result = $this->db->query($sql);
    	
    	if ($result->num_rows() > 0) {
    	
			$row = $result->row();
			$opm_productid = $row->opm_productid;
    		$imageid = $row->imageid;
    		
    	} else {
    	
    		$this->opm->displayError("Image cannot be deleted.","/products/view/".$opm_productid."/images");
			return true;
    	
    	}
    	
    	
    	$sql = "UPDATE opm_images SET deletedate = ".mktime()." WHERE imageid = " . $this->db->escape($imageid);
		
		if ($query = $this->db->query($sql)) {
			
			// move file into deleted area.
			
			$fileSavePath = $this->config->item('fileUploadPath') . "visuals/";
			
			if ((is_file($fileSavePath . $imageid)) && copy($fileSavePath . $imageid, $fileSavePath . "deleted/" . $imageid)) {
  		
  				unlink($fileSavePath . $imageid);
		
			}
			
			$this->setDefaultImageID($opm_productid);
			return $opm_productid;
			
		} else {
		
			return false;
		
		}
    
    }
    
    function getThumbs($returnTotal = false, $propertyid, $productLineID, $searchText, $approvalStatusID,$opmproductid,$productcode,$designerid,$categoryid,$usergroupid, $perPage = null, $offset = null) {
    	
    	
    	
    	
    	if (!$returnTotal)
			$sql = "	SELECT opm_images.imageid, opm_products.*,properties.property,opm_approvalstatuses.approvalstatus, opm_accounts.account AS lastpurchase_account, opm_products_accounts.timestamp AS lastpurchase_timestamp";
		else
			$sql = "	SELECT opm_images.imageid ";
		
		
		$sql .= "	FROM opm_products_productlines
					LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_productlines.opm_productid
					LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
					LEFT JOIN opm_images ON opm_images.opm_productid = opm_products.opm_productid
					LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_products.approvalstatusid
					LEFT JOIN opm_products_accounts ON opm_products_accounts.id = opm_products.lastpurchaseid
    				LEFT JOIN opm_accounts ON opm_accounts.accountid = opm_products_accounts.account_id ";
    	
    	if ($designerid) {
    		
			$sql .=" LEFT JOIN opm_products_designers ON opm_products_designers.opm_productid = opm_products.opm_productid ";
			
		}
		
		if ($usergroupid) {
    			
			$sql .=" LEFT JOIN opm_products_usergroups ON opm_products_usergroups.opm_productid = opm_products.opm_productid ";
		
		}
    	
    	
		$sql .= "	WHERE opm_images.imageid IS NOT NULL AND opm_images.deletedate = 0";
		
		
		if ($searchText) {
			
			$searchText = urldecode($searchText);
			
			$arrSearchWords = explode(" ",$searchText);
    			
    			$sql .= " AND ( ";
    			
    			foreach ($arrSearchWords as $key => $data) {

    				
    				$sql .= " opm_products.productname LIKE '%".addslashes($data)."%' AND";
    				
    			}
    			
    			// remove last AND
    			
    			$sql = substr($sql,0,strlen($sql)-3);
    			
    			$sql .= ")";
			
			
		}
			//$sql .= " AND opm_products.productname LIKE '%".$searchText."%'";
		
		if ($productLineID && $productLineID != 'ALL')
			$sql .=" AND opm_products_productlines.productlineid = ".$this->db->escape($productLineID);
			
		if ($propertyid)
			$sql .=" AND opm_products.propertyid = ".$this->db->escape($propertyid);
			
		if ($approvalStatusID)
			$sql .= " AND opm_products.approvalstatusid = " . $this->db->escape($approvalStatusID);
					
		if ($designerid)
    		$sql .= " AND opm_products_designers.userid = '".$designerid."'";
    		
    	if ($categoryid)
    		$sql .= " AND opm_products.categoryid = '".$categoryid."'";
    		
    	if ($usergroupid) {
    		
    		// get children of this usergroupid!
    		
    		$CI =& get_instance();
			
			$CI->load->model('usergroups_model');
			$ugids = $CI->usergroups_model->getChildren($usergroupid);
			$ugids[] = $usergroupid;
    		$strUGs = implode(",", $ugids); // make it into a comma-delimited list for the sql query!

    		$sql .= " AND opm_products_usergroups.usergroupid IN (".$strUGs.")";

    	}
    	
    	if ($opmproductid)
    		$sql .= " AND opm_products.opm_productid = '".$opmproductid."'";
    		
    	if ($productcode)
    		$sql .= " AND opm_products.productcode = '".$productcode."'";
    	
    	
		$sql .=" GROUP BY opm_images.imageid";
					
		if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
		
				
		$query = $this->db->query($sql);
				
		
		if ($returnTotal) {
		
			return $query->num_rows();
		
		} else {
		
			return $query;
		
		}
	
	}
	
	function writeThumbnailTempFile($imageid,$width=350)
    {
    
		$sql = "SELECT opm_images.*
				FROM opm_images
				WHERE opm_images.imageid = '".$imageid."'";
        	
        $query = $this->db->query($sql);
        
        if (!$i = $query->row()) {
        
        	//$this->opm->displayError("Image ID ".$imageid." cannot be found.");
			return false;
        
        }
        	
    	$size = $width;  // new image width
    	
    	// read image from file.
    	
    	$filePath = $this->config->item('fileUploadPath') . "visuals/" . $i->imageid;
    	
    	$fh = fopen($filePath, 'r');
		$imageData = fread($fh, filesize($filePath));
		fclose($fh);
    	
    	
		$src = imagecreatefromstring($imageData); 
		$width = imagesx($src);
		$height = imagesy($src);
		$aspect_ratio = $height/$width;
		
		if ($width <= $size) {
			$new_w = $width;
			$new_h = $height;
		} else {
			$new_w = $size;
			$new_h = abs($new_w * $aspect_ratio);
		}
		
		$img = imagecreatetruecolor($new_w,$new_h); 
		  
		imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
    
		if (imagejpeg($img,$this->config->item("webrootPath") . "resources/images/temp/invoice/" . $imageid . ".jpg"))
			return true;
		else
			return false;

		imagedestroy($img);
	    
    }

}

?>