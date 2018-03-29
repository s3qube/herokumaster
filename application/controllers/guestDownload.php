<?php
class GuestDownload extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	
    }

	
	function setup($fileid, $filetype, $isUpload = 0) { // filetype is either mf or sep.
	
		$this->opm->checkLogin();
    	$this->opm->opmInit();
    	
    	$this->load->model('properties_model');
    	$this->load->model('files_model');
    	$this->load->model('products_model');
    	$this->load->model('guestdownload_model');
    	$this->load->helper('text');
	
	
		if (checkPerms('can_guest_download',true)) {
		
			$data = array();
			
			$data['fileType'] = $filetype;
			$data['fileID'] = $fileid;
		
			// let's get info about the file, first making sure that user has perms to view it.
			
			if (!$isUpload) {
			
			if ($filetype == 'mf')
				$f = $this->files_model->fetchMasterFile($fileid);
			elseif ($filetype == 'sep')
				$f = $this->files_model->fetchSeparation($fileid);
			else
				$this->opm->displayError("Error: Invalid File Type Sent");
			
			} else { 
				
				$data['isUpload'] = true;
				$f->opm_productid = $fileid; // in the case of uploads, we are passing opmproductid as fileid. don't ask.
			
			}
			
			if ($f->opm_productid) { // make sure we got a product id
			
				// fetch product info
				
				$data['opmProductID'] = $f->opm_productid;
				
				$p = $this->products_model->fetchProductInfo($f->opm_productid);
				
				$this->opm->checkProductViewPerms($f->opm_productid);
				
				$this->load->view('guestDownload/setup', $data);
		        				
			} else {
			
				$this->opm->displayError("Could Not Find Associated Product.");
			
			}
		
		}
	
	}
	
		
    function handle() {
    
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('properties_model');
    	$this->load->model('files_model');
    	$this->load->model('products_model');
    	$this->load->model('guestdownload_model');
    	$this->load->helper('text');
      	
      	if ($this->input->post('name1'))
    		$recips[] = array("name"=>$this->input->post('name1'),"email"=>$this->input->post('email1'));
    	
    	if ($this->input->post('name2'))
    		$recips[] = array("name"=>$this->input->post('name2'),"email"=>$this->input->post('email2'));
    		
    	if ($this->input->post('name3'))
    		$recips[] = array("name"=>$this->input->post('name3'),"email"=>$this->input->post('email3'));
		
    	
    	$postdata['fileType'] = $this->input->post('fileType');
    	$postdata['isUpload'] = $this->input->post('isUpload');
    	$postdata['opmProductID'] = $this->input->post('opmProductID');
    	
    	$errors = 0;
    	
    	if (!$postdata['isUpload'])
    		$postdata['fileID'] = $this->input->post('fileID');
    	else
    		$postdata['fileID'] = 0;
    	
    	
    	if (is_array($recips)) {
    	
    		foreach ($recips as $r) {
    		
    			$postdata['name'] = $r['name'];
    			$postdata['email'] = $r['email'];
    	
		    	do { // get a random string until we are sure it is unique!
		    	
		    		$postdata['randomString'] = random_str(16);
				
				} while (!$this->guestdownload_model->checkString($postdata['randomString']));
		    	
		    	
		    	// get info about file!
		    	
		    	if (!$postdata['isUpload']) {
		    	
			    	if ($postdata['fileType'] == 'mf') {
			    	
			    		$f = $this->files_model->fetchMasterFile($postdata['fileID']);
			    	
			    	} elseif ($postdata['fileType'] == 'sep') {
			    	
			    		$f = $this->files_model->fetchSeparation($postdata['fileID']);
			    	
			    	} else {
			    	
			    		die("ERROR: Couldn't find file info!");
			    	
			    	}
			    	
			    }
		    	
		    	if ($this->guestdownload_model->saveDownload($postdata)) {
		    	
		    		// now lets email the dude.
		    		
		    		$data = $postdata;
		    		
		    		$data['senderName'] = $this->userinfo->username;
		    		
		    		if ($postdata['isUpload']) {
		    		
		    			$data['filename'] = "";
		    		
		    			$subject = $this->load->view('emails/guestUpload_subject',$data,true);
		    			$body = $this->load->view('emails/guestUpload_body',$data,true);
		    		
		    		} else {
		    		
		    			$data['filename'] = $f->filename;
		    		
		    			$subject = $this->load->view('emails/guestDownload_subject',$data,true);
		    			$body = $this->load->view('emails/guestDownload_body',$data,true);
		    		
		    		}
		    		
		    		$recipients = array();
		    		
		    		$recipients[] = $postdata['email'];
		    		$recipients[] = $this->userinfo->login;
		    	
		    		if ($this->opm->sendEmail($subject,$body,$recipients)) {
		    		
		    			if (!$postdata['isUpload']) 
		    				$message = $this->userinfo->username . " set up a guest download of " . $f->filename . " for " . $postdata['name'] . " (".$postdata['email'].")";
		    			else
		    				$message = $this->userinfo->username . " set up a guest upload of for " . $postdata['name'] . " (".$postdata['email'].")";
		    			
		    			$this->opm->addHistoryItem($postdata['opmProductID'],$message);

		    		} else {
		    			
		    			$errors++;
		    		
		    		}
		    	
		    		
		    	
		    	} else {
		    	
		    		$errors++;
		    	
		    	}
		    
		    } // end foreach - mutiple recipients
	    	
	    	if ($errors == 0)
	    		die("Success");
	    	else
	    		die("errors");
	    		
	    	
	    	
	    }
    				
	}
	
	
	
	function file($randomStr) {
	
		$this->load->helper('file');
	
		$this->load->model('properties_model');
    	$this->load->model('files_model');
    	$this->load->model('products_model');
    	$this->load->model('guestdownload_model');
    	$this->load->helper('text');
	
		if (!$gd = $this->guestdownload_model->fetchDownload($randomStr))
    		$this->opm->displayError("There was a problem with your download. Please contact your Bravado rep.");
    		
    	if ($gd->downloaddate != '0')
			$this->opm->displayError("This download has already been used. If you need another download, please contact your Bravado OPM rep.");

    		
		if ($gd->filetype == 'mf') {
    	
    		$f = $this->files_model->fetchMasterFile($gd->fileid);
    		$filepath = $this->config->item('fileUploadPath') . "masterfiles/" . $f->fileid;
    		
    	} elseif ($gd->filetype  == 'sep') {
    	
    		$f = $this->files_model->fetchSeparation($gd->fileid);
    		$filepath = $this->config->item('fileUploadPath') . "separations/" . $f->fileid;
    	
    	} else {
    	
    		$this->opm->displayError("There was a problem with your download. Please contact your Bravado rep.");
    	
    	}	
    	
    	if ($filedata = read_file($filepath)) {
			
				$this->opm->addHistoryItem($gd->opm_productid,$gd->username." (guest) downloaded " . $f->filename); 
			
				header("Content-type: application/octet-stream");

				header("Content-Disposition: attachment; filename=\"" . $f->filename . "\"");
				header("Pragma: no-cache");
				header("Expires: 0");
				
				echo $filedata;
				
				// now disable download!
				$this->guestdownload_model->disableDownload($gd->id);
				
				exit();
			
			
			} else {
			
				$this->opm->displayError("The file could not be read. Please contact your Bravado OPM rep.");
			
			}
	
	}
	
	function upload($randomStr) {
	
		$this->load->helper('file');
	
		$this->load->model('properties_model');
    	$this->load->model('files_model');
    	$this->load->model('products_model');
    	$this->load->model('guestdownload_model');
    	$this->load->helper('text');
	
		if (!$gd = $this->guestdownload_model->fetchDownload($randomStr))
    		$this->opm->displayError("There was a problem with your upload. Please contact your Bravado rep.");
    		
    	if ($gd->downloaddate != '0')
			$this->opm->displayError("This upload has already been used. If you need another upload, please contact your Bravado OPM rep.");

		// mf/sep
		
		if ($gd->filetype == 'mf')
			$fileTypeText = "Master File";
		else
			$fileTypeText = "Separation";
			
    	// get product info
    	
    	$data['p'] = $this->products_model->fetchProductInfo($gd->opm_productid);
    	$data['randomString'] = $randomStr;
    	
    	$template['content'] = $this->load->view('guestDownload/upload', $data, true);
     
        
        $template['nav2'] = "Guest Upload";
        $template['page_title'] = "Guest Upload";
		$template['bigheader'] = "Guest Upload - " . $data['p']->property . " - " . $data['p']->productname . " - " . $fileTypeText;
	//	$template['contentNav'] = $this->load->view('properties/contentNav',$data,true);

		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		
		$this->load->view('global/maintemplate', $template);
	
	}
	
	
	
}
?>