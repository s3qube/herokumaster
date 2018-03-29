<?php
class Archive extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->model('productlines_model');
    	$this->load->model('files_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'products';
    	
    	  	global $searchArgList;
    	
    	$searchArgList = array("propertyid","productlineid");
    	
    	if ($this->config->item('debugMode') == true)
    		$this->output->enable_profiler(TRUE);
    		
    }
    
    function index() {
    
    	redirect("archive/select");
    
    }
	
	function select($propertyid = null,$productlineid = null) {
   	
   		
   		if (checkPerms('can_archive_products')) {
			
			$data = array();
			
			$data['propertyid'] = $propertyid;
			$data['productlineid'] = $productlineid;
			
			$data['properties'] = $this->properties_model->fetchProperties(false,true);
			
			if ($propertyid) {
        		
        		$data['productLines'] = $this->productlines_model->fetchProductLines($propertyid);
        		$data['products'] = $this->products_model->fetchProducts(false,0,null, $data['propertyid'], $productlineid);
			
			}
            
	        $template['page_title'] = "Archive Products";
	        $template['bigheader'] = "Archive Products";
	        $template['nav2'] = "Archive Products";
	       // $template['contentNav'] = $this->load->view('product/contentNav',$data,true);
	       // $template['rightNav'] = $this->load->view('product/rightNav',$data,true);
	        
	        $template['content'] = $this->load->view('archive/archive',$data,true);
	        	        
	        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','dropdown'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	        
		
			$this->load->view('global/maintemplate', $template);
		
		}
    
    }
    
    function submit() { // construct a url from a form submission

    
    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		if ($this->input->post('clearProductLine'))
			$segments['productlineid'] = 0;
		
		$url = "/archive/select/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
			
		
    
    }
    
    function submitProducts() { // archive the products
		
		
		// TO DO:
		
			// Make sure there is no time limit
			// error log for archiving so process doesnt stop if err encountered..
		
		$log = array();
		$goodArchiveCount = 0;
		$badArchiveCount = 0;

		if (checkPerms('can_archive_products')) {
		
			$prodIDArray = $this->input->post('arcList');
			    		
    		foreach ($prodIDArray as $pid) {
    		
    			$product = $this->products_model->fetchProductInfo($pid);
    			$product->masterFiles = $this->files_model->fetchMasterFiles($pid);
    			$product->separations = $this->files_model->fetchSeparations($pid);
    			$productName = $product->property . " - " . $product->productname;
    			
    			// LET's DO master files...
    			
    			foreach ($product->masterFiles->result() as $f) {
    			
    				// check to see if file already archived!
    				
    				if (!$f->archivedate) {
    				
	    				$result = $this->opm->archiveFile("M",$f->fileid);
	    				
	    				if ($result == 'success') {
	    				
	    					$log[] = $productName . " - Master File # - " . $f->fileid . " was archived succesfully.";
	    					$goodArchiveCount++; 
	    					    					
	    				} else {
	    				
	    					$log[] = $productName . " - Master File # - " . $f->fileid . " had the following error: " . $result;
	    					$badArchiveCount++;
	    					
	    				}
	    				
	    			} else {
	    			
	    				$log[] = $productName . " - Master File # - " . $f->fileid . " already archived!";
	    				$badArchiveCount++;
	    			
	    			}
    			
    			}
    			
    			// And now separations...
    			
    			foreach ($product->separations->result() as $f) {
    			
    				// check to see if file already archived!
    				
    				if (!$f->archivedate) {
    				
	    				$result = $this->opm->archiveFile("S",$f->fileid);
	    				
	    				if ($result == 'success') {
	    				
	    					$log[] = $productName . " - Separation # - " . $f->fileid . " was archived succesfully.";
	    					$goodArchiveCount++; 
	    					    					
	    				} else {
	    				
	    					$log[] = $productName . " - Separation # - " . $f->fileid . " had the following error: " . $result;
	    					$badArchiveCount++;
	    					
	    				}
	    			
	    			} else {
	    			
	    				$log[] = $productName . " - Separation # - " . $f->fileid . " already archived!";
	    				$badArchiveCount++;
	    			
	    			}
    			
    			}
    			
    			
    		
    		}
    		
    		
    		// write to log file!
    			
			$filePath = $this->config->item('fileUploadPath') . "archive/archiveLog_" . mktime() . ".txt";
			
			$fileContents = "ARCHIVE LOG " . date('l jS \of F Y h:i:s A') . "\n";
			$fileContents .= "--------------------\n\n";
			
			$fileContents .= "Files Archived: " . $goodArchiveCount ."\n";
			
			$fileContents .= "Files With Errors: " . $badArchiveCount ."\n\n\n";
			
			$fileContents .= implode("\n", $log);
			
			file_put_contents($filePath, $fileContents);
			
			$template['page_title'] = "Archive Products";
       		$template['bigheader'] = "Archive Products";
        	$template['nav2'] = "Archive Products";
	       // $template['contentNav'] = $this->load->view('product/contentNav',$data,true);
	       // $template['rightNav'] = $this->load->view('product/rightNav',$data,true);
	        
	        $data['goodArchiveCount'] = $goodArchiveCount;
	        $data['badArchiveCount'] = $badArchiveCount;
	        $data['log'] = $log;
	        
	        $template['content'] = $this->load->view('archive/archiveResult',$data,true);
	        	        
	        $arrJS['scripts'] = array('opm_scripts');
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	        
		
			$this->load->view('global/maintemplate', $template);

		}
		
	
    }
    
 
    
}



?>