<?php
class Email extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('products_model');
    	$this->load->model('users_model');
    	$this->opm->checkLogin();

    }
    
    function userPicker($opm_productid,$mode) {
    
    	// valid modes are screenprinters,separators,designers
    	
    	$data['mode'] = $mode;
    	$data['p'] = $this->products_model->fetchProductInfo($opm_productid);
    	
    	// we need to get usergroups under screenprinters or seps
    	
    	if ($mode == 'screenPrinters')
    		$data['usergroups'] = $this->usergroups_model->fetchUsergroupsByParent($this->config->item("screenprintersGroupID"),$opm_productid);
    	else if ($mode == 'separators')
			$data['usergroups'] = $this->usergroups_model->fetchUsergroupsByParent($this->config->item("separatorsGroupID"),$opm_productid);
		
    	//$data['separators'] = $this->users_model->getSepsSCPsForProduct('separators', $opm_productid);
    	
    	echo $this->load->view('userPicker',$data,true);
    
    }

	function sendEmailToContacts()
	{
	
		$this->load->model('forum_model');
	
		// get product info
		
		$opm_productid = $this->input->post('opm_productid');
		$mode = $this->input->post('mode');
		$data['comment'] = $this->input->post('notificationComment');
		$data['commentUsername'] = $this->userinfo->username;
		
		$data['product'] = $this->products_model->fetchProductInfo($opm_productid);
		
		$subject = $this->load->view('emails/notify_'.$mode.'_subject',$data,true);
		$body = $this->load->view('emails/notify_'.$mode.'_body',$data,true);	
	
		// we need to get contacts and check prefs.
		
		foreach ($data['product']->approvalInfo as $ai) {
			
			// make sure user has prefs set for recieve emails.
			
			$userinfo = $this->users_model->fetchUserInfo($ai->userid);
			
			if (isset($userinfo->prefs['receive_new_product_emails']) && $userinfo->isactive == 1) {
				$recipients[] = $ai->login;
				
			}
				
		}
	
		//$recipients[] = 'tim@studio211.us';
		//$recipients[] = 'ute.linhart@bravado.com';
		
		// create text for history
		
		if ($mode == 'newProduct')
			$historyText = 'New Product';
		else
			$historyText = 'Product Updated';
			
		if (is_array($recipients)) {
		
			if ($this->opm->sendEmail($subject,$body,$recipients)) {
				
				// add recipients' names to email
				
				$email_string = "";
				
				foreach($recipients as $recip_email)
					$email_string .= $recip_email . ", ";
				
				if ($data['comment']) {
				
					$data['commentBody'] = $data['comment'];
					$data['commentBody'] .= "\n\n recipients were: \n" . $email_string;
				
					$this->opm->addHistoryItem($opm_productid,$historyText . " email sent (with comment) to " . $email_string . ".");
					$this->forum_model->addForumEntry($opm_productid,$this->userinfo->userid,$subject . " email sent with the following comment:",$data['commentBody']);
					
					
				} else {
				
					$this->opm->addHistoryItem($opm_productid,$historyText . " email sent to " . $email_string . ".");
				
				}
			
				$this->opm->displayAlert("Email Has Been sent!","/products/view/" . $opm_productid);
				return true;
		
			} else {
		
				$this->opm->displayError("Email Error:Email not sent.");
			
			}
			
		} else {
		
			$this->opm->displayError("No Recipients were found for this email.");
		
		}
	
	
	}
	
	function sendProductionEmail($opm_productid) { // this function sends notification emails to separators and screenprinters!
	
		$this->load->model('forum_model');
	
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		die();*/
		//$arrValidModes = array("sepsReady","sepsUpdated","artworkReady","artworkUpdated");
		
		//if (!in_array($mode, $arrValidModes)
		//	$this->opm->displayError("Invalid Mode Sent To sendProductionEmail");
		
		// get product info
		
		$data['product'] = $this->products_model->fetchProductInfo($opm_productid);
		$data['comment'] = $this->input->post('notificationComment');
		$data['commentUsername'] = $this->userinfo->username;
		
		$action = $this->input->post('action');
		$recipientUGs = $this->input->post('recipientUGs');
		
		if ($action != 'newDesignProject') {
		
			// get all users in the usergroups, make sure they have product view perms, then prep for email sendage!
			
			if ($recipientUGs) {
			
				$arrUGs = explode(",",$recipientUGs);
				$recipients = array();
				$recipientNames = array();
			
			
			
				foreach ($arrUGs as $key=>$UGID) {
				
					$users = $this->users_model->fetchUsers($returnTotal = false, $offset = 0, $perPage = null, $usergroupid = $UGID);
					
					foreach ($users->result() as $u) {
						
						if (!in_array($u->login, $recipients)) {
							
							$recipients[] = $u->login;
							$recipientNames[] = $u->username . "(" . $u->usergroup . ")";
						
						}
						
					}
				
				
				}
			
			} else {
			
				$this->opm->displayError("No Recipients were found for this email.");
				return false;
			}
			
		} else {
		
			$desEmails = $this->users_model->fetchDesigners(false, $opm_productid, false);
				
			foreach ($desEmails->result() as $recipient) {
				
				$recipients[] = $recipient->login;
				$recipientNames[] = $recipient->username;
			
			}
		
		}
					
		
			

			
			// set body, subject - then send the mail and notify!
			
			$mode = $action;
			
			$subject = $this->load->view('emails/notify_'.$mode.'_subject',$data,true);
			$body = $this->load->view('emails/notify_'.$mode.'_body',$data,true);
				
			if (isset($recipients)) {
			
				if ($this->opm->sendEmail($subject,$body,$recipients)) {
			
					if ($data['comment']) {
						
						// we need to add an entry to the forum for this product with the comment text.
						
						$data['commentBody'] = $data['comment'];
						
						$data['commentBody'] .= "\n\n Recipients were: \n";
						
						foreach ($recipientNames as $name)
							$data['commentBody'] .= $name . "\n";
					
						$this->opm->addHistoryItem($opm_productid,$subject . " email sent (with comment) by " . $this->userinfo->username);
						$this->forum_model->addForumEntry($opm_productid,$this->userinfo->userid,$subject . " email sent with the following comment:",$data['commentBody']);
					
					} else {
					
						$this->opm->addHistoryItem($opm_productid,$subject . " email sent by " . $this->userinfo->username);
					
					}
									
					$this->opm->displayAlert("Email Has Been sent!","/products/view/" . $opm_productid);
					return true;
			
			
				} else {
			
					$this->opm->displayError("Email Could Not Be Sent.");
					return false;
				
				}
			
			} else {
			
				$this->opm->displayError("No Recipients were found for this email.");
				return false;
			
			}
			
		
		
	
	}


}