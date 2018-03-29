<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Opm {

	function opmInit() {
	
		$this->buildPermissionQuery();

		$CI =& get_instance();
		
		// check if we have TOS entries to agree to...
		
		// don't do this if we are already using tos controller (or else we'd have a loop)
		
		$controller = $CI->router->fetch_class();
		
		if ($controller != 'tos') {
		
			$CI->load->model('tos_model');
		
			$tosQuery = $CI->tos_model->fetchNeededTOS($this->userinfo->userid);
			
			if ($tosQuery->num_rows() > 0) { // no tos needed, redirect.
			
				$pos = strpos($_SERVER['PHP_SELF'], "index.php");
				
				$redirectPath = substr($_SERVER['PHP_SELF'],$pos+9);
				
				$CI->session->set_userdata('tosRedirect', $redirectPath);
			
				redirect("/tos/");
			
			}
		
		}
		
		
	
		//if ($CI->userinfo->userid == 1 || $_SERVER['REMOTE_ADDR'] == '68.173.125.182')
			//$CI->output->enable_profiler(TRUE);
	
	}
	
	
	function checkLogin($ajax_mode = false){ // if we are in ajax mode, just echo a simple comment, so that ajax can deal with the user.
	
		$CI =& get_instance();
		
		
		// check the session, make sure we are logged in 
		
		if ($CI->session->userdata('logged_in')) {
		
		
			$CI->load->model('users_model');
			
			$userid = $CI->session->userdata('userid');
			
			if ($CI->session->userdata('impersonateUser')) {
				
				$userid = $CI->session->userdata('impersonateUser');
				
			}
			
			$CI->opm->userinfo = $CI->users_model->fetchUserInfo($userid);
			
			$CI->userinfo =& $CI->opm->userinfo;
			
			// if user is inactive, kick out.
			
			if (!$CI->userinfo->isactive) {
				
				redirect('/login/doLogout');
				return true;
				
			}
			
			// make everyone change pass
			
			if ((!$CI->userinfo->password_changed || $CI->userinfo->password_reset) && ($CI->uri->segment(1) != 'mypreferences') && ($CI->uri->segment(1) != 'tos')) {
				
				redirect('/mypreferences/');
				
			}
			
			if (isset($CI->userinfo->prefs['default_desc_prod_sort']))
				define("DEFAULT_SORT_DIRECTION","desc");
			else
				define("DEFAULT_SORT_DIRECTION","asc");
			
		
		} else {
			
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') { // this is an ajax request, send a simple commment so that JS can redirect the user.
			
				die("<!--not_logged_in-->");
			
			} else {
			
				// let's figure out where we were trying to go!
				
				$pos = strpos($_SERVER['PHP_SELF'], "index.php");
				
				$redirectPath = substr($_SERVER['PHP_SELF'],$pos+9);
				
				$CI->session->set_userdata('loginRedirect', $redirectPath);
				
				redirect('/login/');
				
				//$CI->opm->displayError("You are not logged in!",$baseurl."login/");
				//return true;
			
			} 
			
					
		}
		
	
	}
	
	function archiveFile($filetype,$fileid) {
	
		if (checkPerms('can_archive_products',true)) {
	
			$CI =& get_instance();
			$CI->load->model('files_model');

			// get info about file
		
			if ($filetype == 'M') {
			
				$fileTypeDirName = "masterfiles";
			
				if (!$f = $CI->files_model->fetchMasterFile($fileid)) {
				
					return "File ID not found in DB.";
				
				}	
			
			} elseif ($filetype == 'S') {
			
				$fileTypeDirName = "separations";
			
				if (!$f = $CI->files_model->fetchSeparation($fileid)) {
				
					return "File ID not found in DB.";
				
				}	
			
			} else {
			
				return "Invalid File Type.";
			
			}
			
			// now lets make sure property dir structure exists in archive directory. if not, we'll create it.
			
			$archiveDir = $CI->config->item('fileUploadPath') . "archive/";
			
			// does property directory exist?
			
			
			$propertyDir = goodFilename($f->property) . "/";
			
			
			if (!is_dir($archiveDir . $propertyDir)) {
			
				if (!mkdir($archiveDir . $propertyDir)) {
					
					return "Could not create directory ".$archiveDir . $propertyDir.". Cannot Archive File.";
				
				}
			
			}
			
			// property directory successfully created, create product dir
			
			
			$productDir = $archiveDir . $propertyDir . goodFilename($f->opm_productid . "_" . $f->productname . "_" . $f->category) . "/";
		
			
			if (!is_dir($productDir)) {
			
				if (!mkdir($productDir)) {
					
					return "Could not create directory " . $productDir . ". Cannot Archive File.";
				
				}
			
			}
			
			// ok, now create "masterfiles" or "separations" dir...
			
			$fileTypeDir = $productDir . $fileTypeDirName . "/";
						
			if (!is_dir($fileTypeDir)) {
			
				if (!mkdir($fileTypeDir)) {
					
					return "Could not create directory " . $fileTypeDir . ". Cannot Archive File.";
				
				}
			
			}
			
			// ok, directory structure complete. make a good name for the file and move it!!!
			
			$fileName = $f->fileid . "_" . goodFilename($f->filename);
			
			$origFilePath = $CI->config->item('fileUploadPath') . $fileTypeDirName . "/" . $f->fileid;
			
			if (copy($origFilePath, $fileTypeDir . $fileName)) {
			
				// now let's confirm that the original file and the copy are the same size...
				
				// then we can delete the original...
				
				if (filesize($origFilePath) == filesize($fileTypeDir . $fileName)) {
				
					// delete original file, set archived date in DB
					
					unlink($origFilePath);
					$CI->files_model->markFileAsArchived($filetype,$fileid);
					
					return "success";
				
				} else {
				
					return "File failed archive size confirmation. Cannot Archive File.";
				
				}
			
				
			
			} else {
			
				return "Copy File Failed.";
			
			}
			
			
		}
	
		
	
	}
	
	function buildSkusForProduct($opm_productid, $supressErrors = false) {
	
		if (checkPerms('can_build_skus',true)) {
	
			$CI =& get_instance();
			$CI->load->model('products_model');
			$CI->load->model('users_model');
			
			// okay, lets figure out all the information we need, and if it's not there, throw errrrrs.
			
			if (!$p = $CI->products_model->fetchProductInfo($opm_productid)) {
			
				if (!$supressErrors) 
					$CI->opm->displayError("Product could not be found for sku generation!");
				
				return false;
			
			}
			
			// we need a design code 
			
			if (!$p->designcode) {
				
				if (!$supressErrors) 
					$CI->opm->displayError("No Design Code Found For Product. SKUs could not be generated.");
			
				return false;
			
			}
			
			// we need the property to have a nv_propid
			
			if (!$p->nv_propid) {
			
				if (!$supressErrors) 
					$CI->opm->displayError("No Navision Property Code Found For ".$p->property.". SKUs could not be generated.");
				
				return false;
			
			}
			
			// we need to have a number of prints!
			
			if (!$p->numprints) {
			
				if (!$supressErrors) 
					$CI->opm->displayError("Number of prints for product is zero. SKUs could not be generated.");
				
				return false;
				
				
			
			}
			
	
	    	// it needs to be available in some colors
	    			
			if (sizeof($p->colors) == 0) {
			
				if (!$supressErrors) 
					$CI->opm->displayError("Product is not available in any Colors. SKUs could not be generated.");
				
				return false;
				
				
			
			}
	    	
	    	// it needs to be available in some sizes.
	    			
			if (sizeof($p->sizes) == 0) {
			
				if (!$supressErrors) 
					$CI->opm->displayError("Product is not available in any sizes. SKUs could not be generated.");
				
				return false;
			
			}
			
			// first let's delete any existing SKUs
			
			$CI->products_model->deleteSkus($opm_productid);
			
			// apparently all is good. Lets generate the SKUs. Loop colors, then sizes.
			
			foreach($p->colors as $c) {
			
				foreach($p->sizes as $s) {
											
					$skuText = "";
					$skuText .= sprintf("%04d", $p->nv_propid);
					$skuText .= sprintf("%01d", $p->numprints);
					$skuText .= sprintf("%02d", $c['id']);
					$skuText .= sprintf("%04d", $p->designcode);
					$skuText .= sprintf("%03d", $s['id']);
	
					//echo "GENERATING SKU for color:".$c['color']. ", size:" . $s['size'] . "<br>";
					//echo $skuText . "<br>";
	
					$result = $CI->products_model->addSku($opm_productid,$c['id'],$s['id'],$skuText);
					
					if ($result == 'duplicate') {
					
						$CI->opm->displayError("A duplicate SKU number was found in OPM. Please ensure that assigned Property and Design codes are unique.");
						return false;
					
					} elseif ($result == "queryfailed") {
					
						$CI->opm->displayError("An error was found when attempting to generate SKUs. Please contact OPM tech support.");
						return false;
					
					}
					
				}
			
			}
			
			return true;
			
		}
	
	}

	
	function checkProductViewPerms($opm_productid,$userid = null) {
		
		$CI =& get_instance();
		$CI->load->model('products_model');
		$CI->load->model('users_model');
		
		if ($userid) { // we are checking a userid other than the currently logged in user.
		
			$userinfo = $CI->users_model->fetchUserInfo($userid);
		
			//if (!$userinfo->isactive)
			//	return false;
		
		} else {
		
			$userinfo = $CI->userinfo;
		
		}
		
		if (isset($userinfo->perms['view_all_products'])) // USER HAS VIEW ALL PRODUCTS PERM!
			return true; 
			
		// now check if product is viewable by user's usergroup (we must exclude property contacts and designers)!
		
		$productInfo = $CI->products_model->fetchProductInfo($opm_productid);
		
		// if user created this product, let them view.
		
		if ($productInfo->createdby == $userinfo->userid) 
			return true;
		
		// remove property contacts and designers usergroups from array!
		// we need to get children of these groups as well!!
		
		$designerUGs = $CI->usergroups_model->getChildren($CI->config->item('designersGroupID'));
		$designerUGs[] = $CI->config->item('designersGroupID');
		
		$pcUGs = $CI->usergroups_model->getChildren($CI->config->item('propertyContactsGroupID'));
		$pcUGs[] = $CI->config->item('propertyContactsGroupID');
		
		$propliUGs = $CI->usergroups_model->getChildren($CI->config->item('licenseeGroupID'));
		$propliUGs[] = $CI->config->item('licenseeGroupID');
	
		$arrUGsToIgnore = array_merge($designerUGs,$pcUGs,$propliUGs);
		
		
		$validUsergroups = array();
		
		foreach($productInfo->usergroups as $key=>$value) {
			
			if (in_array($value, $arrUGsToIgnore)) {
			
				//unset($productInfo->usergroups[$key]);
			
			} else {
			
				$validUsergroups[] = $value;
			
			}
		
			//if ($value == $CI->config->item('propertyContactsGroupID') || $value == $CI->config->item('designersGroupID'))
				
		
		}
		
		
		if (array_intersect($userinfo->viewRightsUserGroups, $validUsergroups)) // it is!
			return true;

		// now check if user is property contact, also make sure product is visible to appropriate property contact group.
		
		foreach ($productInfo->approvalInfo as $appUser) {
		
			if ($appUser->userid == $userinfo->userid) { // user is approval contact for product!
				
				foreach ($productInfo->usergroups as $key=>$value) {
					
					if ($value == $CI->config->item('propertyContactsGroupID'))
						return true;

				}

			}
		}
		
		// now check if user is property licensee!
		
		if (in_array($CI->config->item('licenseeGroupID'), $productInfo->usergroups)) { // product is viewable by licensees!
			
			//echo "viewable by licensees!";
			
			foreach ($productInfo->propLicensees as $l) {
								
				if ($l['usergroupid'] == $userinfo->usergroupid || $l['usergroupid'] == $userinfo->usergroupid2) // user is licensee for product!
					return true;
				
			}
		}
		
		// now check if user is designer!
		
		foreach ($productInfo->designers as $d) {
		

			if ($d['userid'] == $userinfo->userid) // user is designer for product!
				return true;
		
		}
		
		// now check if user is licensee!
		
		foreach ($productInfo->licensees as $l) {
		

			if ($l['usergroupid'] == $userinfo->usergroupid || $l['usergroupid'] == $userinfo->usergroupid2) // user is designer for product!
				return true;
		
		}
		
		if (!$userid) {
		
			$CI->opm->displayError("You do not have permission to view this product!");
			return false;
		
		} else {
		
			return false;
		
		}
	
	}
	
	function sendEmail($subject,$body,$recipients,$bccRecipients = false) { // bcc is used for bulk emails.
	
		$CI =& get_instance();

		$kanyeEmail = 'omariwest@gmail.com';
	
		$CI->load->helper('phpmailer');
		
		$mail = new phpmailer();
		
		if ($CI->config->item('mailSMTP'))
			$mail->IsSMTP(); // telling the class to use SMTP
		
		if ($CI->config->item('mailHost'))
			$mail->Host     = $CI->config->item('mailHost'); // SMTP servers
		
		if ($CI->config->item('mailPort'))
			$mail->Port     = $CI->config->item('mailPort'); // SMTP port
		
		$mail->SMTPAuth = false;     // turn on SMTP authentication
		
		if ($CI->config->item('mailUser'))
			$mail->Username = $CI->config->item('mailUser');  // SMTP username
		
		if ($CI->config->item('mailPass'))
			$mail->Password = $CI->config->item('mailPass'); // SMTP password
		
		$mail->From = $CI->config->item('mailFrom');
		$mail->FromName = $CI->config->item('mailFromName');
		
		if ($CI->config->item('testEmailFlag')) {

			$mail->Subject = $CI->config->item('testEmailFlagSubjectText') . $subject;
			$mail->Body = $CI->config->item('testEmailFlagBodyText') . $body;

		} else {

			$mail->Subject = $subject;
			$mail->Body = $body;

		}
		
		if ($bccRecipients)
			$mail->AddAddress($CI->userinfo->login);
		
		
		//echo $mail->ErrorInfo;
		
		$testRecpList = implode(",",$recipients);
			
		if (!$CI->config->item('testServer')) {
			
			foreach ($recipients as $key => $recipient) {
				
				// validate emails first!
				
				if(preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $recipient)) {
				
				//if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $recipient)) {
					
					//if ($bccRecipients)
						$mail->AddBCC($recipient);
					//else
					//	$mail->AddAddress($recipient);
					
				}
				
			}
			
		} else {
		
			
			
			$mail->Body .= " \n\nTHIS IS A TEST EMAIL \n\n RECIPIENTS WOULD HAVE BEEN: " . $testRecpList;
			//echo "if this was on the live server, email would be sent to: $testRecpList.<br><Br> Email actually sent to tim@studio211.us<br><br>";
			$mail->AddAddress('timedgar@mac.com');
			//$mail->AddAddress('ute.linhart@bravado.com');
		
		}
		
		
		
		if ($recipients) {
			
			
			// check if the currently logged in user is on the email. if not, cc them.
				
			if (isset($CI->userinfo->login)) {
			
				$internalUGs = $CI->usergroups_model->getChildren($CI->config->item('bravadoInternalGroupID'));
				$internalUGs[] = $CI->config->item('bravadoInternalGroupID');
			
				if (in_array($CI->userinfo->usergroupid, $internalUGs) || in_array($CI->userinfo->usergroupid2, $internalUGs)) {
			
					if(!in_array($CI->userinfo->login,$recipients) || preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $CI->userinfo->login)) { //eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $CI->userinfo->login)
						
						// lastly, check if the pref is checked!
						
						if (in_array('cc_on_emails', $CI->userinfo->prefs))
							$mail->addCC($CI->userinfo->login);
						
					}
				}
			}
			
			if ($mail->Send()) {
			
				opmLog("Email '".$subject. " " . $body . "' sent to " . $testRecpList);
				//die("email was sent");
				return true;
			} else {
				$baseurl = base_url();
				$error = "Mail Not Sent! Mailer error:" . $mail->ErrorInfo;
				
				if (isset($testRecpList))
					$error .= " Recipient list was: " . $testRecpList;
					
				$CI->opm->displayError($error);
				return false;
			}
		
		}
		
	}

	
	function sendProductEmail($opm_productid,$email_type,$arrData = array()) { // send emails pertaining to a product to users in usergroups with view privs of that product!
	
		$CI =& get_instance();
		$recipients = array();
		$newRecipients = array();
		
		$CI->load->model('products_model');
		$CI->load->model('users_model');
		$product = $CI->products_model->fetchProductInfo($opm_productid);
		$arrData['productInfo'] = $product;
		
		$strUsergroupIDs = implode(",",$product->usergroups);
		
		if ($users = $CI->users_model->fetchUsersFromUsergroups($strUsergroupIDs)) {
	
			//print_r($users);
			//die();
	
			foreach ($users->result() as $user) {
			
				// first, lets make sure user has view rights for this product.
				
				//if ($CI->opm->checkProductViewPerms($opm_productid,$user->userid)) {
			
					// then, lets see if the user is limited to certain properties and, if so, is this property in their list?
					
					$userinfo = $CI->users_model->fetchUserInfo($user->userid);

					// for "receive all emails for which I am a licensee" - set a var if user is "product licensee," then 
					
					
					
					if (is_array($product->licensees)) {
					
						$licids = array();
					
						foreach ($product->licensees as $l) {
							
							$licids[] = $l['usergroupid'];
							
						}
						
						if (in_array($userinfo->usergroupid, $licids) || in_array($userinfo->usergroupid2, $licids))
							$userinfo->isProductLicensee = true;
						else
							$userinfo->isProductLicensee = false;
					
					} else {
						
						$userinfo->isProductLicensee = false;
						
					}
					
					
					
					if ( ( sizeof($userinfo->prefProperties) == 0 ) || in_array($product->propertyid, $userinfo->prefProperties)) { // user has not limited their emails to certain properties, or the property of this product is in that list!
				
						$user->prefs = explode(",",$user->preferences); // put preferences into an array
					
						$userinfo->receiveAllEmails = false;
					
						if (in_array('receive_all_emails', $user->prefs))
							$userinfo->receiveAllEmails = true;
							
						if (in_array('recieve_all_emails_product_licensee', $user->prefs) && $userinfo->isProductLicensee)
							$userinfo->receiveAllEmails = true;
					
					
						if ($email_type == 'approval_status_changed') { // this is an approval email!
						
							if ($userinfo->receiveAllEmails || in_array('receive_approval_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
						if ($email_type == 'sample_approval_status_changed') { // this is a sample approval email!
						
							if ($userinfo->receiveAllEmails || in_array('receive_sample_approval_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
						if ($email_type == 'new_image_uploaded') { // this is an image email!
						
							if ($userinfo->receiveAllEmails || in_array('receive_imageuploaded_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
						if ($email_type == 'new_comment_posted') { // this is a comment email!
							
							if ($userinfo->receiveAllEmails || in_array('receive_comment_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
						if ($email_type == 'masterfile_uploaded') { // this is a master file email!
							
							if ($userinfo->receiveAllEmails || in_array('receive_masterfile_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
						if ($email_type == 'separation_uploaded') { // this is a separation email!
							
							if ($userinfo->receiveAllEmails || in_array('receive_separation_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
						if ($email_type == 'asset_uploaded') { // this is a separation email!
							
							if ($userinfo->receiveAllEmails || in_array('receive_asset_emails', $user->prefs))
								$recipients[] = array("userid"=>$user->userid,"email"=>$user->login);
								
						}
						
					}
				
				//}
			
			}
		
		}
		
		// now filter recipients based on perms...
		
		
		if ($recipients) {
		
			foreach ($recipients as $u) {
			
				if ($CI->opm->checkProductViewPerms($opm_productid,$u['userid'])) {
				
					$newRecipients[] = $u['email'];
				
				}
			
			}
		
		}
				
		
		$subject = $CI->load->view('emails/'.$email_type.'_subject',$arrData,true);
		$body = $CI->load->view('emails/'.$email_type.'_body',$arrData,true);
					
		// make sure we have recipients before we send!
		
		if ($newRecipients) {
		
			if ($CI->opm->sendEmail($subject,$body,$newRecipients))
				return true;
			else
				return false;
		
		}
				
	}
	
		
	/*
	
	function sendProductEmail($opm_productid,$email_type,$arrData = array()) { // send emails pertaining to a product to users in usergroups with view privs of that product!
	
		$CI =& get_instance();
		$recipients = array();
		
		$CI->load->model('products_model');
		$CI->load->model('users_model');
		$product = $CI->products_model->fetchProductInfo($opm_productid);
		$arrData['productInfo'] = $product;
		
		$strUsergroupIDs = implode(",",$product->usergroups);
		
		if ($users = $CI->users_model->fetchUsersFromUsergroups($strUsergroupIDs)) {
	
			foreach ($users->result() as $user) {
			
				// first, lets make sure user has view rights for this product.
				
				if ($CI->opm->checkProductViewPerms($opm_productid,$user->userid)) {
			
					// then, lets see if the user is limited to certain properties and, if so, is this property in their list?
					
					$userinfo = $CI->users_model->fetchUserInfo($user->userid);
					
					
					if ( ( sizeof($userinfo->prefProperties) == 0 ) || in_array($product->propertyid, $userinfo->prefProperties)) { // user has not limited their emails to certain properties, or the property of this product is in that list!
				
						$user->prefs = explode(",",$user->preferences); // put preferences into an array
					
						if ($email_type == 'approval_status_changed') { // this is an approval email!
						
							if (in_array('receive_all_emails', $user->prefs) || in_array('receive_approval_emails', $user->prefs))
								$recipients[] = $user->login;
								
						}
						
						if ($email_type == 'new_image_uploaded') { // this is an image email!
						
							if (in_array('receive_all_emails', $user->prefs) || in_array('receive_imageuploaded_emails', $user->prefs))
								$recipients[] = $user->login;
								
						}
						
						if ($email_type == 'new_comment_posted') { // this is a comment email!
							
							if (in_array('receive_all_emails', $user->prefs) || in_array('receive_comment_emails', $user->prefs))
								$recipients[] = $user->login;
								
						}
						
						if ($email_type == 'masterfile_uploaded') { // this is a master file email!
							
							if (in_array('receive_all_emails', $user->prefs) || in_array('receive_masterfile_emails', $user->prefs))
								$recipients[] = $user->login;
								
						}
						
						if ($email_type == 'separation_uploaded') { // this is a separation email!
							
							if (in_array('receive_all_emails', $user->prefs) || in_array('receive_separation_emails', $user->prefs))
								$recipients[] = $user->login;
								
						}
						
						if ($email_type == 'asset_uploaded') { // this is a separation email!
							
							if (in_array('receive_all_emails', $user->prefs) || in_array('receive_asset_emails', $user->prefs))
								$recipients[] = $user->login;
								
						}
						
					}
				
				}
			
			}
		
		}
		
		$subject = $CI->load->view('emails/'.$email_type.'_subject',$arrData,true);
		$body = $CI->load->view('emails/'.$email_type.'_body',$arrData,true);
		
		print_r($recipients);
		die();
					
		// make sure we have recipients before we send!
		
		if ($recipients) {
		
			if ($CI->opm->sendEmail($subject,$body,$recipients))
				return true;
			else
				return false;
		
		}
				
	}
	
	*/

	
	function sendInvoiceEmail($invoiceid,$email_type,$arrData = array()) { // send emails pertaining to a product to users in usergroups with view privs of that product!
	
		$CI =& get_instance();
		$recipients = array();
		
		$CI->load->model('invoices_model');
		$CI->load->model('users_model');
		
		$invoice = $CI->invoices_model->fetchInvoice($invoiceid);
		
		$ownerInfo = $CI->users_model->fetchUserInfo($invoice->ownerid,true);
		
		$arrData['invoice'] = $invoice;
		
		$subject = $CI->load->view('emails/'.$email_type.'_subject',$arrData,true);
		$body = $CI->load->view('emails/'.$email_type.'_body',$arrData,true);
					
		
		
		$recipients[] = $ownerInfo->login;
		
		// now add ccs
		
		foreach ($invoice->cc as $u) {
		
			$recipients[] = $u->login;
		
		}
		
		// make sure we have recipients before we send!
		
		if ($recipients) {
		
			if ($CI->opm->sendEmail($subject,$body,$recipients))
				return true;
			else
				return false;
		
		}
				
	}
	
	function updateApprovalStatusProperty($propertyid) {
	
		$CI =& get_instance();
		
		$CI->load->model('products_model');
		$CI->load->model('approvalstatus_model');
		
		$products = $CI->products_model->getProductsFromProperty($propertyid);
				
		foreach ($products->result() as $p) {

			$CI->approvalstatus_model->updateApprovalStatus($p->opm_productid);
		
		}
		
		return true;
	
	}

	
	function addHistoryItem($opm_productid,$event) {
	
		$CI =& get_instance();
		
		$CI->load->model('history_model');
		
		if ($opm_productid && $event)
			$CI->history_model->addHistoryItem($opm_productid,$event);
		else
			return false;
	
	}
	
	function addInvoiceHistoryItem($invoiceid,$event) {
	
		$CI =& get_instance();
		
		$CI->load->model('history_model');
		
		if ($invoiceid && $event)
			$CI->history_model->addInvoiceHistoryItem($invoiceid,$event);
		else
			return false;
	
	}
	
	function setLastModified($opm_productid) {

		$CI =& get_instance();
		
		$CI->load->model('products_model');
		$CI->products_model->setLastModified($opm_productid);
		
		return true;

	}
	
	function setViewTimestamp($opm_productid) {

		$CI =& get_instance();
		
		$CI->load->model('products_model');
		$CI->products_model->setViewTimestamp($opm_productid);
		
		return true;

	}
	

    function displayError($errortext, $url = 0)
    {
    	$CI =& get_instance();
    	
    	$template['page_title'] = 'Error';
    	
    	if ($url)
    		$template['redirectUrl'] =  $url;
    	    		
    	$template['content'] = "ERROR: " . $errortext;
    
    	$CI->load->view('global/alerttemplate', $template);
    	echo $CI->output->get_output();
    	exit();
    	
    }
    
    function displayAlert($alerttext, $url = 0)
    {
    	$CI =& get_instance();
    	
    	if ($url) { // we have a redirect url, put the alert in flashdata and display on page, else display on new page! as per Ute
    	
    		$CI->session->set_flashdata('alert', $alerttext);
    		redirect($url);
    		    	
    	} else {
    	
    		$template['page_title'] = '';
    	
			//if ($url)
			//	$template['redirectUrl'] =  $url;
			
			
			$template['content'] = "ALERT: " . $alerttext;
		
			$CI->load->view('global/alerttemplate', $template);
			echo $CI->output->get_output();
			exit();
    	
    	}
    
    	
    	
    }
    
    
    
    function displayUsergroups($opm_productid = 0, $view = 'product/involvement_usergroup.php') { // this displays the usergroups on the involvement page
    	
    	$CI =& get_instance();
    	
    	$CI->load->model('usergroups_model');
    	$usergroups = $CI->usergroups_model->fetchUsergroups($opm_productid,null,false,false);
    	
    	$html = "";
    	
    	foreach ($usergroups AS $ug) {
    		
    		if (!in_array($ug['usergroupid'], $CI->config->item('MultipleSelectUGs'))) {
    		
	    		if (isset($ug['children']))
	    			$ug['has_children'] = true;
	    		else
	    			$ug['has_children'] = false;
	    			
	    		if (isset($ug['isassigned']))
	    			$ug['isassigned'] = true;
	    		else
	    			$ug['isassigned'] = false;
	    			
	    		$ug['table_width'] = 290;
	    		$ug['opm_productid'] = $opm_productid;
	    		$html .= $CI->load->view($view, $ug, true);
	    		
	    		if (isset($ug['children'])) {
	    			
	    			$html .= "<div id=\"usergroup_".$ug['usergroupid']."_children\" style=\"display:none; margin-left:20px;\">";
	    			
	    			foreach ($ug['children'] as $ug) {
	   
						if (isset($ug['children']))
							$ug['has_children'] = true;
						else
							$ug['has_children'] = false;
	    			
	    				if (isset($ug['isassigned']))
							$ug['isassigned'] = true;
						else
							$ug['isassigned'] = false;
	    			
	    				$ug['table_width'] = 270;
	    				$ug['hidden'] = true;
	    				$ug['opm_productid'] = $opm_productid;
	    				$html .= $CI->load->view($view, $ug, true);
	    				
						if (isset($ug['children'])) {
							
							$html .= "<div id=\"usergroup_".$ug['usergroupid']."_children\" style=\"display:none; margin-left:20px;\">";
							
							foreach ($ug['children'] as $ug) {
			   
								if (isset($ug['children']))
									$ug['has_children'] = true;
								else
									$ug['has_children'] = false;
							
								if (isset($ug['isassigned']))
									$ug['isassigned'] = true;
								else
									$ug['isassigned'] = false;
							
								$ug['table_width'] = 250;
								$ug['hidden'] = true;
								$ug['opm_productid'] = $opm_productid;
								$html .= $CI->load->view('product/involvement_usergroup.php', $ug, true);
								
								if (isset($ug['children'])) {
									
									$html .= "<div id=\"usergroup_".$ug['usergroupid']."_children\" style=\"display:none; margin-left:20px;\">";
									
									foreach ($ug['children'] as $ug) {
					   
										if (isset($ug['children']))
											$ug['has_children'] = true;
										else
											$ug['has_children'] = false;
									
										if (isset($ug['isassigned']))
											$ug['isassigned'] = true;
										else
											$ug['isassigned'] = false;
									
										$ug['table_width'] = 230;
										$ug['hidden'] = true;
										$ug['opm_productid'] = $opm_productid;
										$html .= $CI->load->view($view, $ug, true);
									
									}
									
									$html .= "\n\n</div>\n\n";
										
								
								}
							
							}
							
							$html .= "\n\n</div>\n\n";
								
						
						}
	    			
	    			}
	    			
	    			$html .= "\n\n</div>\n\n";
	    				
	    		
	    		}
	    		
	    	}
	    	
	    }
    	
    	echo $html;
    	
    }
    
    
    function displayTerritories($opm_productid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('territories_model');
     	
    	$territories = $CI->territories_model->fetchTerritories($opm_productid); 
    	
    	//print_r($territories);
    
    	$html = "";
    	
    	foreach ($territories as $territory) {
    	
	   		$html .= $CI->load->view('product/involvement_territory.php', $territory, true);	
    	
    	}
    	
    	echo $html;
    	

    }
    
    function displayRights($opm_productid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('rights_model');
     	
    	$rights = $CI->rights_model->fetchRights($opm_productid); 
    	
    	//print_r($rights);
    
    	$html = "";
    	
    	foreach ($rights as $right) {
    	
	   		$html .= $CI->load->view('product/involvement_right.php', $right, true);	
    	
    	}
    	
    	echo $html;
    	

    }
    
    function displayChannels($opm_productid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('channels_model');
     	
    	$channels = $CI->channels_model->fetchChannels($opm_productid); 
    	    	
    	//print_r($rights);
    
    	$html = "";
    	
    	foreach ($channels as $channel) {
    	
    		
	   		$html .= $CI->load->view('product/involvement_channel.php', $channel, true);	
    	
    	}
    	
    	echo $html;
    	
    }
    
    
    function displayPropertyTerritories($propertyid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('territories_model');
     	
    	$territories = $CI->territories_model->fetchPropertyTerritories($propertyid); 
    
    	$html = "";
    	
    	foreach ($territories as $territory) {
    		
    		$territory['propertyid'] = $propertyid;
    	
	   		$html .= $CI->load->view('properties/territories.php', $territory, true);	
    	
    	}
    	
    	echo $html;
    	

    }
    
    function displayOfficeTerritories($officeid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('territories_model');
     	
    	$territories = $CI->territories_model->fetchOfficeTerritories($officeid); 
    
    	$html = "";
    	
    	foreach ($territories as $territory) {
    		
    		$territory['officeid'] = $officeid;
    	
	   		$html .= $CI->load->view('offices/territories.php', $territory, true);	
    	
    	}
    	
    	echo $html;
    	

    }
    
    function displayPropertyRights($propertyid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('rights_model');
     	
    	$rights = $CI->rights_model->fetchPropertyRights($propertyid); 
    
    	$html = "";
    	
    	foreach ($rights as $right) {
    		
    		$right['propertyid'] = $propertyid;
    	
	   		$html .= $CI->load->view('properties/rights.php', $right, true);	
    	
    	}
    	
    	echo $html;
    	

    }
    

	function displayPropertyChannels($propertyid) {
    
    	$CI =& get_instance();
    	
     	$CI->load->model('channels_model');
     	
    	$channels = $CI->channels_model->fetchPropertyChannels($propertyid); 
    
    	$html = "";
    	
    	foreach ($channels as $channel) {
    		
    		$channel['propertyid'] = $propertyid;
    	
	   		$html .= $CI->load->view('properties/channels.php', $channel, true);	
    	
    	}
    	
    	echo $html;
    	

    }
    
    
    function displayAvatar($userid) {
    
    	$CI =& get_instance();
    	
    	$CI->load->model('users_model');
    	
    	if ( ($user = $CI->users_model->fetchUserInfo($userid)) && ($user->avatar_path) ) {
    	
    		echo '<img src="'.base_url().'imageclass/viewAvatar/'.$user->userid.'" class="avatarImage" style="margin-left:15px;">';
    	
    	} else {
    	
    		echo '<img src="'.base_url().'resources/images/avatars/person_avatar.gif" class="avatarImage" style="margin-left:15px;">';
    	
    	}
    
    }
    
    function displayPropertyImage($propertyID) {
    
    	$CI =& get_instance();
    	
    	$CI->load->model('properties_model');
    	
    	if ( ($p = $CI->properties_model->fetchPropertyInfo($propertyID)) && ($p->image_path) ) {
    	
    		echo '<img src="'.base_url().'resources/files/propertyimages/'.$p->image_path.'" class="propertyImage" style="margin-right:20px;" align="absmiddle" border="0">';
    	
    	} else {
    	
    		// echo '<img src="'.base_url().'resources/images/avatars/person_avatar.gif" class="avatarImage" style="margin-left:15px;">';
    	
    	}
    
    }
    
    function updateAvailableTerritories($opm_productid) {
    
    	$CI =& get_instance();
    	
    	$CI->load->model('products_model');
    	
    	if ($CI->products_model->updateAvailableTerritories($opm_productid))
    		return true;
    
    }
    
    function updateAvailableRights($opm_productid) {
    
    
    	$CI =& get_instance();
    	
    	$CI->load->model('products_model');
    	
    	if ($CI->products_model->updateAvailableRights($opm_productid))
    		return true;
    
  
    }
    
    function updateAvailableChannels($opm_productid) {
    
    
    	$CI =& get_instance();
    	
    	$CI->load->model('products_model');
    	
    	if ($CI->products_model->updateAvailableChannels($opm_productid))
    		return true;
    
  
    }
    
    function updateAvailableTerritoriesProp($propertyid) { // update all the products in a given property
    
    	$CI =& get_instance();
    	
    	$CI->load->model('products_model');

    	if ($CI->products_model->updateAvailableTerritoriesProp($propertyid))
    		return true;
    
    }
    
    function updateAvailableRightsProp($propertyid) { // update all the products in a given property
    
    	$CI =& get_instance();
    	
    	$CI->load->model('products_model');

    	if ($CI->products_model->updateAvailableRightsProp($propertyid))
    		return true;
    
    }
    
    function updateAvailableChannelsProp($propertyid) { // update all the products in a given property
    
    	$CI =& get_instance();
    	
    	$CI->load->model('products_model');

    	if ($CI->products_model->updateAvailableChannelsProp($propertyid))
    		return true;
    
    }
    
    function createProductExcel($objData) {
    	
    	/*
    	echo "<pre>";
    	print_r($objData->result());
    	echo "</pre>";
    	die();
    	*/
    	
    	$CI =& get_instance();
    	
    	$CI->load->library('PHPExcel');
        $CI->load->library('IOFactory');
        $CI->load->model('products_model');
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Bravado OPM")->setLastModifiedBy("Bravado OPM")->setTitle("Bravado OPM Excel Export");
									
		$objPHPExcel->setActiveSheetIndex(0);
		
		//$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(400);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	
		$objPHPExcel->getActiveSheet()->setCellValue('B1', "OPM Product ID");
		$objPHPExcel->getActiveSheet()->setCellValue('C1', "Product Code");
		$objPHPExcel->getActiveSheet()->setCellValue('D1', "Property");
		$objPHPExcel->getActiveSheet()->setCellValue('E1', "Product Name");
		$objPHPExcel->getActiveSheet()->setCellValue('F1', "Category");
		$objPHPExcel->getActiveSheet()->setCellValue('G1', "# Masterfiles");
		$objPHPExcel->getActiveSheet()->setCellValue('H1', "# Separations");
		$objPHPExcel->getActiveSheet()->setCellValue('I1', "Approval Status");



    
    	$count = 2;
    
    	foreach ($objData->result() as $row) {
    	
    		$objPHPExcel->getActiveSheet()->getRowDimension($count)->setRowHeight(150);
    	
    		
    	
    		// now lets add the thumbnail!
    		
    		$base_url = base_url();
    		
    		//$filename = $base_url . 'imageclass/viewThumbnail/'.$row->default_imageid.'/100';
    		/*$webRoot = $CI->config->item('webrootPath');
			$filename = $webRoot . "resources/images/eddie.jpg";
			$handle = fopen($filename, "r");
			$imageString = fread($handle, filesize($filename));
			fclose($handle);
    		
    		//header("Content-type: image/gif");
			//echo $imageString;
			//return true;*/
			
			if ($image = $CI->products_model->fetchImage($row->default_imageid)) {
				
				//print_r($row);
				//die();
				
				$filePath = $CI->config->item('fileUploadPath') . "visuals/" . $image->imageid;
    	
    			$fh = fopen($filePath, 'r');
				$imageData = fread($fh, filesize($filePath));
				fclose($fh);
								
				$size = 150;  // new image width
				$src = imagecreatefromstring($imageData); 
				$width = imagesx($src);
				$height = imagesy($src);
				$aspect_ratio = $height/$width;
				
				if ($width <= $size) {
					$new_w = $width;
					$new_h = $height;
				} else {
					$new_w = $size;
					$new_h = 150;//abs($new_w * $aspect_ratio);
				}
				
				$img = imagecreatetruecolor($new_w,$new_h); 
				  
				imagecopyresampled($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
				
				//$gdImage = imagecreatefromjpeg($base_url . 'imageclass/viewThumbnail/'.$row->default_imageid.'/100');
	
				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Logo');
				$objDrawing->setDescription('Logo');
				
				
				
				$objDrawing->setImageResource($img);

				$objDrawing->setHeight(150);
				$objDrawing->setWidth(150);
				
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	
				$objDrawing->setCoordinates('A'.$count);
				
			}
			
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $count, $row->opm_productid);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $count, $row->productcode);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $count, $row->property);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $count, $row->productname);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $count, $row->category);
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $count, $row->numMasterFiles);
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $count, $row->numSeparations);
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $count, $row->approvalstatus);
			

    		$count++;
    	
    	}
    	
    	$objPHPExcel->getActiveSheet()->setTitle('Simple');
			
			
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
				
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Bravado_Product_Export_'.mktime().'.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); 
		exit;
    
    }
    
    function createPropertyExcel() {
    	
    	$CI =& get_instance();
    	
    	$CI->load->library('PHPExcel');
        $CI->load->library('IOFactory');
        $CI->load->model('properties_model');
        $CI->load->model('invoices_model');
        
        $reportData = $CI->properties_model->fetchPropertyReportData();
       
       	$channelList = $CI->invoices_model->fetchChannels();
       	
       	// create array of channels for display, with LETTER OF COLUMN
       
       	$chrNumber = 68; // the ascii character which is the first rate column
       
       	foreach ($channelList->result() as $c) {
       	
       		$channels[$c->channelcode] = array (
       		
       			"channel"=>$c->channel,
       			"columnLetter"=>chr($chrNumber)
       		
       		);
       		
       		$chrNumber++;
       	
       	}
       		
       	
       /*	echo "<pre>";
       	print_r($reportData);
       	die();*/
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator("Bravado OPM")->setLastModifiedBy("Bravado OPM")->setTitle("Bravado OPM Excel Export");
									
		$objPHPExcel->setActiveSheetIndex(0);
		
		//$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(400);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);

	
		$objPHPExcel->getActiveSheet()->setCellValue('B1', "Property ID");
		$objPHPExcel->getActiveSheet()->setCellValue('C1', "Property Name");
		
		$chrNumber = 68;
		
		foreach ($channels as $cid=>$c) {

			$objPHPExcel->getActiveSheet()->setCellValue( $c['columnLetter'] . '1', $c['channel']);

		}

    	$count = 2;
    	
    	foreach ($reportData as $d) {
			
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $count, $d['id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $count, $d['property']);

			foreach ($channels as $ccode=>$c) {
			
				
				$objPHPExcel->getActiveSheet()->setCellValue($c['columnLetter'] . $count, $d[$ccode]);
			
			}

    		$count++;
    	
    	}
    	
    	$objPHPExcel->getActiveSheet()->setTitle('Property Export');
			
			
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
				
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Bravado_Property_Export_'.mktime().'.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); 
		exit;
    
    }
    
    
    
	function text_decrypt_symbol($s, $i) {
		$CI =& get_instance();
	# $s is a text-encoded string, $i is index of 2-char code. function returns number in range 0-255
	
			return (ord(substr($s, $i, 1)) - $CI->config->item('START_CHAR_CODE'))*16 + ord(substr($s, $i+1, 1)) - $CI->config->item('START_CHAR_CODE');
	}
	
	function text_decrypt($s) {
		$CI =& get_instance();
		//global $START_CHAR_CODE, $CRYPT_SALT;
		
		$result = '';
	
		if ($s == "")
			return $s;
		$enc = $CI->config->item('CRYPT_SALT') ^ $CI->opm->text_decrypt_symbol($s, 0);
		for ($i = 2; $i < strlen($s); $i+=2) { # $i=2 to skip salt
			$result .= chr($CI->opm->text_decrypt_symbol($s, $i) ^ $enc++);
			if ($enc > 255)
				$enc = 0;
		}
		return $result;
	}
	
	function text_crypt_symbol($c) {
		$CI =& get_instance();
	# $c is ASCII code of symbol. returns 2-letter text-encoded version of symbol
	
			return chr($CI->config->item('START_CHAR_CODE') + ($c & 240) / 16).chr($CI->config->item('START_CHAR_CODE') + ($c & 15));
	}
	
	function text_crypt($s) {
		$CI =& get_instance();
		//global $START_CHAR_CODE, $CRYPT_SALT;
	
		if ($s == "")
			return $s;
		$enc = rand(1,255); # generate random salt.
		$result = $CI->opm->text_crypt_symbol($enc); # include salt in the result;
		$enc ^= $CI->config->item('CRYPT_SALT');
		for ($i = 0; $i < strlen($s); $i++) {
			$r = ord(substr($s, $i, 1)) ^ $enc++;
			if ($enc > 255)
				$enc = 0;
			$result .= $CI->opm->text_crypt_symbol($r);
		}
		return $result;
	}
	
	function buildPermissionQuery() {
	
		/*
		
			THIS JOIN IS USED BY SEVERAL QUERIES, TO FIGURE OUT IF USER HAS PERMISSION TO VIEW PRODUCT, PROPERTY, ETC. 
			IT IS NICE TO HAVE THIS IN ONE PLACE, SHOULD PERMISSION NEEDS CHANGE IN THE FUTURE.
		
		*/
		
		$CI =& get_instance();
		$CI->load->model('usergroups_model');
	
		// is user administrator?
		
		if (checkPerms('view_all_products'))  { // yes!
			
			
			if (checkPerms('restrict_by_territories') && !in_array($CI->userinfo->userid, $CI->config->item('superAdmins')) )  { // yes!	
			
				define('USE_PERMISSION_QUERY', true);
				
				// build territories str
				
				$terrIDs = array();
				
				foreach ($CI->userinfo->territories as $t) {
				
					if ($t['isassigned'] || $t['isinherited']) {
					
						$terrIDs[] = $t['territoryid'];
					
					}
				
				}
				
				$strTerrIDs = implode(",",$terrIDs);
				
				if (!$strTerrIDs)
					$strTerrIDs = "0";
				
				/*echo "<pre>";
				
				print_r($terrIDs);
				print_r($CI->userinfo);
				die();*/
					
					
				$sql = "
					
					LEFT JOIN (
						
							SELECT opm_products.opm_productid AS id
							FROM opm_products
							LEFT JOIN opm_products_territories ON opm_products_territories.opm_productid = opm_products.opm_productid
							LEFT JOIN opm_property_territories ON opm_property_territories.propertyid = opm_products.propertyid

							WHERE   (
							
										(opm_property_territories.id IS NOT NULL AND (opm_products_territories.isexception <> 1 OR opm_products_territories.id IS NULL))
									 
										OR 
									
										(opm_products_territories.id IS NOT NULL AND opm_products_territories.isexception <> 1) 
									
										OR
										
										(opm_products.createdby = ".$this->userinfo->userid.")
									
									)
							 
							 		AND 
							 		
							 			(opm_products_territories.territoryid IN (".$strTerrIDs.") OR opm_property_territories.territoryid IN (".$strTerrIDs."))
							 			
							 		
							 
							GROUP BY opm_products.opm_productid							
					
						) AS canview ON canview.id = opm_products.opm_productid
				
				";
		
			} else {
			
				
				if (!in_array($CI->userinfo->userid, $CI->config->item('superAdmins'))) {
					
					define('USE_PERMISSION_QUERY', true);
		
					$sql = "
						
						LEFT JOIN (
							
								SELECT opm_products.opm_productid AS id
								FROM opm_products
								LEFT JOIN properties ON properties.propertyid = opm_products.propertyid
								WHERE properties.ishidden <> 1
						
							) AS canview ON canview.id = opm_products.opm_productid
					
					";
					
					
				} else {
					
					define('USE_PERMISSION_QUERY', false);

				}
				
					
			
			}
			
		
		} else { // user is not administrator
		
			define('USE_PERMISSION_QUERY', true);
			
			// get all children of propertycontact and designer user groups
			
			// we have to deal with them differently, because a product must have property contacts UG view rights as well as having that particular contact assigned in order to be viewable by that contact... no?
			
			$designerUGs = $CI->usergroups_model->getChildren($CI->config->item('designersGroupID'));
			$designerUGs[] = $CI->config->item('designersGroupID');
			
			//print_r($designerUGs);
			//die();
			
			$pcUGs = $CI->usergroups_model->getChildren($CI->config->item('propertyContactsGroupID'));
			$pcUGs[] = $CI->config->item('propertyContactsGroupID');
			
			$liUGs = $CI->usergroups_model->getChildren($CI->config->item('licenseeGroupID'));
			$liUGs[] = $CI->config->item('licenseeGroupID');
			$liUGs[] = 592; //temp TE
			
			// below is for new "view only" group - which allows for viewing of a whole property a la licensees (why it is lumped in w them)
			
			$voUGs = $CI->usergroups_model->getChildren($CI->config->item('viewOnlyPropGroupID'));
			$voUGs[] = $CI->config->item('viewOnlyPropGroupID');
			
			$liUGs = array_merge($voUGs,$liUGs);
			
			//$arrRegularUGs = array();
			//$arrSpecialUGs = array();
			
			//$arrRegularUGs[] = 0; // kludge
			
			foreach($CI->userinfo->viewRightsUserGroups as $ugid) {
			
				if ($ugid > 0) {
			
					if (in_array($ugid, $designerUGs)) // user belongs to designer user groups
						$arrDesignerUGs[] = $ugid;
					else if (in_array($ugid, $pcUGs)) // user belongs to property contact groups
						$arrContactUGs[] = $ugid;
					else if (in_array($ugid, $liUGs)) // user belongs to licensee groups
						$arrLicenseeUGs[] = $ugid;
					else
						$arrRegularUGs[] = $ugid;
					
				}
			
			}
			
			
			// remove all 0s from arrRegularUGs.
			
			//array_filter($arrRegularUGs);
		
			$strUGs = @implode(",", $arrRegularUGs);
			//$strSpecialUGs = implode(",", $arrSpecialUGs);
		
			$sql = "  INNER JOIN ( ";
					
			
			$sql .= "		 ";
						
			if ($strUGs)
				$sql .= buildPermSQL("opm_products_usergroups.usergroupid IN ($strUGs)") . " UNION ";
						
			if (isset($arrDesignerUGs)) {
				$strDesignerUGs = implode(",", $arrDesignerUGs);
				$sql .= buildPermSQL("opm_products_designers.userid = '".$this->userinfo->userid."' AND opm_products_usergroups.usergroupid IN (".$strDesignerUGs.")",true) . " UNION ";

			}
			
			if ($this->userinfo->approvalProperties) { // user is approval contact for some properties
			
				$strContactUGs = implode(",", $pcUGs);
				$sql .= buildPermSQL("opm_user_app_properties.userid = '".$this->userinfo->userid."' AND opm_products_usergroups.usergroupid IN (".$strContactUGs.")",false,true) . " UNION ";
			
			}
			
			if (isset($arrLicenseeUGs)) { //  this handles products which are assigned to licensees individually
				
				$strLicenseeUGs = implode(",", $arrLicenseeUGs);
				$sql .= buildPermSQL("(opm_products_licensees.usergroupid = '".$this->userinfo->usergroupid."' OR opm_products_licensees.usergroupid = '".$this->userinfo->usergroupid2."')",false,false,false,true) . " UNION ";

			}
			
			if (isset($arrLicenseeUGs)) { // this handles products which are visible to licensees as a group
			
				$arrLicenseeUGs[] = $CI->config->item('licenseeGroupID');
				$strLicenseeUGs = implode(",", $arrLicenseeUGs);
				

				if (in_array($this->userinfo->usergroupid, $liUGs) && in_array($this->userinfo->usergroupid2, $liUGs)) {
					
					// both user's groups are licensee groups (RARE).
				
					$sql .= buildPermSQL("(opm_usergroup_properties.usergroupid = '".$this->userinfo->usergroupid."' OR opm_usergroup_properties.usergroupid = '".$this->userinfo->usergroupid2."') AND opm_products_usergroups.usergroupid IN (".$strLicenseeUGs.")",false,false,true);	

				
				} else if (in_array($this->userinfo->usergroupid, $liUGs)) {
				
					// only user's primary UG is a licensee group
				
					$sql .= buildPermSQL("(opm_usergroup_properties.usergroupid = '".$this->userinfo->usergroupid."') AND opm_products_usergroups.usergroupid IN (".$strLicenseeUGs.")",false,false,true);	

				
				} else if (in_array($this->userinfo->usergroupid, $liUGs)) {
				
					// only user's secondary UG is a licensee group

				
					$sql .= buildPermSQL("(opm_usergroup_properties.usergroupid = '".$this->userinfo->usergroupid2."') AND opm_products_usergroups.usergroupid IN (".$strLicenseeUGs.")",false,false,true);	
				
				}
				
			
			}
			
			// strip off last "UNION"
			
			$endOfSQL = substr($sql, strlen($sql)-6, 6);
			
			if ($endOfSQL == 'UNION ') {
			
				$sql = substr($sql,0,strlen($sql)-6);
			
			}
			
			// add products which have been created by logged in user.
			
			// if we have preceeding rules, add UNION
			
			if ($sql != "  INNER JOIN ( " . "		 ")
				$sql .= "UNION ";
			
			$sql .= " SELECT opm_products.opm_productid as id FROM opm_products WHERE opm_products.createdby = " . $this->userinfo->userid ." ";
			
			
												
				
			$sql .= ") AS canview ON canview.id = opm_products.opm_productid";
					
			//die($sql);
		
		}
	
		define('PERMISSION_QUERY', $sql);
	
	}
	
	function createPDF($grabsheetid, $download = false, $saveToFile = false, $lowRez = false) {
		
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
			
				define('GRABSHEET_PROPERTYIMAGEPATH',"/files/propertyimages/" . $property_imageinfo['image_path']);
			
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
	
	function getRoyaltyTotals($reportData,$propertyid) {
	
		// lets first get an array of all channels
		
		$CI =& get_instance();
    	$CI->load->model('invoices_model');
    	
    	$chnls = $CI->invoices_model->fetchChannels($propertyid);
		$channels = array();
		
		$totals['subTotal'] = 0.00;
		$totals['total'] = 0.00;
		$totals['channels'] = array();
		
		
		
		foreach ($chnls->result() as $c) {
			//print_r($c);
			$totals['channels'][$c->channelcode] = array("channel"=>$c->channel,"subTotal"=>0.00,"recoupRate"=>$c->rate,"total"=>0);
		
		}
		
		/*echo "<pre>";
		
			print_r($reportData);
		
		echo "</pre>";*/
		
		// now lets get totals for each channel, and an overall total
		
		if (is_array($reportData)) {
		
			//echo "<pre>";
			//print_r($reportData);
			//die();
		
			foreach ($reportData as $product) {
			
				if (is_array($product['charges'])) {
			
					foreach ($product['charges'] as $c) {
					
						if ($c['channelcode']) {
					
							$channelCode = $c['channelcode'];
							$recoupRate = $totals['channels'][$channelCode]['recoupRate'];
						
							$totals['subTotal'] += $c['chargeamount'];
							$totals['channels'][$channelCode]['subTotal'] += $c['chargeamount'];
							$totals['channels'][$channelCode]['total'] += ($c['chargeamount'] * ($recoupRate / 100));
							$totals['total'] += ($c['chargeamount'] * ($recoupRate / 100));
						
						}
					}
		
				}			
		
			}
			
		
		}
		
		
		return $totals;
	
	}
	
	function getInvoiceContents($id,$mode) {
	
		$CI =& get_instance();
		
		$CI->load->model('invoices_model');
		
		$CI->invoices_model->updateTotal($id);

		
		$invoice = $CI->invoices_model->fetchInvoice($id);
		$data['invoice'] = $invoice;
		$data['mode'] = $mode;
		
		$pid = 0;
		
		$sortedItems = array();
		
		foreach ($invoice->items as $i) {
		
			if ($i->opm_productid != $pid) { // create distinct prod entry
			
				$sortedItems[$i->opm_productid] = array (
					"opm_productid"=>$i->opm_productid,
					"default_imageid"=>$i->default_imageid,
					"propertycode"=>$i->nv_propid,
					"property"=>$i->property,
					"productname"=>$i->productname,
					"category"=>$i->category
				);
				
				$sortedItems[$i->opm_productid]['totalCharges'] = 0;
			
			}
			
			// regardless, add charge info to charges sub array
			
			$sortedItems[$i->opm_productid]["charges"][] = array (
				"id"=>$i->id,
				"chargetypeid"=>$i->chargetypeid,
				"chargeamount"=>$i->chargeamount,
				"chargedescription"=>$i->chargedescription,
				"chargetype"=>$i->chargetype,
				"opm_productid"=>$i->opm_productid,
				"hours"=>$i->hours,
				"hourlyrate"=>$i->hourlyrate,
				"channelcode"=>$i->channelcode,
				"notes"=>$i->notes
			
			);
			
			// get total charges for products
			
			$sortedItems[$i->opm_productid]['totalCharges'] += $i->chargeamount;
			
			$pid = $i->opm_productid;
		
		}
		
		$data['sortedItems'] = $sortedItems;
		
		if ($invoice->statusid != '1') {
    		$data['locked'] = true;
    		$data['formDisabled'] = true;
    	} else {
    		$data['locked'] = false;
    	}

		$data['chargeTypes'] = $CI->invoices_model->fetchChargeTypes();
		$data['statuses'] = $CI->invoices_model->fetchStatuses();
		
		$html = "";
		
		$data['altRow'] = 0; // for alt row colors

		foreach ($sortedItems as $i) {

			$i['productText'] = $i['property'] . " - " . $i['productname'] . " - " . $i['category'];
			
			$maxLength = 50;
			
			if (strlen($i['productText']) > $maxLength) {
			
				$i['productText'] = substr($i['productText'],0,$maxLength) . "...";
			
			}
			
			$data['i'] = $i;
			$html .= $CI->load->view('invoices/lineItem', $data, true);
			
			if ($data['altRow'] == 0)
				$data['altRow'] = 1;
			else
				$data['altRow'] = 0;
		
		}
		
		// print total
		
		$html .= $CI->load->view('invoices/ajax/invTotal', $data, true);
		
	
		return $html;
	
	}
	
	function getInvoiceNotes($id) {
	
		$CI =& get_instance();
	
		$CI->load->model('invoices_model');
	
		$data['notes'] = $CI->invoices_model->fetchInvoiceNotes($id);
		
		$html = $CI->load->view('invoices/notes', $data, true);
		
		return $html;
	
	}
	
	function checkInvoiceCodes($arrInvoices = array()) {
	
		// check property, design and customer codes for all existing invoices in system and generate
		// list of missing stuff to be emailed to bravado representatives...
		
		$arrErrors = array();
		
		$CI =& get_instance();
	
		$CI->load->model('invoices_model');
		
		$arrData = $CI->invoices_model->fetchMissingInvoiceCodes($arrInvoices);
		
		foreach ($arrData->result() as $row) {
		
			//print_r($row);
			
			if ($row->opm_productid && !$row->invoiceid) { // this is a missing design code
			
				$arrErrors[] = "<a href='".base_url()."products/view/".$row->opm_productid."' target='_blank'>" . $row->property . " - " . $row->productname . "</a> is missing a navision designcode!";
			
			} elseif ($row->propertyid) { // this is a missing property code
			
				$arrErrors[] = "<a href='".base_url()."properties/view/".$row->propertyid."' target='_blank'>" . $row->property . "</a> is missing a navision property code!";

			
			} elseif ($row->userid) { // this is a missing customer id
			
				$arrErrors[] = "<a href='".base_url()."users/view/".$row->userid."' target='_blank'>" . $row->username . "</a> is missing a navision customer id!";
			
			} elseif ($row->invoicedetailid) { // this is a missing channelcode
			
				$arrErrors[] = "<a href='".base_url()."invoices/edit/".$row->invoiceid."' target='_blank'>Invoice # " . $row->invoiceid . "</a> is missing channel codes!";
			
			} elseif ($row->hasnobodystyle) { // this is a missing bodystyle
			
				$arrErrors[] = "<a href='".base_url()."products/view/".$row->opm_productid."' target='_blank'>Product # " . $row->opm_productid . "</a> has no body style!";
			
			}
			
			//$arrErrors[] = $row;
		
		}
		
		
		return $arrErrors;
		
	
	}
	
	
	function fetchProductsForExport() {
		
		$CI =& get_instance();
	
		$CI->load->model('products_model');
	
		$arrProducts = array();
	
		$products = $CI->products_model->fetchProductsForExport();
		
		foreach ($products->result() as $prodRow) {
		
			$prod = $CI->products_model->fetchProductInfo($prodRow->opm_productid);
		
			foreach ($prod->skus as $p) {
			
				$arrProducts[] = array(
								"opm_productid"=>$prod->opm_productid,
								"sku"=>$p->sku,
								"category"=>$prod->category,
								"bodystyle"=>$prod->bodystyle,
								"shortname"=>$prod->shortname,
								"productname"=>$prod->productname,
								"lastmodified"=>$prod->lastmodified,
								"size"=>$p->sizecode,
								"color"=>$p->color
								);
			
			}
			
			
		}
		
		return $arrProducts;
	
	}
	
	/*function fetchNeededTOS($userid,$returnTos = true) {
		
		$CI =& get_instance();
			
		$CI->load->model('tos_model');
	
		$CI->tos_model->fetchNeededTos($userid);
	
	}*/
	
}

?>