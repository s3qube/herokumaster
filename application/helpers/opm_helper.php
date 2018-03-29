<?

function opmLog($str) { // write line to OPM log fileÉ

	$CI =& get_instance();
	
	$strDate = date('m/d/y h:i:s A');

	$filePath = $CI->config->item('logPath');
	$text = $strDate . " - " . $str . "\n";
	
	if (is_writable($filePath)) {
	
	    if (!$handle = fopen($filePath, 'a')) {
		
			error_log("Could not open Log File!");
			return false;   
	    
	    }
	
	    // Write $somecontent to our opened file.
	    if (fwrite($handle, $text) === FALSE) {
	    
			return true;
	    
	    }
	
	    fclose($handle);
	
	} else {
	    
	    error_log("The OPM log file is not writeable!");
	    return false;
	    
	}

}

function checkPerms($perm, $redirect = false) { // redirect is used in controllers, as opposed to views where things are toggled and user isn't necessarily redired.

	$CI =& get_instance();

	if (isset($CI->userinfo->perms[$perm])) {
		return true;
	} else {
	
		if ($redirect) {
			$CI->opm->displayError("You do not have permission to do that.");
			return true;
		} else {
			return false;
		}
	}

}

function buildPermSQL($where, $joinProductsDesignersTable = false, $joinApprovalPropertiesTable = false, $joinUsergroupPropertiesTable = false, $joinProductsLicenseesTable = false) {

	
	$sql = "
	
			SELECT opm_products.opm_productid as id
			FROM opm_products
			
			LEFT JOIN opm_products_usergroups ON opm_products_usergroups.opm_productid = opm_products.opm_productid ";
			
			if ($joinApprovalPropertiesTable) {
			
				$sql .= " LEFT JOIN opm_user_app_properties ON opm_user_app_properties.propertyid = opm_products.propertyid ";
			
			}
			
			if ($joinProductsDesignersTable) {
			
				$sql .= " LEFT JOIN opm_products_designers ON opm_products_designers.opm_productid = opm_products.opm_productid ";
			
			}
			
			if ($joinProductsLicenseesTable) {
			
				$sql .= " LEFT JOIN opm_products_licensees ON opm_products_licensees.opm_productid = opm_products.opm_productid ";
			
			}
			
			if ($joinUsergroupPropertiesTable) {
			
				$sql .= " LEFT JOIN opm_usergroup_properties ON opm_usergroup_properties.propertyid = opm_products.propertyid ";
			
			}
					
			
	$sql .= "
			
			WHERE (
	
	";
	
	$sql .= $where;
	
	
	$sql .= ")			
						
			GROUP BY opm_products.opm_productid ";
				
				
	return $sql;

}

function buildAscDescUrl(&$args) {
	
	// this function assumes that the last 3 argument (items in the query string) are orderby, order_ascdesc and offset.

	$CI =& get_instance();

	$url = base_url() . $CI->uri->segment(1) . "/" . $CI->uri->segment(2) . "/";
	
	
	
	if (($args['orderByAscDesc'] == 'asc') || ($args['orderByAscDesc'] == '0')) {
	
		$args['orderByAscDesc'] = 'desc';
		$arr = "&uarr;";
	
	} else {
		
		//die($args['orderByAscDesc']);

		$args['orderByAscDesc'] = 'asc';
		$arr = "&darr;";
		
	}
	

	foreach ($args as $k=>$d)  {
    		
    	// build a url for use by the table headers (sorting links). Don't put sorting info or page offset into this url, as it needs to be filled out by the link!
    		
    	//if ($k != 'orderByAscDesc' && $k != 'offset' && $k != 'exportExcel' && $k != 'quickSearch')
    		$url .= $d . "/";
    	
    }
    	    
   
   // display an up or down arrow if that field is sorted.
 
 	$str = "<a href=\"$url\">$arr</a>";
 	    
    return $str;


}

function goodFilename($filename) { // removes offensive characters from filenames, dirnames, etc
	
	$badChars = array(" ", "&", "+", "\"", "\'","/");
	
	$filename = str_replace($badChars, "_", $filename);

	return $filename;


}

function buildDesignerList($objDesigners) {

	$strDesigners = "";
	
	foreach ($objDesigners as $d) {
								
		if (checkPerms('can_view_approval_status')) {
								
			$strDesigners .= "<a href=\"".base_url() ."users/view/".$d['userid']."\">" . $d['username'] . "</a>,&nbsp;";
								
		} else {
								
			$strDesigners .= $d['username'] . ",&nbsp;";
								
		} 
								
	}
	
	// get the last ,&nbsp; off the bloody end!
	
	$strDesigners = substr($strDesigners,0,(strlen($strDesigners)-7));
	
	return $strDesigners;

}


function buildAbbrList($objList) {

	$strList = "";

	foreach($objList as $ug) {
	
		$strList .= $ug['usergroup'] . ", ";
	
	}
	
	// get the last ,&nbsp; off the bloody end!
	
	$strList = substr($strList,0,(strlen($strList)-2));
	
	return $strList;

}

function opmDate($timestamp, $fullYear = false) {

	if ($fullYear)
		$dateString = "m/d/Y";
	else
		$dateString = "m/d/y";
		
	return date($dateString, $timestamp);

}


function checkDisabled() {

	global $formDisabled;

	if (isset($formDisabled))
		echo "DISABLED";
	
	return true;

}

function opmDateTime($timestamp) {

	return date("m/d/y h:i a", $timestamp);

}

function checkEmail($email) {
	
	if (!preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9\._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9\._-] +)+$/" , $email)) {
  		return false;
 	}
 	
 	return true;
}


function array2ul($arr,$echo,$root) {

	if ($root)
		$out = "<ul class=\"mktree\" id=\"tree1\">";
	else
		$out = "<ul>";

	foreach ($arr as $a) {
		
			$out .= "<li>" . "<a href=\"".base_url()."usergroups/edit/".$a['usergroupid']."\" class=\"redLink\">" . $a['usergroup'] . "</a>";
			
			if (isset($a['children']))
				$out .= array2ul($a['children'],0,0);
				
			$out .="</li>";
	
	}
	
	$out .= "</ul>";

	if ($echo == 1)
		echo $out;
	else
		return $out;

}

function categoryArray2ul($arr,$echo,$root) {

	if ($root)
		$out = "<ul class=\"mktree\" id=\"tree1\">";
	else
		$out = "<ul>";

	foreach ($arr as $a) {
		
			$out .= "<li>" . "<a href=\"".base_url()."categories/edit/".$a['categoryid']."\" class=\"redLink\">" . $a['category'] . "</a>";
			
			if (isset($a['children']))
				$out .= categoryArray2ul($a['children'],0,0);
				
			$out .="</li>";
	
	}
	
	$out .= "</ul>";

	if ($echo == 1)
		echo $out;
	else
		return $out;

}

function officeArray2ul($arr,$echo,$root) {

	if ($root)
		$out = "<ul class=\"mktree\" id=\"tree1\">";
	else
		$out = "<ul>";

	foreach ($arr as $a) {
		
			$out .= "<li>" . "<a href=\"".base_url()."offices/edit/".$a['id']."\" class=\"redLink\">" . $a['office'] . "</a>";
			
			if (isset($a['children']))
				$out .= officeArray2ul($a['children'],0,0);
				
			$out .="</li>";
	
	}
	
	$out .= "</ul>";

	if ($echo == 1)
		echo $out;
	else
		return $out;

}

function byteSize($bytes) {

    $size = $bytes / 1024;
    if($size < 1024)
        {
        $size = number_format($size, 2);
        $size .= ' KB';
        } 
    else 
        {
        if($size / 1024 < 1024) 
            {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
            } 
        else if ($size / 1024 / 1024 < 1024)  
            {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
            } 
        }
    return $size;

}

function displayChildren($arrUsergroupsRow) {
	
	global $arrUsergroups_select;
	global $uglevel;
	
	$uglevel++;
	
	foreach ($arrUsergroupsRow['children'] as $data) {
	
		// add each child to the array  - dashes are used to "tab" the list to indicate the parent/child relationship.
		
		$dashes = "";
		
		for ($x=1;$x<=$uglevel;$x++)
			$dashes .= "&nbsp;&nbsp;";
		
		$arrUsergroups_select[$data['usergroupid']] = $dashes . "&nbsp; " . $data['usergroup'];
		
		if (isset($data['children']))
			displayChildren($data);
	
	}
	
	$uglevel--;
	
	

}

function displayCatChildren($arrCategoriesRow) {
	
	global $arrCategories_select;
	global $catlevel;
	
	$catlevel++;
	
	foreach ($arrCategoriesRow['children'] as $data) {
	
		// add each child to the array  - dashes are used to "tab" the list to indicate the parent/child relationship.
		
		$dashes = "";
		
		for ($x=1;$x<=$catlevel;$x++)
			$dashes .= "&nbsp;&nbsp;";
		
		$arrCategories_select[$data['categoryid']] = $dashes . "&nbsp;" . $data['category'];
		
		if (isset($data['children']))
			displayCatChildren($data);
	
	}
	
	$catlevel--;
	
	

}

function displayOfficeChildren($arrOfficesRow) {
	
	global $arrOffices_select;
	global $oflevel;
	
	$oflevel++;
	
	foreach ($arrOfficesRow['children'] as $data) {
	
		// add each child to the array  - dashes are used to "tab" the list to indicate the parent/child relationship.
		
		$dashes = "";
		
		for ($x=1;$x<=$oflevel;$x++)
			$dashes .= "&nbsp;&nbsp;";
		
		$arrOffices_select[$data['id']] = $dashes . "&nbsp;" . $data['office'];
		
		if (isset($data['children']))
			displayOfficeChildren($data);
	
	}
	
	$oflevel--;
	
	

}


function usergroupArray2Select($arrUsergroups) {

	$arrUsergroups_select = array();
	
	global $arrUsergroups_select;
	global $uglevel;
	
	$uglevel = 0;

	// we are trying to build an array which will put the multidimensional usergroups array into a one dimen
	// sional array, suitable for display in an HTML select.

	foreach ($arrUsergroups as $key=>$data) {
	
		$arrUsergroups_select[$data['usergroupid']] = "<b>".$data['usergroup']."</b>";
		
		if (isset($data['children']))
			displayChildren($data);
	
	}
	
	return $arrUsergroups_select;

}

function categoryArray2Select($arrCategories, $incSubCategories = true) {

	$arrCategories_select = array();
	
	global $arrCategories_select;
	global $catlevel;
	
	$catlevel = 0;

	// we are trying to build an array which will put the multidimensional usergroups array into a one dimen
	// sional array, suitable for display in an HTML select.

	foreach ($arrCategories as $key=>$data) {
	
		$arrCategories_select[$data['categoryid']] = "<b>".$data['category']."</b>";
		
		if ($incSubCategories) {
		
			if (isset($data['children']))
				displayCatChildren($data);
		
		}
	
	}
	
	return $arrCategories_select;

}

function officeArray2Select($arrOffices) {

	//print_r($arrOffices);
	//die();

	$arrOffices_select = array();
	
	global $arrOffices_select;
	global $oflevel;
	
	$oflevel = 0;

	// we are trying to build an array which will put the multidimensional usergroups array into a one dimen
	// sional array, suitable for display in an HTML select.

	foreach ($arrOffices as $key=>$data) {
	
		$arrOffices_select[$data['id']] = "<b>".$data['office']."</b>";
		
		if (isset($data['children']))
			displayOfficeChildren($data);
	
	}
	
	
	return $arrOffices_select;

}

function random_str($length) {

    $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    srand((double)microtime()*1000000);
    $i = 1;
    $pass = '' ;

    while ($i <= $length) {

        $num = rand(0,strlen($chars));
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;

    }

    return $pass;

}

function createRandOpmPassword() {
				
	$pass = "";	
		
	while (!checkPwReqs($pass,false)) {
	
	  $pass = random_str(8);
	
	} 
	
	return $pass;
		

}

function checkPWReqs($pwd,$retErrorString = true) {
	
		$pwErrs = array();
		
		if(strlen($pwd) < 8)//too short
		{
			$pwErrs[] = "Password must be at least 8 characters long";
		}
		if(strlen($pwd) > 20)//too long
		{
			$pwErrs[] = "Password must be no more than 20 characters long";
		}	
		if(!preg_match("#[0-9]+#", $pwd))//at least one number
		{
			$pwErrs[] = "Password must contain at least one number";
		}
		if(!preg_match("#[a-z]+#", $pwd))//at least one letter
		{
			$pwErrs[] = "Password must contain at least one letter";
		}
		if(!preg_match("#[A-Z]+#", $pwd))//at least one capital letter
		{
			$pwErrs[] = "Password must contain at least one capital letter";
		}
		/*if(!preg_match("#\W+#", $pwd))//at least one symbol
		{
			$error = true;
		}*/
		
		
		if ($pwErrs) {
			
			$pwErrString = implode("<br />", $pwErrs);
			
			if ($retErrorString)
				return $pwErrString;
			else
				return false;
			
		} else {
			
			return true;
			
		}
	
	
}

function multi2dSortAsc(&$arr, $key){

  
  $sort_col = array();
  
  if (is_array($arr)) {
  
	  foreach ($arr as $sub) $sort_col[] = $sub[$key];
	  array_multisort($sort_col, $arr);
  
  }
  
}

function getJPEGImageXY($data) {

	$soi = unpack('nmagic/nmarker', $data);
	if ($soi['magic'] != 0xFFD8) return false;
	$marker = $soi['marker'];
	$data   = substr($data, 4);
	$done   = false;
	
	while(1) {
	        if (strlen($data) === 0) return false;
	        switch($marker) {
	                case 0xFFC0:
	                        $info = unpack('nlength/Cprecision/nY/nX', $data);
	                        return array($info['X'], $info['Y']);
	                        break;
	
	                default:
	                        $info   = unpack('nlength', $data);
	                        $data   = substr($data, $info['length']);
	                        $info   = unpack('nmarker', $data);
	                        $marker = $info['marker'];
	                        $data   = substr($data, 2);
	                        break;
	        }
	}
	
  
	
}

function printInvoiceStatus($statusName,$ucase = false) {
		
	$html = "<span class=\"inv_".str_replace(" ","_",$statusName)."\">";
	
	if ($ucase)
		$html .= strtoupper($statusName);
	else
		$html .= $statusName;
	
	$html .= "</span>";
	
	return $html;

}

function unzipFile($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true) {
  
  if ($zip = zip_open($src_file)) 
  {
    if ($zip) 
    {
      $splitter = ($create_zip_name_dir === true) ? "." : "/";
      if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
      
      // Create the directories to the destination dir if they don't already exist
      create_dirs($dest_dir);

      // For every file in the zip-packet
      while ($zip_entry = zip_read($zip)) 
      {
        // Now we're going to create the directories in the destination directories
        
        // If the file is not in the root dir
        $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
        if ($pos_last_slash !== false)
        {
          // Create the directory where the zip-entry should be saved (with a "/" at the end)
          create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
        }

        // Open the entry
        if (zip_entry_open($zip,$zip_entry,"r")) 
        {
          
          // The name of the file to save on the disk
         // $file_name = $dest_dir.zip_entry_name($zip_entry);
          $file_name = zip_entry_name($zip_entry);
          echo "FILE NAME:";
          die($file_name);
          // Check if the files should be overwritten or not
          if ($overwrite === true || $overwrite === false && !is_file($file_name))
          {
            // Get the content of the zip entry
            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            file_put_contents($file_name, $fstream );
            // Set the rights
            chmod($file_name, 0777);
            echo "save: ".$file_name."<br />";
          }
          
          // Close the entry
          zip_entry_close($zip_entry);
        }       
      }
      // Close the zip-file
      zip_close($zip);
    }
  } 
  else
  {
    return false;
  }
  
  return true;
}

/**
 * This function creates recursive directories if it doesn't already exist
 *
 * @param String  The path that should be created
 *  
 * @return  void
 */
function create_dirs($path)
{
  if (!is_dir($path))
  {
    $directory_path = "";
    $directories = explode("/",$path);
    array_pop($directories);
    
    foreach($directories as $directory)
    {
      $directory_path .= $directory."/";
      if (!is_dir($directory_path))
      {
        mkdir($directory_path);
        chmod($directory_path, 0777);
      }
    }
  }
}




?>