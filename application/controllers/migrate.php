<?php
class Migrate extends CI_Controller {

	function __construct()
    {
    	
    	parent::__construct();

    }
    
    function disableProps() {
    
    	
    	$row = 1;
    	
		if (($handle = fopen("/var/www/html/resources/testing/disable_props.csv", "r")) !== FALSE) {
		    
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		
		        $num = count($data);
		        echo "<p> $num fields in line $row: <br /></p>\n";
		        $row++;
		
		        for ($c=0; $c < $num; $c++) {
		
		           // echo "property:" . $data[$c] . "<br />\n";
		            
		            $sql = "SELECT * FROM properties WHERE property LIKE '%".$data[$c]."%'";
		            
		            $result = $this->db->query($sql);
			
			
			
					if ($result->num_rows() > 0) {
					
						$prop = $result->row();
						echo "found prop for " . $prop->property . "<br>";
						
						// delete all rows from opm_products_usergroups for that property!
						
						
						
						$sql = "DELETE opm_products_usergroups
								FROM opm_products_usergroups
								LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_usergroups.opm_productid
								LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
								WHERE properties.propertyid = " . $prop->propertyid;
						
						if ($result = $this->db->query($sql))
							echo $prop->property . " rows deleted! <br>";
						
						
					} else {
						
						echo "no prop found for " . $prop->property . "<br>";
						
					}
		            
		           // echo $sql . "<br />\n";

		
		        }
		
		    }
		
		    fclose($handle);
		
		}
    	
    
    }
    
    /*
    function reverseOzzy() {
	    
	     $this->load->model('approvalstatus_model');
	     
	     $sql = "SELECT * FROM opm_products WHERE propertyid = 1252 AND (approvalstatusid = 1 OR approvalstatusid = 2) AND deletedate = 0";
	     $query = $this->db->query($sql);
	     
	     foreach($query->result() as $p) {
		     
		     
		     $sql = "SELECT opm_approvalstatuses.approvalstatus,users.username
    				FROM opm_approvalstatus
    				LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_approvalstatus.approvalstatusid
    				LEFT JOIN users ON users.userid = opm_approvalstatus.userid
    				WHERE opm_approvalstatus.opm_productid = '".$p->opm_productid."' AND opm_approvalstatus.userid = 247";
    				
			$result = $this->db->query($sql);
			
			
			
			if ($result->num_rows() > 0) {
			
			
				echo " reversing:" . $p->opm_productid . "<br>";
				
				$row = $result->row();
				
				$sql = "DELETE FROM opm_approvalstatus WHERE opm_productid = '".$p->opm_productid."' AND userid = 247";
				$this->db->query($sql);
				
				// add history entry
						
				$this->opm->addHistoryItem($p->opm_productid, $row->approvalstatus . " by " . $row->username . " REVERSED BY Charles Dooher (automated)");
				
				//update overall product status
				
				$this->approvalstatus_model->updateApprovalStatus($p->opm_productid);
				
				//return true;
				
				
			
			} else {
			
				// do nothing
			}
		 
		 	//die("just did one");    
		     
	     }
	    
	    
    }
    
    function reverseOzzySamples() {
	    
	     echo "about to reverse samples"; 
	    
	     $this->load->model('approvalstatus_model');
	     
	     $sql = "SELECT * FROM opm_products WHERE propertyid = 1252 AND approvalstatusid = 1 and deletedate = 0";
	     $query = $this->db->query($sql);
	     
	     foreach($query->result() as $p) {
		     
		     echo "updating:" . $p->opm_productid;
		     
		     $this->approvalstatus_model->updateApprovalStatus($p->opm_productid);

		     /*
		     $sql = "UPDATE opm_products SET sampleappstatusid = 0 WHERE opm_productid = " . $p->opm_productid;
			 $result = $this->db->query($sql);
									
			 $this->opm->addHistoryItem($p->opm_productid, "Sample Approval REVERSED BY Charles Dooher (bulk)");
				  
		     
	     }
	    
	    
    }
    
    function checkDFArchive() {
    
    	//echo "<pre>";
    	
    	error_reporting(E_ALL);
    
    	$sql = "
    	
    		SELECT opm_masterfiles.*
			FROM opm_masterfiles
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_masterfiles.opm_productid
			WHERE opm_products.propertyid = 602 
			";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $f) {
    	
    		echo $f->fileid . "<br>";
    	
    	}
    	
    }
    
    */
    
    function createAICArchive() {
    
    	//echo "<pre>";
    	
    	error_reporting(E_ALL);
    
    	$sql = "
    	
    		SELECT opm_masterfiles.*
			FROM opm_masterfiles
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_masterfiles.opm_productid
			WHERE opm_products.propertyid = 494 AND opm_products.approvalstatusid IN (1,2) 
			AND opm_masterfiles.archivedate = 0";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $f) {
    	    	
    		$file = '/files/masterfiles/' . $f->fileid;
    		$newfile = '/files/temp/AIC_ARCHIVE/masterfiles/' . $f->filename;

    		if (copy($file, $newfile)) {
	    	
	    		echo "copied $file to $newfile\n";
				$sql = "UPDATE opm_masterfiles SET archivedate = '".mktime()."' WHERE fileid = " . $f->fileid;
				$this->db->query($sql);
	    	
	    	} else {
		    	
		    	echo "FAILED to copy $file to $newfile\n";
		    	
	    	}
	    	
	    	//die();
    	
    	}
    	
    	$sql = "
    	
    		SELECT opm_separations.*
			FROM opm_separations
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_separations.opm_productid
			WHERE opm_products.propertyid = 494 AND opm_products.approvalstatusid IN (1,2)
			AND opm_separations.archivedate = 0";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $f) {
    	
    		$file = '/files/separations/' . $f->fileid;
    		$newfile = '/files/temp/AIC_ARCHIVE/separations/' . $f->filename;

    		if (copy($file, $newfile)) {
	    	
	    		echo "copied $file to $newfile\n";
				$sql = "UPDATE opm_separations SET archivedate = '".mktime()."' WHERE fileid = " . $f->fileid;
				$this->db->query($sql);
				
	    	} else {
		    	
		    	echo "FAILED to copy $file to $newfile\n";
		    	
	    	}
    	
    	
    	}
    	
    	die("ALL DONE!!");
    
    }
    
    function createArchiveByProduct() {
    
    	//echo "<pre>";
    	
    	$propertyID = 1021;
    	$folderName = "LIR_ARCHIVE/";
    	$folderPath = "/files/temp/";
    	
    	error_reporting(E_ALL);
    	
    	// first create main folder
    	
    	if (!file_exists($folderPath.$folderName)) {
	    		
    		if (!mkdir($folderPath.$folderName, 0777, true)) {
			    die('Failed to create folder ' . $folderPath.$folderName);
			}
    		
    		
		}
    	
    
    	$sql = "
    	
    		SELECT opm_masterfiles.*, properties.property, opm_products.productname
			FROM opm_masterfiles
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_masterfiles.opm_productid
			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
			WHERE opm_products.propertyid = ".$propertyID." AND opm_products.approvalstatusid IN (1,2) 
			AND opm_masterfiles.archivedate = 0"; //
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $f) {
    	
    			// create valid, human readable folder name
    	
    			$prodFolderName = $f->opm_productid . "_" . $f->property. "_" . $f->productname;
	    		
	    		$prodFolderName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $prodFolderName);
				// Remove any runs of periods (thanks falstro!)
				$prodFolderName = mb_ereg_replace("([\.]{2,})", '', $prodFolderName);
    	
    	
    		// create folder for product if doesn't exist
    		
    		if (!file_exists($folderPath.$folderName.$prodFolderName)) {
	    		
	    		
	    		if (!mkdir($folderPath.$folderName.$prodFolderName, 0777, true)) {
				    die('Failed to create folder ' . $folderPath.$folderName.$prodFolderName);
				}
	    		
	    		
    		}
    			
    		
    			// create mf folder for product ID if doesn't exist
    	    	
	    	    if (!file_exists($folderPath.$folderName.$prodFolderName."/masterfiles")) {
		    	    
		    		if (!mkdir($folderPath.$folderName.$prodFolderName."/masterfiles", 0777, true)) {
				    	die('Failed to create folder ' . $folderPath.$folderName.$prodFolderName."/masterfiles");
					}  
		    	    
	    	    }
	    	    	
		    		$file = '/files/masterfiles/' . $f->fileid;
		    		$newfile = $folderPath.$folderName.$prodFolderName."/masterfiles/" . $f->filename;
					
					
					if (file_exists($file)) {
						
						if (copy($file, $newfile)) {
			    	
				    		echo "copied $file to $newfile\n";
							$sql = "UPDATE opm_masterfiles SET archivedate = '".mktime()."' WHERE fileid = " . $f->fileid;
							$this->db->query($sql);
				    	
				    	} else {
					    	
					    	echo "FAILED to copy $file to $newfile\n";
					    	
				    	}
						
					} else {
						
						echo "file " . $file . "doesn't exist.";
						
					}
		    		
		    	
		    	
		    	
		    
	    	
	    	//die();
    	
    	}
    	
    	$sql = "
    	
    		SELECT opm_separations.*, properties.property, opm_products.productname
			FROM opm_separations
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_separations.opm_productid
			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
			WHERE opm_products.propertyid = ".$propertyID." AND opm_products.approvalstatusid IN (1,2) 
			AND opm_separations.archivedate = 0"; //
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $f) {
    	
    			// create valid, human readable folder name
    	
    			$prodFolderName = $f->opm_productid . "_" . $f->property. "_" . $f->productname;
	    		
	    		$prodFolderName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $prodFolderName);
				// Remove any runs of periods (thanks falstro!)
				$prodFolderName = mb_ereg_replace("([\.]{2,})", '', $prodFolderName);
    	
    	
    		// create folder for product if doesn't exist
    		
    		if (!file_exists($folderPath.$folderName.$prodFolderName)) {
	    		
	    		
	    		if (!mkdir($folderPath.$folderName.$prodFolderName, 0777, true)) {
				    die('Failed to create folder ' . $folderPath.$folderName.$prodFolderName);
				}
	    		
	    		
    		}
    			
    		
    			// create mf folder for product ID if doesn't exist
    	    	
	    	    if (!file_exists($folderPath.$folderName.$prodFolderName."/separations")) {
		    	    
		    		if (!mkdir($folderPath.$folderName.$prodFolderName."/separations", 0777, true)) {
				    	die('Failed to create folder ' . $folderPath.$folderName.$prodFolderName."/separations");
					}  
		    	    
	    	    }
	    	    	
		    		$file = '/files/separations/' . $f->fileid;
		    		$newfile = $folderPath.$folderName.$prodFolderName."/separations/" . $f->filename;
					
					
					if (file_exists($file)) {
						
						if (copy($file, $newfile)) {
			    	
				    		echo "copied $file to $newfile\n";
							$sql = "UPDATE opm_separations SET archivedate = '".mktime()."' WHERE fileid = " . $f->fileid;
							$this->db->query($sql);
				    	
				    	} else {
					    	
					    	echo "FAILED to copy $file to $newfile\n";
					    	
				    	}
						
					} else {
						
						echo "file " . $file . "doesn't exist.";
						
					}
		    		
		    	
		    	
		    	
		    
	    	
	    	//die();
    	
    	}
    	
    	/*$sql = "
    	
    		SELECT opm_separations.*
			FROM opm_separations
			LEFT JOIN opm_products ON opm_products.opm_productid = opm_separations.opm_productid
			WHERE opm_products.propertyid = ".$propertyID." AND opm_products.approvalstatusid IN (1,2)
			AND opm_separations.archivedate = 0";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $f) {
    	
    		$file = '/files/separations/' . $f->fileid;
    		$newfile = '/files/temp/AIC_ARCHIVE/separations/' . $f->filename;

    		if (copy($file, $newfile)) {
	    	
	    		echo "copied $file to $newfile\n";
				$sql = "UPDATE opm_separations SET archivedate = '".mktime()."' WHERE fileid = " . $f->fileid;
				$this->db->query($sql);
				
	    	} else {
		    	
		    	echo "FAILED to copy $file to $newfile\n";
		    	
	    	}
    	
    	
    	}*/
    	
    	die("ALL DONE!!");
    
    }
    
    
    function removeLicCheckbox() {
	    
	    // licensee group id = 231
	    // YS propertyid = 1144
	    
	    $sql = "SELECT opm_products_usergroups.id,opm_products_usergroups.opm_productid
	    		FROM opm_products_usergroups
	    		LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_usergroups.opm_productid
	    		WHERE opm_products.propertyid = 1143 AND opm_products_usergroups.usergroupid = 231";
	    
	    $query = $this->db->query($sql);
	    
	    foreach ($query->result() as $r) {
		    
		    $sql = "DELETE FROM opm_products_usergroups WHERE id = " . $r->id;
		    
		    if ($this->db->query($sql)) {
			    
			    echo $sql . " COMPLETED <br>";
			    
		    }
		    
	    }
	    
	    die("hiii");
	    
    }
    
    /*function addIntUmgGroup() {
	    
	    $sql = "SELECT DISTINCT opm_productid FROM opm_products_usergroups WHERE usergroupid = 1";
	    $query = $this->db->query($sql);
	    
	    foreach ($query->result() as $p) {
		    
		    $sql = "INSERT INTO opm_products_usergroups (opm_productid,usergroupid)
		    		VALUES (".$this->db->escape($p->opm_productid).",504)";
		    		
		    $this->db->query($sql);
		    
	    }
	    
	    echo "DONE! NUMROWS:". $query->num_rows();
	    
	    
    }*/
    
    /*function licPrefCheck() {
    	
    	$this->load->model('usergroups_model');
    
    	// get all lic ugs
    	
    	$licUGids = $this->usergroups_model->getChildren($this->config->item('licenseeGroupID'));
    	$strUGids = implode(",",$licUGids);
    	
    	$sql = "SELECT userid,usergroupid,usergroupid2 FROM users WHERE usergroupid IN (".$strUGids.") OR usergroupid2 IN (".$strUGids.")";
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $u) {
	    	
	    	$sql = "INSERT INTO opm_user_preferences (userid,prefid) VALUES (".$u->userid.",12)";	
	    	$this->db->query($sql);    	
    	
	    	echo $sql . " COMPLETED!<br>";
    	}
    	
    	
    
    }*/
    
    /*
    function prodLicenseeCheck() {
    	
    	$this->load->model('usergroups_model');
    
    	// get all lic ugs
    	
    	$licUGids = $this->usergroups_model->getChildren($this->config->item('licenseeGroupID'));
    	$strUGids = implode(",",$licUGids);
    	
    	$sql = "SELECT userid,usergroupid,usergroupid2 FROM users WHERE usergroupid IN (".$strUGids.") OR usergroupid2 IN (".$strUGids.")";
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $u) {
	    	
	    	$uids[] = $u->userid;	    	
    	}
    	
    	$strUids = implode(",",$uids);
    	
    	$sql = "SELECT opm_products.opm_productid, users.userid, users.usergroupid,users.usergroupid2
    			FROM opm_products 
    			LEFT JOIN users ON users.userid = opm_products.createdby
    			WHERE opm_products.createdby IN (".$strUids.")";
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $p) {
	    	
	    	// which usergroup of user is licensee group?
	    	$usergroupid = 0;
	    	
	    	if (in_array($p->usergroupid, $licUGids)) {
		    	
		    	$usergroupid = $p->usergroupid;
		    	
	    	} else if (in_array($p->usergroupid2, $licUGids)) {
		    	
		    	$usergroupid = $p->usergroupid2;
		    	
	    	}
	    	
	    	if ($usergroupid) {
		    	
		    	$sql ="SELECT * FROM opm_products_licensees WHERE opm_productid = ".$p->opm_productid." AND usergroupid = " . $this->db->escape($usergroupid);
		    	$query2 = $this->db->query($sql);
		    	
		    	if ($query2->num_rows() == 0) {
			    	
			    	//echo "adding usergroupid " . $usergroupid . " to product # " . $p->opm_productid . "<br>";
			    	
			    	$sql = "INSERT INTO opm_products_licensees (opm_productid,usergroupid) VALUES (".$p->opm_productid.", ".$usergroupid.")";
			    	
			    	$this->db->query($sql);
			    	
			    	echo $sql . " COMPLETED! <br>";
			    	
		    	}
		    	
	    	}
	    	
	    		    	
    	}
    	
    	
    	echo "hi<pre>";
    	//print_r($prods);
    
    }*/
    
}


?>