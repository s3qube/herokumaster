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
    	
    	//if ($this->config->item('debugMode') == true)
    	//	$this->output->enable_profiler(TRUE);
    	
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
				AND opm_images.imageid IS NOT NULL
				";
				
		$data['thumbnails'] = $this->db->query($sql);
    	
    	$this->load->view('grabsheets/getThumbs',$data);	
    
    }
    
    function getThumbsJSON($propertyid,$productlineid,$searchText,$approvalStatusID,$opmproductid,$productcode,$designerid,$categoryid,$usergroupid,$pageNum = 1,$perPage = 15)
    {
    
    	$this->load->model('images_model');

    	// pagination nation
    	
    	$offset = ($pageNum - 1) * $perPage;

		// mo pagination
		
		//if (isset($results->wines)) { // snooth gave us a result!
		

		$data['totalThumbnails'] = $this->images_model->getThumbs(true,$propertyid,$productlineid,$searchText,$approvalStatusID,$opmproductid,$productcode,$designerid,$categoryid,$usergroupid);	
		$data['thumbnails'] = $this->images_model->getThumbs(false,$propertyid,$productlineid,$searchText,$approvalStatusID,$opmproductid,$productcode,$designerid,$categoryid,$usergroupid,$perPage,$offset);
		$data['perPage'] = $perPage;
		$data['pageNum'] = $pageNum;
		$data['numPages'] = ceil($data['totalThumbnails']  / $perPage);
    	
    	$this->load->view('grabsheets/getThumbsJSON',$data);	
    
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
			
			$arrJS['scripts'] = array('jquery-1.3.2.min','opm_scripts','tipsx3'); // 
			$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
				
			$this->load->view('global/maintemplate', $template);
			$this->output->enable_profiler(TRUE);
		}    
    }

	
	function create($id = 0, $copy = false) // an id is passed if we are editing. // copy means edit but save as new...
   	{
   		$this->load->model('approvalstatus_model');

   		$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['grabsheetGroups'] = $this->grabsheets_model->fetchGrabsheetGroups();
    	$data['grabsheetTemplates'] = $this->grabsheets_model->fetchGrabsheetTemplates();
    	$data['approvalStatuses'] = $this->approvalstatus_model->fetchApprovalStatuses();
    	$data['headerImages'] = $this->grabsheets_model->fetchHeaderImages();
	    $data['designers'] = $this->users_model->fetchDesigners(true,null,true,true);
		$data['categories'] = $this->categories_model->fetchCategories();

    	
    	$data['copy'] = $copy;
    	
    	if ($id)
    		$data['grabsheet'] = $this->grabsheets_model->fetchGrabsheetInfo($id);
    		
    	if (isset($data['grabsheet']) && $data['grabsheet']->isfile && $copy != true) {
    	
    		$this->opm->displayError("This Grabsheet cannot be edited, as it is saved permanently.");
			return true;
    	
    	}
    		
    		
    	/*echo "<pre>";
    	print_r($data['grabsheet']);
    	echo "</pre>";
    	die();*/
    	
    	
    	// $template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	// $template['content'] = $this->load->view('search/search',$data,true);

   	
     	$template['content'] = '';
     	$template['headInclude'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."resources/grabsheetStyles.css\"><link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."resources/smoothbox.css\">";       
       
       	if (!$id) {
       	
	        $template['page_title'] = "Create Grabsheet";
	        $template['bigheader'] = "Create Grabsheet";
	        $template['nav2'] = "Create Grabsheet";
	        
	        $data['grabsheet'] = new stdClass();
	        
	        $data['grabsheet']->grabsheettemplateid = 0;
	        $data['grabsheet']->property_imageid = 0;
	        $data['grabsheet']->grabsheetgroupid = 0;
	        $data['grabsheet']->showproductcodes = 0;
	        $data['grabsheet']->headerimageid = 1;
	        
       	} else {
       		
       		$template['page_title'] = "Edit Grabsheet -" . $data['grabsheet']->grabsheettitle;
	        $template['bigheader'] = "Edit Grabsheet - " . $data['grabsheet']->grabsheettitle;
	        $template['nav2'] = "Edit Grabsheet - " . $data['grabsheet']->grabsheettitle;
       	
       	}
       	
       	
       	$template['searchArea'] = $this->load->view('grabsheets/searchArea',$data,true);

        
        
        $arrJS['scripts'] = array('mootools-1.2.4-core-nc','mootools-1.2-more','opm_scripts','grabsheetDragDrop2','smoothbox'); 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        $template['content'] = $this->load->view('grabsheets/create',$data,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function create2($id = 0, $copy = false) // an id is passed if we are editing. // copy means edit but save as new...
   	{
   		$this->load->model('approvalstatus_model');

    	$data['properties'] = $this->properties_model->fetchProperties();
    	$data['grabsheetGroups'] = $this->grabsheets_model->fetchGrabsheetGroups();
    	$data['grabsheetTemplates'] = $this->grabsheets_model->fetchGrabsheetTemplates();
    	$data['approvalStatuses'] = $this->approvalstatus_model->fetchApprovalStatuses();
    	$data['headerImages'] = $this->grabsheets_model->fetchHeaderImages();
    	
    	$data['copy'] = $copy;
    	
    	if ($id)
    		$data['grabsheet'] = $this->grabsheets_model->fetchGrabsheetInfo($id);
    		
    	if (isset($data['grabsheet']) && $data['grabsheet']->isfile && $copy != true) {
    	
    		$this->opm->displayError("This Grabsheet cannot be edited, as it is saved permanently.");
			return true;
    	
    	}
    		
    		
    	/*echo "<pre>";
    	print_r($data['grabsheet']);
    	echo "</pre>";
    	die();*/
    	
    	
    	// $template['contentNav'] = $this->load->view('search/searchNav',$data,true);
    	// $template['content'] = $this->load->view('search/search',$data,true);

   	
     	$template['content'] = '';
     	$template['headInclude'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."resources/ui-lightness/jquery-ui-1.10.2.custom.min.css\">";       
       
       	if (!$id) {
       	
	        $template['page_title'] = "Create Grabsheet";
	        $template['bigheader'] = "Create Grabsheet";
	        $template['nav2'] = "Create Grabsheet";
	        
	        $data['grabsheet']->grabsheettemplateid = 0;
	        $data['grabsheet']->property_imageid = 0;
	        $data['grabsheet']->grabsheetgroupid = 0;
	        $data['grabsheet']->showproductcodes = 0;
	        $data['grabsheet']->headerimageid = 1;
	        
       	} else {
       		
       		$template['page_title'] = "Edit Grabsheet -" . $data['grabsheet']->grabsheettitle;
	        $template['bigheader'] = "Edit Grabsheet - " . $data['grabsheet']->grabsheettitle;
	        $template['nav2'] = "Edit Grabsheet - " . $data['grabsheet']->grabsheettitle;
       	
       	}
       	
       	
       //	$template['searchArea'] = $this->load->view('grabsheets/searchArea',$data,true);

        
        
        $arrJS['scripts'] = array('jquery-1.9.1','jquery-ui-1.10.2.custom.min','opm_scripts'); 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        $template['content'] = $this->load->view('grabsheets/create2',$data,true);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function save() {
    
    	/*echo "<pre>";
    	print_r($_POST);
    	echo "</pre>";
    	
    	die();*/
    	
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
				
			if (!$postdata['headerimageid'] = $this->input->post('headerimageid'))
				$errors .= "Grabsheet has no header (branding) image!<br />";
				
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
			
			$postdata['property_imageid'] = $this->input->post('property_imageid');
			$postdata['grabsheetid'] = $this->input->post('grabsheetid');
			$postdata['savePermanent'] = $this->input->post('savePermanent');
			$postdata['showProductCodes'] = $this->input->post('showProductCodes');
			
			if ($postdata['showProductCodes'] == 'on')
				$postdata['showProductCodes'] = 1;
			else
				$postdata['showProductCodes'] = 0;
				
			
			if ($grabsheetid = $this->grabsheets_model->saveGrabsheet($postdata)) {
			
				if ($postdata['savePermanent'] == 'on') {  // try to save perm, if success, write entry to db, else alert
				
					if ($this->opm->createPDF($grabsheetid, false, true)) {
					
						$this->grabsheets_model->setGrabToFile($grabsheetid);
					
					} else {
					
						$this->opm->displayError("Grabsheet could not be saved as a file, it has been saved as regular grab instead.");
						return false;

					}
						
				
				}
			
				if ($postdata['grabsheetid'])
					$this->opm->displayAlert("Grabsheet Saved!","/grabsheets/search");
				else
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
    
    function view($grabsheetid, $download = false, $saveToFile = false, $lowRez = false) {
    
		$grab = $this->grabsheets_model->fetchGrabsheetInfo($grabsheetid);
		
		/*unset($grab->grabsheet);
		print_r($grab);
		die();*/
		
		if ($grab->isfile) {
		
			$filepath = $this->config->item('fileUploadPath') . "grabsheets/" . $grab->grabsheetid;
		
			if ($download) {
			
				header("Content-type: application/octet-stream");

				header("Content-Disposition: attachment; filename=\"Bravado_Grabsheet_" . $grab->grabsheettitle . ".pdf\"");
				header("Pragma: no-cache");
				header("Expires: 0");
				
				ob_end_flush();
				
				$filedata = readfile($filepath);
				
				echo $filedata;
				exit();
			
			} else {
				
				header("Content-type: application/pdf");
				
				$filedata = readfile($filepath);
			
				echo $filedata;
				exit();
			
			}
		
		} else {
			
			$this->opm->createPDF($grabsheetid, $download, $saveToFile, $lowRez);
		
		}
		
	}
	
	function _addText($pdf,$text) {
		
		$pdf->SetFont('Arial','',200);
		$pdf->SetTextColor(0,0,0);
		$pdf->Text(20,550,$text);
		
	}
		
	function view2() { 
		
		$CI =& get_instance();
		
		// get general grab info
		
		$CI->load->library('fpdf');
		
		//define('GRABSHEET_IMAGEPATH',false);
		//define('GRABSHEET_IMAGEWIDTH',500);
		define('GRABSHEET_TITLE',"");
		
		define('FPDF_FONTPATH',$CI->config->item('webrootPath')."resources/fpdf_font/");
				
		$pdf=new PDF_ImageAlpha('L','pt','Letter');
		
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		//$this->_addText(&$pdf,"hiii");
		
		
		
		$pdf->Output();
		
	}
	
	function view3() {
		
		$this->load->library('Pdftc');

		$pdf = new Tcpdf('L', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('My Title');
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');
		
				
		// set font
		$pdf->SetFont('dejavusans', '', 10);
	
		$pdf->AddPage();
		
		// define style for border
		$border_style = array('all' => array('width' => 0));
		
		// --- CMYK ------------------------------------------------
		
		$pdf->SetDrawColor(50, 0, 0, 0);
		$pdf->SetFillColor(100, 0, 0);
		$pdf->SetTextColor(100, 0, 0, 0);
		$pdf->Rect(0, 0, 300, 220, 'DF', $border_style);
	
		$pdf->Image($this->config->item('webrootPath')."resources/files/visuals/PCD-SKULL_REPEAT.png", 50, 50, 100, '', '', 'http://www.tcpdf.org', '', false, 300);	
		
		// change font size
		$pdf->SetFontSize(30);

		// change text color
		$pdf->SetTextColor(255,0,0);
		
		$pdf->Write(20, 'Some sample text');
		
		$pdf->AddPage();
		
		$pdf->Write(20, 'Some sample text');
		
		$pdf->Output('My-File-Name.pdf', 'I');
		
		
		
		// set default header data
		/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);*/
		
		
		// ---------------------------------------------------------

		
	}
		
		
	function createPdf($grabsheetid, $download = false, $saveToFile = false, $lowRez = false) {
	
		$CI =& get_instance();
	
		// GET GENERAL DB INFO FOR PDF
		
		$sql = "SELECT opm_grabsheet.*, opm_grabsheet_headerimages.imagepath, opm_grabsheet_headerimages.width
				FROM opm_grabsheet
				LEFT JOIN opm_grabsheet_headerimages ON opm_grabsheet_headerimages.id = opm_grabsheet.headerimageid
				WHERE opm_grabsheet.grabsheetid = '".$grabsheetid."'";
				
		$result = $CI->db->query($sql);
		$grabsheetinfo = $result->row_array();
		
		
		
		$sql = "SELECT *
				FROM opm_productlines
				LEFT JOIN properties ON properties.propertyid = opm_productlines.propertyid
				WHERE opm_productlines.productlineid = '".$grabsheetid."'";
				
		$result = $CI->db->query($sql);
		$productline = $result->row_array();
		
		// GET ALL IMAGES
		$sql = "SELECT opm_grabsheet_detail.comment,opm_products.*,opm_images.image_label,opm_images.imageid,opm_images.image_type,properties.property
				FROM opm_grabsheet_detail
				LEFT JOIN opm_images ON opm_images.imageid = opm_grabsheet_detail.imageid
				LEFT JOIN opm_products ON opm_products.opm_productid = opm_images.opm_productid
				LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
				WHERE opm_grabsheet_detail.grabsheetid = '".$grabsheetid."'
				ORDER BY opm_grabsheet_detail.displayorder
				";
		
		$products = $CI->db->query($sql);

		$CI->load->library('fpdf');		
	
		if ($grabsheetinfo['grabsheettemplateid'] == '1')
			define('GRABSHEET_TITLE',$grabsheetinfo['grabsheettitle']);
		else if ($grabsheetinfo['grabsheettemplateid'] == '2')
			define('GRABSHEET_TITLE',"");
		
		define('GRABSHEET_IMAGEPATH',$CI->config->item('webrootPath').$grabsheetinfo['imagepath']);
		define('GRABSHEET_IMAGEWIDTH',$grabsheetinfo['width']);
		
		// if we have a property image to display, define it's path!
		
		if ($grabsheetinfo['property_imageid']) {
		
			$sql = "SELECT * FROM properties WHERE propertyid = " . $CI->db->escape($grabsheetinfo['property_imageid']);
			$result = $CI->db->query($sql);
			$property_imageinfo = $result->row_array();
			
			if ($property_imageinfo['image_path']) {
			
				define('GRABSHEET_PROPERTYIMAGEPATH',$CI->config->item('webrootPath')."resources/files/propertyimages/" . $property_imageinfo['image_path']);
			
			} else {
			
				define('GRABSHEET_PROPERTYIMAGEPATH',false);
			
			}
			
		} else {
		
			define('GRABSHEET_PROPERTYIMAGEPATH',false);
		
		}

		define('FPDF_FONTPATH',$CI->config->item('webrootPath')."resources/fpdf_font/");
				
		$pdf=new PDF_ImageAlpha('L','pt','Letter');
		
		$pdf->AliasNbPages();
		//$pdf=new MEM_IMAGE();
		
		if ($grabsheetinfo['grabsheettemplateid'] == '1') 
			$pdf->AddPage();
		
		
		//$pdf->Image('../opm/images/500x500img.jpg',10,40,250);
		
		
		// control vars
		
		$imgsperpage = 8;
		$imgsperrow = 4;
		$imgpaddingx = 12;
		$imgpaddingy = 60;
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
				
				//	$CI->opm->displayError("This grabsheet contains links to images which have been deleted from the OPM system.<br>The product in question is <a href=\"".base_url()."products/view/".$product['opm_productid']."\">".base_url()."products/view/".$product['opm_productid']."</a>, imageID#".$product['imageid']."");
				//	return false;
				
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
				$file = $CI->config->item('webrootPath') . "resources/images/temp/".$product['imageid'].".jpg"; 
				
				if ($lowRez) {
				
					//die("about to make low rezzzz");
				
					$size = 550;  // new image width
					
					$filePath = $CI->config->item('fileUploadPath') . "visuals/" . $product['imageid'];
    	
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
					
					//die("gagaga");
					
					if ($product['image_type'] == "image/x-png" || $product['image_type'] == "image/png") {
					
						$src = imagecreatefrompng($filePath);
						$width = imagesx($src);
						$height = imagesy($src); 
    		
    		 			$newImg = imagecreatetruecolor($size, $size);
			 			imagealphablending($newImg, false);
			 			imagesavealpha($newImg,true);
			 			$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			 			imagefilledrectangle($newImg, 0, 0, $size, $size, $transparent);
			 			imagecopyresampled($newImg, $src, 0, 0, 0, 0, $size, $size, $width, $height);
						
						$img = $newImg;
						
					} else {
					
						$img = imagecreatetruecolor($new_w,$new_h);
					
					}
					
					imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);

					// determine image type and send it to the client    
					if ($product['image_type'] == "image/pjpeg" || $product['image_type'] == "image/jpeg") {    
						imagejpeg($img,$file); 
					} else if ($product['image_type'] == "image/x-png" || $product['image_type'] == "image/png") {
						imagepng($img,$file);
					} else if ($product['image_type'] == "image/gif") {
						imagegif($img,$file);
					}
					 
					imagedestroy($img);
				
				} else {
				
					$filePath = $CI->config->item('fileUploadPath') . "visuals/" . $product['imageid'];
    	
    				$fh = fopen($filePath, 'r');
					$data = fread($fh, filesize($filePath));
					
					fclose($fh);
					
					
					//$data = $product['image'];
				
				}
				
				
				
				if ($product['imageid']) {
					
					if (!$lowRez) { // in lowrez mode, we already wrote the temp file. 
				
						$file_handle = fopen($file,"a");  
						fwrite($file_handle, $data);     
						fclose($file_handle);
					
					}
							
					if ($product['image_type'] == "image/pjpeg" || $product['image_type'] == "image/jpeg") { 			   
					
						$pdf->Image($file,$xpos,$ypos,$imgwidth,$imgheight,'JPEG');
					
					} else if ($product['image_type'] == "image/x-png" || $product['image_type'] == "image/png") {
					
						$pdf->Image($file,$xpos,$ypos,$imgwidth,$imgheight,'PNG');
					
					} else if ($product['image_type'] == "image/gif") {
					
						$pdf->Image($file,$xpos,$ypos,$imgwidth,$imgheight,'GIF');
					
					}
					
					unlink($file);
					
					// create text string and display!
					
					$producttext = $product['productname'];
						
					$pdf->SetFont('Arial','B',9);
					$pdf->Text($xpos,$ypos+$imgheight+14,$producttext);
					
					// print create date
					
					//$pdf->SetFont('Arial','',9);
					//$pdf->Text($xpos,$ypos+$imgheight+26,"Create Date:");
					
					if ($product['productcode'] && $grabsheetinfo['showproductcodes']) {
					
						$pdf->SetFont('Arial','B',9);
						$pdf->Text($xpos,$ypos+$imgheight+25,$product['productcode']);
					
						$commentY = $ypos+$imgheight+36;
					
					} else {
					
						$commentY = $ypos+$imgheight+25;
					
					}
					
					$pdf->SetFont('Arial','B',9);
					$pdf->Text($xpos,$commentY,$product['comment']);
				
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
				
				
					
				} else {
				
				//	$CI->opm->displayError("This grabsheet contains links to images which have been deleted from the OPM system.<br>The product in question is <a href=\"".base_url()."products/view/".$product['opm_productid']."\">#".base_url()."products/view/".$product['opm_productid']."</a>, imageID#".$product['imageid']."");
				//	return false;
				
				}
				
			
			} else if ($grabsheetinfo['grabsheettemplateid'] == '2') {
			
				$pdf->AddPage();
	
				// print top text
				$pdf->SetFont('Arial','B',17);
				
				if (GRABSHEET_PROPERTYIMAGEPATH == true) {
				
					$headertext = strtoupper($product['productname']);
					$pdf->Text(290,55,$headertext);
				
				} else {
				
					$headertext = strtoupper($product['property'] . " : " . $product['productname']);
					$pdf->Text(20,55,$headertext);
					
					if ($grabsheetinfo['showproductcodes'] == '1') {
					
						$headertext2 = strtoupper($product['productcode']);
						$pdf->Text(20,75,$headertext2);
						
					}
				
				}
			
				// save temp file for image and display!
				$file = $CI->config->item('webrootPath') . "resources/images/temp/".$product['imageid'].".jpg";
				
				
				if ($lowRez) {
				
					//die("about to make low rezzzz");
				
					$size = 550;  // new image width
					
					$filePath = $CI->config->item('fileUploadPath') . "visuals/" . $product['imageid'];
    	
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
					
					if ($product['image_type'] == "image/x-png" || $product['image_type'] == "image/png") {
					
						$src = imagecreatefrompng($filePath);
						$width = imagesx($src);
						$height = imagesy($src); 
    		
    		 			$newImg = imagecreatetruecolor($size, $size);
			 			imagealphablending($newImg, false);
			 			imagesavealpha($newImg,true);
			 			$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			 			imagefilledrectangle($newImg, 0, 0, $size, $size, $transparent);
			 			imagecopyresampled($newImg, $src, 0, 0, 0, 0, $size, $size, $width, $height);
						
						$img = $newImg;
						
					} else {
					
						$img = imagecreatetruecolor($new_w,$new_h);
					
					}
					  
					imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
		
					// determine image type and send it to the client    
					if ($product['image_type'] == "image/pjpeg" || $product['image_type'] == "image/jpeg") {    
						imagejpeg($img,$file); 
					} else if ($product['image_type'] == "image/x-png" || $product['image_type'] == "image/png") {
						imagepng($img,$file);
					} else if ($product['image_type'] == "image/gif") {
						imagegif($img,$file);
					}
					 
					imagedestroy($img);
				
				} else {
					
					$filePath = $CI->config->item('fileUploadPath') . "visuals/" . $product['imageid'];
    	
    				$fh = fopen($filePath, 'r');
					$data = fread($fh, filesize($filePath));
					fclose($fh);
					
					//$data = $product['image'];
				
				}
				
				if ($product['imageid']) {
				
					if (!$lowRez) {
						$file_handle = fopen($file,"a");  
						fwrite($file_handle, $data);     
						fclose($file_handle);
					}
					
					//die("ello");
					
					if ($product['image_type'] == "image/pjpeg" || $product['image_type'] == "image/jpeg") { 			   
					
						$pdf->Image($file,185,100,425,425,'JPEG');
					
					} else if ($product['image_type'] == "image/x-png" || $product['image_type'] == "image/png") {
					
						$pdf->Image($file,185,100,425,425,'PNG');
					
					} else if ($product['image_type'] == "image/gif") {
					
						$pdf->Image($file,185,100,425,425,'GIF');
					
					}
					
					
					unlink($file);
					
				} else {
				
					$CI->opm->displayError("This grabsheet contains links to images which have been deleted from the OPM system.<br>The product in question is <a href=\"".base_url()."products/view/".$product['opm_productid']."\">".base_url()."products/view/".$product['opm_productid']."</a>, imageID#".$product['imageid']."");
					return false;
				
				}
				
				// print comments if they exist
				
				if ($product['comment']) {
				
					$pdf->SetFont('Arial','',12);
					$pdf->Text(20,550,"Comment: " . $product['comment']);
				
				}
				
				
				// print grabsheetttile
				
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(20,585,$grabsheetinfo['grabsheettitle']);
			
			}
			
		
		}
		
		$filename = "Bravado_" . $grabsheetinfo['grabsheettitle'] . ".pdf";
		$filename = str_replace(' ','_',$filename);
		$filename = str_replace('/','_',$filename);
		
		if ($saveToFile) {

			$pdf->Output($CI->config->item('fileUploadPath') . "grabsheets/" . $grabsheetid ,'F');
			
			if (file_exists($CI->config->item('fileUploadPath') . "grabsheets/" . $grabsheetid)) {
			
				return true;
			
			} else {
			
				return false;
			
			}
			
			
			
		} else {
		
			if ($download) {			
				$pdf->Output($filename,'D');
			} else {
				$pdf->Output();
			}
		
		}
	
	}
		
    
    
    
    
}

?>