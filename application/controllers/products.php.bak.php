<?php
class Products extends CI_Controller {

	function __construct()
    {
    	parent::Controller();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('products_model');
    	$this->load->model('properties_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'products';
    	
    	if ($this->config->item('debugMode') == true)
    		$this->output->enable_profiler(TRUE);
    }
	
	function view($id,$tabname = 'summary')
   	{
   	
   		// make sure we have permission to view this product!
   		$this->opm->checkProductViewPerms($id);
   	
    	if ($tabname)
    		$data['tabname'] = $tabname;
    	else
    		$data['tabname'] = 'summary';	
    
    	$data['product'] = $this->products_model->fetchProductInfo($id);
    	$data['bcDrop'] = $this->products_model->fetchProducts(false,null,null,$data['product']->propertyid,0,$data['product']->categoryid);
     	
     	// determine if we show "new comments since last view", etc.
     
     	if ($data['product']->latestForum) { // make sure there actually is a latest forum entry
     		
     		if ($data['product']->latestForum->timestamp > $data['product']->lastview)
     			$data['newComments'] = true;
     	
     	}
     	
     	
     	if($data['product']->latestHistory) { // make sure there actually is a latest history entry
     	
     		if ($data['product']->latestHistory->timestamp > $data['product']->lastview)
     			$data['newHistory'] = true;
     	
     	}
     	
     
     	$template['content'] = '';
     	
     	$template['headInclude'] = $this->load->view('product/loadContentJS',$data,true);
       
        $template['page_title'] = "View Product - " . $data['product']->property . " - " . $data['product']->productname;
        $template['bigheader'] = $this->load->view('bigheader/product',$data,true);
        $template['nav2'] = $this->load->view('product/breadcrumbs',$data,true);
        $template['contentNav'] = $this->load->view('product/contentNav',$data,true);
        $template['rightNav'] = $this->load->view('product/rightNav',$data,true);
        
        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','dropdown'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        // insert view record
       	
       	$this->opm->setViewTimestamp($id);
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function edit($id = 0)
   	{
   		
   		$this->load->model('categories_model');
   		$this->load->model('productlines_model');
   		$this->load->model('designers_model');
   		$this->load->model('users_model');
   		
   		$data['properties'] = $this->properties_model->fetchProperties(false,true);
   		$data['categories'] = $this->categories_model->fetchCategories();
   		//$data['designers'] = $this->users_model->fetch(false,0,null,3); // 3 is designer ugid
    
    
    	if ($id != 0) { // we are in edit mode!
    	
    	
    		$data['mode'] = "edit";	
    		$data['product'] = $this->products_model->fetchProductInfo($id);
    		$template['page_title'] = "Edit Product - " . $data['product']->property . " - " . $data['product']->productname;
    		$template['bigheader'] = $this->load->view('bigheader/product',$data,true);
    		$template['nav2'] = "Edit Product&nbsp;&nbsp;&gt;&nbsp;&nbsp;" . $data['product']->property . "&nbsp;&nbsp;&gt;&nbsp;&nbsp;" . $data['product']->productname;    		
    	
    		$data['productLines'] = $this->productlines_model->fetchProductLines($data['product']->propertyid,true,$data['product']->opm_productid);
    		$data['designers'] = $this->users_model->fetchDesigners(false,$data['product']->opm_productid);

    	
    	} else {  // we are in add mode!
    	
    		$data['mode'] = "add";	
    		$data['product']->opm_productid = 0;
    		$data['product']->propertyid = 0;
    		$data['product']->categoryid = 0; 
    		$data['product']->productname = ""; 
    		$data['product']->productcode = "";
    		$data['product']->filmlocations = "";
    		$data['product']->filmnumber = "";
    		$data['product']->copyrightaddendums = "";
    		$data['product']->artworkcharges = "";
    		$data['product']->presentationstyles = "";
    		$data['product']->duedate = 0;
    		$data['productLines'] = 0;
    		
    		$template['page_title'] = "Add Product";
    		$template['bigheader'] = "Add New Product";
    		$template['nav2'] = "Add New Product";
    		
    		$data['designers'] = $this->users_model->fetchDesigners();

    	
    	}
    	     
     	
     	$template['content'] = $this->load->view('product/edit',$data,true);
       
        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','datepicker'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
        
        header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');
  	 	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
   	 	header('Cache-Control: post-check=0, pre-check=0',false);
    	session_cache_limiter('must-revalidate');
	
		$this->load->view('global/maintemplate', $template);
    
    }
    
    function save() {
    
    	$this->load->model('categories_model');
    	
    	if (checkPerms('can_delete_products') && $this->input->post('delete_product')) {
    	
    		if ($this->products_model->deleteProduct($this->input->post('opm_productid'))) {
			
				$this->opm->displayAlert("Product has been deleted!","/search/doSearch");
				return true;	
			
			}
    	
    	}
    
    	if (checkPerms('can_add_products') || checkPerms('can_edit_products')) {
   		
			$errors = "";
			$postdata['opm_productid'] = $this->input->post('opm_productid');
			
			if (!$postdata['propertyid'] = $this->input->post('propertyid'))
				$errors .= "Product Must have a Property!<br />";
			
			if (!$postdata['productname'] = $this->input->post('productname')) 
				$errors .= "Product Must have a Name!<br />";
			
			if (!$postdata['categoryid'] = $this->input->post('categoryid')) 
				$errors .= "Product Must have a Category!<br />";
			
			if (!$postdata['productLineIDs'] = $this->input->post('productLineIDs')) 
				$errors .= "Product must have at least one product line!<br />";
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
	
			$postdata['productcode'] = $this->input->post('productcode');
			$postdata['filmlocations'] = $this->input->post('filmlocations');
			$postdata['filmnumber'] = $this->input->post('filmnumber');
			$postdata['artworkcharges'] = $this->input->post('artworkcharges');
			$postdata['presentationstyles'] = $this->input->post('presentationstyles');
			$postdata['copyrightaddendums'] = $this->input->post('copyrightaddendums');
			$postdata['designerIDs'] = $this->input->post('designerIDs');
			$postdata['duedate'] = $this->input->post('duedate');
			
			// convert duedate to a timestamp
			
			if ($postdata['duedate']) {
			
				$splitDate = explode("-", $postdata['duedate']);
				
				if (is_array($splitDate))
					$postdata['duedate'] = @mktime(1,0,0,$splitDate[0],$splitDate[1],$splitDate[2]);
			
			}
			
			// get pre-save info about product, to determine if any changes were made
			
			if ($postdata['opm_productid']) { // make sure we are editing, not adding.
			
				$pi = get_object_vars($this->products_model->fetchProductInfo($postdata['opm_productid'],true));
				
				// fetch new names of prop and cat, so we can compare...
				
				$property = $this->properties_model->fetchPropertyInfo($postdata['propertyid']);
				$postdata['property'] = $property->property;
	
				$category = $this->categories_model->fetchCategory($postdata['categoryid']);
				$postdata['category'] = $category->category;
	
				
				// unset useless vars
				
				$npi = $postdata;
				unset($pi['opm_productid'],$pi['old_productlineid'],$pi['statusid'],$pi['designerid'],$pi['isactive'],$pi['design_completed'],$pi['verballyapproved'],$pi['createdby'],$pi['lastmodified'],$pi['default_imageid'],$pi['approvalstatusid'],$pi['timestamp'],$pi['approvalstatus'],$pi['propertyid'],$pi['approval_methodid'],$pi['copyright'],$pi['productline'],$pi['categoryid']);
				unset($npi['designerIDs'],$npi['productLineIDs'],$npi['opm_productid'],$npi['categoryid'],$npi['propertyid']);
				
				// now we compare arrays, to check for changes.
				
				$arrDiff = array_diff_assoc($npi,$pi);
			
				foreach ($arrDiff as $key => $value) {
					
					if ($key == 'category') {
						$message = "Category was changed to " . $npi['category'] . " by " . $this->userinfo->username;
					} elseif ($key == 'productname') {
						$message = "Product Name was changed to " . $npi['productname'] . " by " . $this->userinfo->username;
					} elseif ($key == 'property') {
						$message = "Property was changed to " . $npi['property'] . " by " . $this->userinfo->username;
					} elseif ($key == 'duedate') {
						$message = "Due Date was changed to " . date("m/d/y h:i a",$npi['duedate']) . " by " . $this->userinfo->username;
					} elseif ($key == 'filmlocations') {
						$message = "Print + Garment Info was changed to " . $npi['filmlocations'] . " by " . $this->userinfo->username;
					} elseif ($key == 'filmnumber') {
						$message = "Film Number was changed to " . $npi['filmnumber'] . " by " . $this->userinfo->username;
					} elseif ($key == 'artworkcharges') {
						$message = "Artwork Charges was changed to " . $npi['artworkcharges'] . " by " . $this->userinfo->username;
					} elseif ($key == 'presentationstyles') {
						$message = "Presentation Styles was changed to " . $npi['presentationstyles'] . " by " . $this->userinfo->username;
					} elseif ($key == 'copyrightaddendums') {
						$message = "Copyright addendums was changed to ". $npi['copyrightaddendums'] ." by " . $this->userinfo->username;
					} else {
						$message = $key . " was changed to " . addslashes($value) . " by " . $this->userinfo->username;
					} 
					
					//echo $message . "<br><br>";
					
					$this->opm->addHistoryItem($postdata['opm_productid'],$message);
					
				}
			}
	
			if ($opm_productid = $this->products_model->saveProduct($postdata)) {
			
				$this->opm->setLastModified($opm_productid);
				
				if ($this->input->post('save_add_another'))
					$this->opm->displayAlert("Product has been saved!","/products/edit/");
				else
					$this->opm->displayAlert("Product has been saved!","/products/view/" . $opm_productid);
				
				return true;	
			
			
			} else {
			
				$this->opm->displayError("There was an error saving the product!","back");
				return true;
			}
		
		} else {
		
			$this->opm->displayError("You do not have permission to do that.");
			return true;
		
		}
			
	}
	
	function saveForumPost() {
	
	
		if (checkPerms('can_post_to_forums',true)) {
   		
			$errors = "";
			$postdata['opm_productid'] = $this->input->post('opm_productid');
			
			if (!$postdata['post_title'] = $this->input->post('post_title'))
				$errors .= "Post was missing Title<br />";
			
			if (!$postdata['post_text'] = $this->input->post('post_text')) 
				$errors .= "Post was missing content<br />";
			
			if ($errors) {
			
				$this->opm->displayError($errors);
				return false;
			
			}
	
			
			$this->load->model('forum_model');
			
			if ($this->forum_model->addForumEntry($postdata['opm_productid'],$this->userinfo->userid,$postdata['post_title'],$postdata['post_text'])) {
			
				// send email
				
				$arrData['username'] =  $this->userinfo->username;
				$arrData['commentSubject'] = $postdata['post_title'];
				$arrData['commentBody'] = $postdata['post_text'];
				
				$this->opm->sendProductEmail($postdata['opm_productid'],"new_comment_posted",$arrData);
			
				$this->opm->setLastModified($postdata['opm_productid']);
				$this->opm->displayAlert("Message succesfully posted.","/products/view/" . $postdata['opm_productid']);
				return true;	
			
			
			} else {
			
				$this->opm->displayError("Error saving forum post","back");
				return true;
			}
		
		}
		
			
	}
	
	function saveImage() {
	
		if (checkPerms('can_upload_images',true)) {
		
			if (is_uploaded_file($_FILES['imageFile']['tmp_name'])) { // we have a file upload!
	
				$sizeInfo = getimagesize($_FILES['imageFile']['tmp_name']);
				
				$postdata['image'] = addslashes(fread(fopen($_FILES['imageFile']['tmp_name'], "r"), filesize($_FILES['imageFile']['tmp_name'])));
				$postdata['filename'] = $_FILES['imageFile']['name'];
				$postdata['image_type'] = $_FILES['imageFile']['type'];

				if ($sizeInfo[0] != $this->config->item('visualWidth') || $sizeInfo[1] != $this->config->item('visualHeight')) {
				
					$this->opm->displayError("Image is not ". $this->config->item('visualWidth')."x". $this->config->item('visualHeight'),"back");
					return true;
				
				}

				unlink($_FILES['imageFile']['tmp_name']);
		
			} else {
			
				$this->opm->displayError("The uploaded image appears to be invalid.","/products/view/" . $postdata['opm_productid']."/images");
				return true;
			
			}
			
		

			$postdata['opm_productid'] = $this->input->post('opm_productid');
			
			$postdata['image_label'] = $this->input->post('label');
			
			
			$this->load->model('images_model');
			
			if ($this->images_model->saveImage($postdata)) {
				
				// prepare data and send email
				
				$arrData['imageInfo'] = $postdata;
				$arrData['username'] = $this->userinfo->username;
				$this->opm->sendProductEmail($postdata['opm_productid'],"new_image_uploaded",$arrData);
				
				$this->opm->setLastModified($postdata['opm_productid']);
				$this->opm->addHistoryItem($postdata['opm_productid'],$postdata['filename']." uploaded by " . $this->userinfo->username); 
				$this->opm->displayAlert("Image succesfully uploaded.","/products/view/" . $postdata['opm_productid']."/images");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("Error saving image","/products/view/" . $postdata['opm_productid']."/images");
				return true;
			}
		
		}
		
			
	}
    
    function loadContent($id,$tabName)
    {
    
    	if ($tabName == 'summary' && checkPerms('view_summary_tab')) {
    		
			$this->load->model('users_model');
			$this->load->model('usergroups_model');
			$this->load->model('images_model');
			$data['usergroups'] = $this->usergroups_model->fetchUsergroups($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
    		$data['images'] = $this->images_model->fetchImages($id);
			echo $this->load->view('product/'.$tabName,$data,true);
    
    	} else if ($tabName == 'involvement' && checkPerms('view_involvement_tab')) {
    		
			$this->load->model('users_model');
			$this->load->model('usergroups_model');
			$data['usergroups'] = $this->usergroups_model->fetchUsergroups($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			echo $this->load->view('product/'.$tabName,$data,true);
 	
    	} else if ($tabName == 'images' && checkPerms('view_images_tab')) {
    	
    		$this->load->model('images_model');
    		$data['images'] = $this->images_model->fetchImages($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else if ($tabName == 'files' && checkPerms('view_files_tab')) {
    		
    		$this->load->model('files_model');
    		$data['masterfiles'] = $this->files_model->fetchMasterFiles($id);
    		$data['separations'] = $this->files_model->fetchSeparations($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else if ($tabName == 'forum' && checkPerms('view_forum_tab')) {
    		
    		$this->load->model('forum_model');
    		$data['forum'] = $this->forum_model->fetchForumEntries($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else if ($tabName == 'history' && checkPerms('view_history_tab')) {
    		
    		$this->load->model('history_model');
    		$data['history'] = $this->history_model->fetchHistory($id);
    		$data['product'] = $this->products_model->fetchProductInfo($id);
			echo $this->load->view('product/'.$tabName,$data,true);
    	
    	} else {
    	
    		$this->opm->displayError("Cannot load tab content.");
			return true;
    	
    	}
	
    
    }
    
    
    function updateApprovalStatus($opm_productid) {
    	
    	$this->load->model('approvalstatus_model');
    	
		if($id = $this->approvalstatus_model->updateApprovalStatus($opm_productid))
			echo "success. appstatus id changed to " . $id;
    
    }
    
    function changeApprovalStatus($opm_productid,$action,$userid = 0) { // user id submitted for verbal approval
    
    	$this->load->model('approvalstatus_model');
    	
    	$this->opm->setLastModified($opm_productid);
	
		// first check that user has approval/rejection rights on this product.
		
		$product = $this->products_model->fetchProductInfo($opm_productid);
		
		$canApprove = false;
		
		foreach ($product->approvalInfo as $ai) {
		
			if ($ai->userid == $this->userinfo->userid)
				$canApprove = true;
		
		}
		
		if ($canApprove || checkPerms('can_verbally_approve')) {
		
			if ($action == 'approve') {
			
				if($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,1)) {
				
					$this->opm->displayAlert("Product successfully Approved!","/products/view/" . $opm_productid);
					return true;
				
				}
				
			} else if ($action == 'reject') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,3)) {
				
					$this->opm->displayAlert("Rejection Successful!","/products/view/" . $opm_productid);
					return true;
					
				}
				
							
			} else if ($action == 'verballyApprove') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($userid,$opm_productid,1,true)) {
				
					$this->opm->displayAlert("Verbal Approval Successful!","/products/view/" . $opm_productid);
					return true;
					
				}
			
			} else if ($action == 'undo') {
			
				if ($this->approvalstatus_model->changeApprovalStatus($userid,$opm_productid,0)) {
				
					$this->opm->displayAlert("Reversal Success!","/products/view/" . $opm_productid);
					return true;
					
				}
				
			} else if ($action == 'approvewrevisions') {
				
				$this->load->model('forum_model');
				
				$postdata['revisions'] = $this->input->post('revisions');
				
				if ($postdata['revisions'] && ($postdata['revisions'] != 'Enter Revisions Here...') && ($postdata['revisions'] != 'If you have any revisions, please enter them here...')) { // set status to approve w/ revisions.
				
					$this->forum_model->addForumEntry($opm_productid,$this->userinfo->userid,"REVISIONS",$postdata['revisions']);
					
					if ($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,2,false,$postdata['revisions'])) {
					
						$this->opm->displayAlert("Approval Success!","/products/view/" . $opm_productid);
						return true;
						
					}
				
				} else { // no revisions were sent, simply approve.
				
				
					if($this->approvalstatus_model->changeApprovalStatus($this->userinfo->userid,$opm_productid,1)) {
					
						$this->opm->displayAlert("Product successfully Approved!","/products/view/" . $opm_productid);
						return true;
				
					}
				
				
				}
				
			} else if ($action == 'resubmitwrevisions') {
				
				$this->load->model('forum_model');
				
				$postdata['revisions'] = $this->input->post('revisions');
				
				$this->forum_model->addForumEntry($opm_productid,$this->userinfo->userid,"REVISIONS",$postdata['revisions']);
				
				// send email!
				
				$arrData['username'] =  $this->userinfo->username;
				$arrData['productInfo'] = $this->products_model->fetchProductInfo($opm_productid);
				$arrData['revisions'] = $postdata['revisions'];
				$arrData['approvalstatus'] = "Resubmitted With Revisions";
				
				$this->opm->sendProductEmail($opm_productid,"approval_status_changed",$arrData);
				
				// alert user
								
				$this->opm->displayAlert("Revisions have been submitted!","/products/view/" . $opm_productid);
			
			}
		
		} else {
		
		
		}
		
					
	}
	
	
	function setSampAppStatus($opm_productid,$onoff) { // user id submitted for verbal approval

		if (checkPerms('can_edit_samp_approval',true)) {
		
			if ($onoff) {
			
				$statusid = 1;
				$message = "Sample approval status was changed to Approved by " . $this->userinfo->username;
			
			} else {
			
				$statusid = 0;
				$message = "Sample approval status was changed to Pending by " . $this->userinfo->username;
				
			}
			
			if ($this->products_model->setSampAppStatus($opm_productid,$statusid)) {
				
				
				$this->opm->addHistoryItem($opm_productid,$message);
				
				$this->opm->displayAlert("Sample approval status has been changed.","/products/view/" . $opm_productid);
				return true;
				
			} else {
			
				$this->opm->displayError("Error saving status","/products/view/" . $opm_productid);
				return true;
			
			}
		
		}

    }
 
    
}



?>