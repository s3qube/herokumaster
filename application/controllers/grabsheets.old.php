<?php
class Grabsheets extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->model('categories_model');
    	$this->load->model('grabsheets_model');
    	$this->load->helper('text');
    	$this->opm->opmInit();
    	$this->opm->activeNav = 'products';
    	
    	if ($this->config->item('debugMode') == true)
    		$this->output->enable_profiler(TRUE);
    	
    	global $searchArgList;
    	$searchArgList = array("propertyid","grabsheetgroupid","grabsheettemplateid","offset");
    	
    }
    
    function getThumbs($productlineid)
    {
 
		$sql = "SELECT opm_images.imageid
				FROM opm_products_productlines
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_products_productlines.opm_productid
				LEFT JOIN opm_images ON opm_images.opm_productid = opm_products.opm_productid
				WHERE opm_products_productlines.productlineid = ".$this->db->escape($productlineid)."
				AND opm_images.imageid IS NOT NULL";
				
		$data['thumbnails'] = $this->db->query($sql);
    	
    	$this->load->view('grabsheets/getThumbs',$data);	
    
    }
    
    function getThumbInfo($imageid) {
    
    	// get property name, product name, approval status?
    	
    	$sql = "SELECT opm_products.productname, properties.property
    			FROM opm_images
    			LEFT JOIN opm_products ON opm_products.opm_productid = opm_images.opm_productid
    			LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
    			WHERE opm_images.imageid = " . $this->db->escape($imageid);
    			
    	$query = $this->db->query($sql);
    	$row = $query->row();
		
    	echo $row->property ." // ".$row->productname;
   
    }
    
    function getProductlines($propertyid)
    {
    
    	$this->load->model('productlines_model');
    
    	// take propertyid and fetch associated productlines
    	
    	$data['productLines'] = $this->productlines_model->fetchProductLines($propertyid);
 
    	$this->load->view('grabsheets/productLineSelect',$data);
    
    }
    
    function search($propertyid = 0, $grabsheetgroupid = 0, $grabsheettemplateid = 0, $offset = 0) {
		
		global $searchArgList;
	
		if (checkPerms('can_view_grabsheets',true)) {

			$this->load->model('properties_model');
			
			$data['grabsheetGroups'] = $this->grabsheets_model->fetchGrabsheetGroups();
    		$data['grabsheetTemplates'] = $this->grabsheets_model->fetchGrabsheetTemplates();
    		$data['properties'] = $this->properties_model->fetchProperties();

	
			
	   
			$template['page_title'] = "Search Grabsheets";
			$template['bigheader'] = "Search Grabsheets";
			$template['nav2'] = "Search Grabsheets";
			$data['totalGrabsheets'] = $this->grabsheets_model->fetchGrabsheets(true,null,null,$propertyid,$grabsheetgroupid,$grabsheettemplateid);
			
			
			foreach ($searchArgList as $k=>$d) 
				$data['args'][$d] = ${$d};
			
			
			$this->load->library('pagination');
			$config['base_url'] = base_url().'/grabsheets/search/'.$propertyid."/".$grabsheetgroupid."/".$grabsheettemplateid."/";
			$config['total_rows'] = $data['totalGrabsheets'];
			$config['per_page'] = '20';
			$config['uri_segment'] = 6;
			//$config['full_tag_open'] = '<p>';
			//$config['full_tag_close'] = '</p>';
	
			$this->pagination->initialize($config);
			
			$data['grabsheets'] = $this->grabsheets_model->fetchGrabsheets(false,$offset,$config['per_page'],$propertyid,$grabsheetgroupid,$grabsheettemplateid);

			$data['prodStart'] = $offset + 1;
			$data['prodEnd'] = $data['prodStart'] + ($data['grabsheets']->num_rows() - 1);
			
			$template['rightNav'] = $this->load->view('grabsheets/rightNav',$data,true);
			$template['searchArea'] = $this->load->view('grabsheets/search_searchArea',$data,true);
			$template['contentNav'] = $this->load->view('grabsheets/searchNav',$data,true);
			$template['content'] = $this->load->view('grabsheets/search',$data,true);
			
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','tipsx3'); // 
			$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
				
			$this->load->view('global/maintemplate', $template);
			$this->output->enable_profiler(TRUE);
		}    
    }

	
	function create()
   	{

    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['grabsheetGroups'] = $this->grabsheets_model->fetchGrabsheetGroups();
    	$data['grabsheetTemplates'] = $this->grabsheets_model->fetchGrabsheetTemplates();
    	
    	$template['searchArea'] = $this->load->view('grabsheets/searchArea',$data,true);
    	
    	// $template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	// $template['content'] = $this->load->view('search/search',$data,true);

   	
     	$template['content'] = '';
     	$template['headInclude'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."resources/grabsheetStyles.css\">";       
        $template['page_title'] = "Create Grabsheet";
        $template['bigheader'] = "Create Grabsheet";
        $template['nav2'] = "Create Grabsheet";
        
        
        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','grabsheetDragDrop','tipsx3'); 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        $template['content'] = $this->load->view('grabsheets/create',$data,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function save() {
    
    	if (checkPerms('can_create_grabsheets',true)) { // check permissions
			
			$errors = "";	
		
			if (!$postdata['grabsheetgroupid'] = $this->input->post('grabsheetgroupid'))
				$errors .= "Grabsheet has no Group!<br />";
			
			if (!$postdata['title'] = $this->input->post('title'))
				$errors .= "Grabsheet has no Title!<br />";
				
			if (!$postdata['grabsheettemplateid'] = $this->input->post('grabsheettemplateid'))
				$errors .= "Grabsheet has no Template!<br />";
				
			if (!$postdata['itemids'] = $this->input->post('itemids'))
				$errors .= "Grabsheet appears to be empty!<br />";
				
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
			
			$postdata['property_imageid'] = $this->input->post('property_imageid');
			
			
			if ($userid = $this->grabsheets_model->saveGrabsheet($postdata)) {
			
				
				$this->opm->displayAlert("Grabsheet Successfully Created!","/grabsheets/search");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("Could Not Save Grabsheet!");
				return true;
			}
		
		}		
			
	}
	
	function submit() { // construct a search url from a form submission
	
		if ($this->input->post('createGroup')) { // we are creating a new grabsheet group!
		
			if($this->input->post('newGroupName')) {
				
				if($this->grabsheets_model->createGroup($this->input->post('newGroupName'))) {
				
					$this->opm->displayAlert("Group Successfully Created!","/grabsheets/search");
					return true;	
				
				} else {
				
					$this->opm->displayError("Could not create group!");
					return true;
				
				}
				
			
			} else {
			
				$this->opm->displayError("You didn't enter a Group Name!");
				return true;
			
			}
		
		}   
    
    	global $searchArgList;
    	
    
		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/grabsheets/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);

    
    }
    
    function view($grabsheetid, $download = false) {
    
		// GET GENERAL DB INFO FOR PDF
		
		$sql = "SELECT *
				FROM opm_grabsheet
				WHERE opm_grabsheet.grabsheetid = '".$grabsheetid."'";
				
		$result = $this->db->query($sql);
		$grabsheetinfo = $result->row_array();
		
		$sql = "SELECT *
				FROM opm_productlines
				LEFT JOIN properties ON properties.propertyid = opm_productlines.propertyid
				WHERE opm_productlines.productlineid = '".$grabsheetid."'";
				
		$result = mysql_query($sql) or die(mysql_error());
		$productline = mysql_fetch_array($result);
		
		// GET ALL IMAGES
		$sql = "SELECT opm_products.*,opm_images.image,opm_images.image_label,opm_images.imageid,properties.property
				FROM opm_grabsheet_detail
				LEFT JOIN opm_images ON opm_images.imageid = opm_grabsheet_detail.imageid
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_images.opm_productid
				LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
				WHERE opm_grabsheet_detail.grabsheetid = '".$grabsheetid."'
				ORDER BY opm_grabsheet_detail.displayorder
				";
		
		$products = $this->db->query($sql);

		$this->load->library('fpdf');		
	
		if ($grabsheetinfo['grabsheettemplateid'] == '1')
			define('GRABSHEET_TITLE',$grabsheetinfo['grabsheettitle']);
		else if ($grabsheetinfo['grabsheettemplateid'] == '2')
			define('GRABSHEET_TITLE',"");
		
		define('GRABSHEET_IMAGEPATH',$this->config->item('webrootPath')."resources/images/bravadopdflogo.jpg");
		
		// if we have a property image to display, define it's path!
		
		if ($grabsheetinfo['property_imageid']) {
		
			$sql = "SELECT * FROM properties WHERE propertyid = " . $this->db->escape($grabsheetinfo['property_imageid']);
			$result = $this->db->query($sql);
			$property_imageinfo = $result->row_array();
			
			if ($property_imageinfo['image_path']) {
			
				define('GRABSHEET_PROPERTYIMAGEPATH',$this->config->item('webrootPath')."resources/files/propertyimages/" . $property_imageinfo['image_path']);
			
			} else {
			
				define('GRABSHEET_PROPERTYIMAGEPATH',false);
			
			}
			
		} else {
		
			define('GRABSHEET_PROPERTYIMAGEPATH',false);
		
		}

		
		$pdf=new PDF('L','pt','Letter');

		define('FPDF_FONTPATH',$this->config->item('webrootPath')."resources/fpdf_font/");
		
		$pdf->AliasNbPages();
		//$pdf=new MEM_IMAGE();
		
		if ($grabsheetinfo['grabsheettemplateid'] == '1') 
			$pdf->AddPage();
		
		
		//$pdf->Image('../opm/images/500x500img.jpg',10,40,250);
		
		
		// control vars
		
		$imgsperpage = 8;
		$imgsperrow = 4;
		$imgpaddingx = 12;
		$imgpaddingy = 45;
		$imgwidth = 178;
		$imgheight = 178;
		$startx = 20;
		$starty = 100;
		
		// init
		
		$rowimgnum = 1;
		$pageimgnum = 1;
		$imgnum = 1;
		$xpos = $startx;
		$ypos = $starty;
		
		$pdf->SetFont('Arial','',10);
		$pdf->SetTextColor(0,0,0);
		
		foreach ($products->result_array() as $product) {
		
			if ($grabsheetinfo['grabsheettemplateid'] == '1') {
		
				if (!$product['imageid']) {
				
					$this->opm->displayError("This grabsheet contains links to images which have been deleted from the OPM system.<br>Please go back and edit the grabsheet to remove the deleted images.");
					return false;
				
				}
			
				if ($pageimgnum <= $imgsperpage) {
					$pageimgnum++;
				} else {
					$ypos = $starty;
					$xpos = $startx;
					$pageimgnum = 2;
					$pdf->AddPage();
				}
				
				// save temp file for image and display!
				$file = $this->config->item('webrootPath') . "resources/images/temp/".$product['imageid'].".jpg"; 
				$data = $product['image'];   
				$file_handle = fopen($file,"a");  
				fwrite($file_handle, $data);     
				fclose($file_handle);
				
				//die("file");
						
				$pdf->Image($file,$xpos,$ypos,$imgwidth,$imgheight,'JPEG');
				unlink($file);
				
				// create text string and display!
				
				$producttext = $product['productname'];
					
				$pdf->SetFont('Arial','B',9);
				$pdf->Text($xpos,$ypos+$imgheight+14,$producttext);
				
				// print create date
				
				//$pdf->SetFont('Arial','',9);
				//$pdf->Text($xpos,$ypos+$imgheight+26,"Create Date:");	
				$pdf->SetFont('Arial','B',9);
				$pdf->Text($xpos,$ypos+$imgheight+26,$product['productcode']);
			
				// do stuff to display stuff properly
			
					if ($rowimgnum < $imgsperrow) {
					
						// go to next column
						$xpos += ($imgwidth + $imgpaddingx);
						$rowimgnum++;
						
					} else {
					
						// go to next row;
						$xpos = $startx;
						$ypos += ($imgheight + $imgpaddingy);
						$rowimgnum = 1;
					}
			
			} else if ($grabsheetinfo['grabsheettemplateid'] == '2') {
			
				$pdf->AddPage();
	
				// print top text
				$pdf->SetFont('Arial','B',17);
				$headertext = strtoupper($product['property'] . " : " . $product['productname']);
				$pdf->Text(20,55,$headertext);
			
				// save temp file for image and display!
				$file = $this->config->item('webrootPath') . "resources/images/temp/".$product['imageid'].".jpg";
				$data = $product['image'];   
				$file_handle = fopen($file,"a");  
				fwrite($file_handle, $data);     
				fclose($file_handle);
				
				//die("ello");
						
				$pdf->Image($file,185,90,425,425,'JPEG');
				unlink($file);
				
				// print comments if they exist
				
				if (isset($product['comments'])) {
				
					$pdf->SetFont('Arial','',12);
					$pdf->Text(20,550,"Comments: " . $product['comments']);
				
				}
				
				
				// print grabsheetttile
				
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(20,585,$grabsheetinfo['grabsheettitle']);
			
			}
			
		
		}
		
		if (isset($property_imageinfo['property'])) {
			
			$strProp = $property_imageinfo['image_path'] . "-";
			
		} else {
		
			$strProp = "";
		
		}
		
		$filename = "Bravado_" . $strProp . $grabsheetinfo['grabsheettitle'] . ".pdf";
		$filename = str_replace(' ','_',$filename);
		$filename = str_replace('/','_',$filename);
		
		if ($download) {			
			$pdf->Output($filename,'D');
		} else {
			$pdf->Output();
		}
    
    
    }
    
}

?>