<?php
class Navisionimport extends CI_Controller {

	function __construct()
    {
    	
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';

    }
    
    function importPropertyCodes() {
    	
    	// match prop codes with OPM ones, update table, then create exception report.
    	
    	?>
    	
    		<html>
    			<head>
    				<title>IMPORT PROP CODES</title>
    			</head>
    			
    			<body>
    				<form name="propForm" method="post" action="<?= base_url(); ?>navisionimport/propCodeHandler">
    				
    	
    	<?
    	
    	// first we need to create an array of all properties that are already imported.
    	
    	$sql = "SELECT propertyid, nv_propid FROM properties WHERE nv_propid <> '0'";
    	$query = $this->db->query($sql);
    	
    	$importedProps = array();
    	
    	foreach ($query->result() as $r)
    		$importedProps[] = $r->nv_propid;
    		
    	$row = 1;
		
		if (($handle = fopen($this->config->item('webrootPath') . "resources/files/navision_import/navision_property_codes.csv", "r")) !== FALSE) {
		  
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

		        if (($row > 1) && (!in_array($data[0],$importedProps))) {
		        
		        	
		        	$sql = "SELECT propertyid,property FROM properties WHERE property LIKE '".addslashes(trim($data[1]))."' AND nv_propid = 0";
		        	$query = $this->db->query($sql);
		        	
		        	//print_r($query);
		        	
		        	if ($p = $query->row()) {
		        		
		        		// insert ID into DB
		        		
		        		$sql = "UPDATE properties SET nv_propid = '".$data[0]."' WHERE propertyid = " . $p->propertyid;
		        		$this->db->query($sql);
		        		
		        		
		        		$insertedItems[] = array(
		        		
		        			"dbName"=>$p->property,
		        			"csvName"=>$data[1],
		        			"dbID"=>$this->db->insert_id(),
		        			"propID"=>$data[0]
		        		
		        		);
		        	
		        	} else {
		        	
		        		// add to exception array
		        		
		        		$exceptions[] = array("id"=>$data[0],"name"=>$data[1]);
		        	
		        	}
		        	
		       	}
		   	
		   		
		   		$row++;
		   
		    }
		   
		    fclose($handle);
		    
		    // LETS MAKE GUESSES AT PROPERTY NAME AND GIVE THE USER OPTIONS!
		    
		    ?>
		    
		    <table border="1" width="600">
				<tr>
					<th>Navision Property</th>
					<th>OPM Property</th>
				</tr>
		    
		    <?
		    
		    echo "EXCEPTIONS:<br><br>";
		    
		    foreach ($exceptions as $e) {
		    
		    	$splitName = explode(" ", $e['name']);
		    	
		    	$sql = "SELECT property,propertyid FROM properties
		    			WHERE (";
		    	
		    	$splitCount = 0;
		    	
		    	foreach ($splitName as $key=>$x) {
		    		
		    		if ((strlen($x) > 1) && $x != 'the' && $x != 'The') { //  make sure we arent seaching for just a single letter
		    		
			    		$sql .= " property LIKE '%".addslashes($x)."%' OR ";
			    	
			    		$splitCount++;
		    			
		    		}
		    	
		    		
		    	
		    	}
		    	
		    	// remove last OR
		    	
		    	$sql = substr($sql, 0, strlen($sql) - 3);
		    	
		    	if ($splitCount > 0)  { // we have words to match 
		    				
			    	$sql .= ") AND nv_propid = 0";
			    	
			    	//echo $sql . "<br><br>";
			    	
			    	$matches = $this->db->query($sql);
	
			    	if ($matches->num_rows() > 0) {
			    	
			    		// print matches in table
			    		
			    		?>
			    		
			    		
			    			<tr>
			    				<th><?= $e['name'] ?></th>
			    				
			    				<td>
			    			
			    				<select name="propertyid[<?= $e['id'] ?>]">
			    					<option value="0">NO MATCH</option>
			    					
						    			<? foreach ($matches->result() as $m) { ?>
						    			
						    					<option value="<?= $m->propertyid ?>"><?= $m->property ?></option>
						    			<? } ?>
			    			
			    				</select>
			    			
			    			</td>
			    			</tr>
			    			
			    		
			    		
			    		
			    		<?
			    		
			    		//die();
			    	
			    	} else {
			    	
			    		$noMatches[] = array("name"=>$e['name'],"id"=>$e['id']);
			    	
			    	}
			    	
			    
			    } else {
			    
			    	$noMatches[] = array("name"=>$e['name'],"id"=>$e['id']);
			    
			    }
		    	
		    	
		    	
		    }
		    
		    ?>
		    		</table>
		    			<input type="submit" value="Import Codes" />
		    			
		    			<br><br>
		    			
		    			<pre>
		    			
		    				<? print_r($insertedItems); ?>	
		    
		    			</pre>
		    
		  			  </form>
    			</body>
    		</html>
		    
		    <?	
		    
		    
		
		}
    
    }
    
    function propCodeHandler() {
    
    	$propIDs = $this->input->post('propertyid');
    	
    	foreach ($propIDs as $nvid=>$opmid) {
    	
    		if ($opmid > 0) {
    		
    			$sql = "UPDATE properties SET nv_propid = " . $this->db->escape($nvid) ." WHERE propertyid = " . $this->db->escape($opmid);
    			$this->db->query($sql);
    		}
    	
    	}
    	
    	// display all OPM props with no navision id.
    	
    	echo "<p>PROPERTIES UPDATED!</p>";
    	
    	echo "<h3><a href='" . base_url() . "navisionimport/importPropertyCodes'>GO BACK TO IMPORT TOOL</a></h3>";
    	
    	echo "<p>THE FOLLOWING PROPERTIES HAVE NO NAVISION ID...</p>";
    	
    	$sql = "SELECT * FROM properties WHERE nv_propid = 0 ORDER BY property";
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $row) {
    	
    		echo $row->property . "<br><br>";
    	
    	}
    	

    
    }
    
    function importProducts() {
    
    	if (!isset($_FILES['importFile'])) { // show upload page
    	
    		$data = array();
    		
    		
	      
	        $template['page_title'] = "Import Navision Products";
	        $template['bigheader'] = "Import Navision Products";
	        $template['nav2'] = "Import Navision Products";
	        $template['content'] = $this->load->view('navisionImport/importProducts',$data,true);
	      	//$template['contentNav'] = $this->load->view('product/contentNav',$data,true);
	        //$template['rightNav'] = $this->load->view('product/rightNav',$data,true);
    		
    		$this->load->view('global/maintemplate', $template);
    	
    	} else {  // handle upload
    	
    		
    		// the format for old navision itemcodes is:
    		
    		// xxxx bandcode
    		// xxxx designcode
    		// xx size
    	
    		// get all properties and put into array, for making sure we have nav prop ids in db, and recording propertyid in import table.
    		
    		
    		$propArray = array();
    		$propQuery = $this->properties_model->fetchProperties();
    	
    		foreach ($propQuery->result() as $propRow) { // make array of properties with nv_propid
    		
    			if ($propRow->nv_propid)
    				$propArray[$propRow->nv_propid] = $propRow->propertyid;
    		
    		}

    		
    		$f = fopen($_FILES['importFile']['tmp_name'], 'r');
			
			if ($f) {
			
				$fileErrors = array();
				$lineNum = 1;
				
				while ($line = fgetcsv($f)) {
				 
				 	if (strlen($line[0]) != 10) {
				 	
				 		$fileErrors[] = "Line #" . $lineNum . " is not 10 digits - invalid SKU!";
				 	
				 	}
				 	
				 	// make sure we have nv_propid in the DB for every line.
				 	
				 	if (!isset($propArray[substr($line[0],0,4)]))
				 		$fileErrors[] = "Line #" . $lineNum . "'s propertyid is not in the database. Please correct and retry import.";
				 	
				 	$lineNum++;
				 
				}
				
				if ($fileErrors) { // we have errors, display them!!
				
					echo "FILE CONTAINED THE FOLLOWING ERRORS. PLEASE CORRECT AND TRY AGAIN.<br><br>";
				
					foreach($fileErrors as $errText) {
					
						echo $errText . "<br>";
					
					}
					
					die();
				
				}
				
				
				rewind($f); // error check complete. begin importation.
		
				$curDesignCode = "0"; // the unique id of the product, extracted from SKU
				$lineNum = 1;
				$importedProducts = 0;
		
			    while ($line = fgetcsv($f)) {  // You might need to specify more parameters
			       	
			       	$designCode = substr($line[0],4,4);			       
			       	$curSize = substr($line[0],8,2); // extracted navision size code..!	       
			       	      
					if ($designCode != $curDesignCode) { // new product, insert old and add new element
					
						// INSERT PRODUCT (if this isnt the first iteration...
						
							if (isset($curItem)) {
						
								$sql = "INSERT INTO navision_product_import (propertyid,designcode,itemcode,itemcode2,description,description2,sizes,color,bodystyle)
										VALUES(".$this->db->escape($curItem['propertyid']).",".$this->db->escape($curItem['designcode']).",".$this->db->escape($curItem['itemcode']).",".$this->db->escape($curItem['itemcode2']).",".$this->db->escape($curItem['description']).",".$this->db->escape($curItem['description2']).",".$this->db->escape($curItem['sizes']).",".$this->db->escape($curItem['color']).",".$this->db->escape($curItem['bodystyle']).")";
								
								$this->db->query($sql);
								$importedProducts++;
							
							}
						
						// CREATE NEXT NEW ONE
						
						$ts = mktime();
						
						$curItem = array(
										
							"itemcode"=>$line[0],
							"itemcode2"=>$line[1],
							"description"=>$line[2],
							"description2"=>$line[3],
							"sizes"=>$curSize,
							"color"=>$line[5],
							"bodystyle"=>$line[6],
							"designcode"=>substr($line[0],4,4),
							"propertyid"=>$propArray[substr($line[0],0,4)]
										
						);
					
					} else { // new size, just tack it on.
			       
			       		$curItem["sizes"] .= ",". $curSize;
			       
					}
			       
			        // deal with $line.
			        // $line[0] is the first column of the file
			        // $line[1] is the second cokumn
			        // ...
			        
			        
			 		$curDesignCode = $designCode;
			 		$lineNum ++;
			    
			    }
			    
			    echo "IMPORT COMPLETE<br>";
			    echo $importedProducts . " products successfully imported.<br>";

			  
			    fclose($f);
			
			} else {
			
			    // error
			
			}
    	
    	
    	}
    
    }
    
    function matchProducts($propertyID = 0, $perPage = 0, $offset = 0) {
    	
    	$this->load->model('properties_model');
    	$this->load->model('navimport_model');
    
    	$data = array();
	      
        $template['page_title'] = "Match Navision Products";
        $template['bigheader'] = "Match Navision Products";
        $template['nav2'] = "Match Navision Products";
        
        $arrJS['scripts'] = array('jquery-1.3.2.min','opm_scripts');
        //$arrJS['scripts'] = array('jquery-1.3.2.min','opm_scripts');
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        if (!$propertyID) {
        	
        	// pick property
        	
        	$data['properties'] = $this->properties_model->fetchProperties();
       		$template['content'] = $this->load->view('navisionImport/pickProperty',$data,true);
        
        } else {
        
        	// first, lets look to match itemcode2 and productcode. record all matches in design code field.
        	
        	$data['numAssigned'] = $this->navimport_model->matchProductCodes($propertyID);
        	
        	$data['totalNavProducts'] = $this->navimport_model->fetchNavisionMatches(true,null,null,$propertyID);

        
        	$this->load->library('pagination');
			$config['base_url'] = base_url().'/navisionimport/matchProducts/'.$propertyID."/".$perPage."/";
			$config['total_rows'] = $data['totalNavProducts'];
			
			// determine per page
			
			if ($perPage)
				$config['per_page'] = $perPage;
			else
				$config['per_page'] = $this->config->item('searchPerPage');
			
			$config['uri_segment'] = 5;

	
    		$this->pagination->initialize($config);
    		
    		$data['navProducts'] = $this->navimport_model->fetchNavisionMatches(false,$offset,$config['per_page'],$propertyID);
    		
    		$data['prodStart'] = $offset + 1;
    		$data['prodEnd'] = $data['prodStart'] + ($data['navProducts']->num_rows() - 1);
        
      
        	
        	// first, get property info so we can eliminate those words from search.
        	$data['p'] = $this->properties_model->fetchPropertyInfo($propertyID);
        	$propertyWords = explode(" ", strtoupper($data['p']->property));
                	
        	$data['products'] = array();
        	
        	$arrKey = 0;
        	
        	foreach ($data['navProducts']->result() as $np) {
      	
        		// lets break descriptions down to vaild search words.
        		
        		$arrDesc1 = explode(" ", strtoupper(str_replace("-"," ",$np->description)));
        		$arrDesc2 = explode(" ", strtoupper(str_replace("-"," ",$np->description2)));
        		
        		$arrDesc = array_unique(array_merge($arrDesc1,$arrDesc2));
       
        		foreach ($arrDesc as $index=>$searchWord) {
        		
        			if (in_array($searchWord, $propertyWords)) {
        		
						unset($arrDesc[$index]);
        				
        			} elseif ($searchWord == strtoupper($np->color)) {
        			
        				unset($arrDesc[$index]);
        			
        			} else {
        			
        				$badWords = array('XS','S','SM','M','LG','L','XL','T',"-"," ","","SS","MENS","SLIM","FIT","BLK","JR","GIRLS","CREW","NECK",$np->itemcode2);
        				
        				if (in_array($searchWord,$badWords)) {
        				
        					unset($arrDesc[$index]);
        				
        				}
        			
        			}
        				
        		
        		}
        		       
        		$data['products'][$arrKey] = $np;
        		
        		// find matches
        		
        		$data['products'][$arrKey]->matches = $this->products_model->findProductMatches($propertyID,$arrDesc);
					
				$arrKey++;
        	}
        	
        	$template['contentNav'] = $this->load->view('navisionImport/searchNav',$data,true);
			$template['content'] = $this->load->view('navisionImport/matchProducts',$data,true);
        
        }
        
		
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function assignDesignCode() {
    
    	$designCode = $this->input->post('designcode');
    	$opm_productid = $this->input->post('opm_productid');
    	$propertyid = $this->input->post('propertyid');
    	
    	// first check if opm_productid belongs to correct property
    	
    	if ($this->products_model->checkDesignCode($opm_productid,$propertyid)) {
    	
    		if ($this->products_model->assignDesignCode($opm_productid,$designCode)) {
    		
    			die("success");
    		
    		} else {
    		
    			die("Error: Could not assign design code.");
    		
    		}
    	
    	} else {
    	
    		die("Error: ProductID entered does not match property.");
    	
    	}
    	
    	
    }
    
    function checkInvoiceCodes() {
    
    	$arrErrors = $this->opm->checkInvoiceCodes();
    	
    	echo "<pre>";
    		print_r($arrErrors);
    	echo "</pre>";
    	
    	die();
    
    
    }
    
}

?>