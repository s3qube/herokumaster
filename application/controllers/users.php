<?php
class Users extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('users_model');
    	$this->load->model('usergroups_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'administration';
    	
    	global $searchArgList;
    	$searchArgList = array("usergroupid","appPropertyID","username","showDeactivated","login","permissionid","officeid","deptid","offset");
    	
    	//$this->output->enable_profiler(TRUE);
    }

	
	function search($usergroupid = 0, $appPropertyID = 0, $username = null, $showDeactivated = '0', $login = null, $permissionid = null, $officeid = null, $deptid = null, $offset = 0) {
	
		if (checkPerms('can_view_users',true)) {

			$this->load->model('properties_model');
	
			global $searchArgList;
			
			$username = urldecode($username);
			
			$data = array();
	   
			$template['page_title'] = "Search Users";
			$template['bigheader'] = "Search Users";
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
			
			if ($showDeactivated == '0')
				$includeDeactivated = false;
			else
				$includeDeactivated = true;
				
			
			$data['totalUsers'] = $this->users_model->fetchUsers(true,null,null,$usergroupid,$appPropertyID,$username,true,$login,$permissionid,null,null,$officeid,$deptid,true);
			
			
			foreach ($searchArgList as $k=>$d) 
				$data['args'][$d] = ${$d};
			
			
			$this->load->library('pagination');
			$config['base_url'] = base_url().'/users/search/'.$usergroupid."/".$appPropertyID."/".$username."/".$showDeactivated."/".$login."/".$permissionid."/".$officeid."/".$deptid."/";;
			$config['total_rows'] = $data['totalUsers'];
			$config['per_page'] = '20';
			$config['uri_segment'] = 9;
		
	
			$this->pagination->initialize($config);
			
			$data['users'] = $this->users_model->fetchUsers(false,$offset,$config['per_page'],$usergroupid,$appPropertyID,$username,true,$login,$permissionid,null,null,$officeid,$deptid,true); 
			$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
			$data['properties'] = $this->properties_model->fetchApprovalProperties();
			$data['permissions'] = $this->users_model->fetchPermissionsList();
			
			$data['prodStart'] = $offset + 1;
			$data['prodEnd'] = $data['prodStart'] + ($data['users']->num_rows() - 1);
			
			$template['rightNav'] = $this->load->view('users/rightNav',$data,true);
			$template['searchArea'] = $this->load->view('users/searchArea',$data,true);
			$template['contentNav'] = $this->load->view('users/searchNav',$data,true);
			$template['content'] = $this->load->view('users/search',$data,true);
			
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','tipsx3'); // 
			$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
				
			$this->load->view('global/maintemplate', $template);
			
		}    
    }
    
    function submit() { // construct a search url from a form submission
    
    	global $searchArgList;

		foreach ($searchArgList as $key=>$data) {
			
			if ($this->input->post($data))
				$segments[$data] = $this->input->post($data);
			else
				$segments[$data] = 0;	
			
		}
		
		$url = "/users/search/";
		
		foreach ($segments as $data)
			$url .= $data . "/";
			
		
		redirect($url);
    
    }
    
    function view($id,$tabname = 'basicinfo')
   	{
   		if (checkPerms('can_view_users',true)) {

			if ($tabname)
				$data['tabname'] = $tabname;
			else
				$data['tabname'] = 'basicinfo';	
		
		
			$data['user'] = $this->users_model->fetchUserInfo($id);
			
			if (!checkPerms('admin_all_users') && $data['user']->createdby != $this->userinfo->userid) {
   	
   				$this->opm->displayError("You do not have permission to view this user.");
				return false;
				
			}
			
			$template['content'] = '';
			$template['headInclude'] = $this->load->view('users/loadContentJS',$data,true);;
		   
			$template['page_title'] = "View User";
			$template['bigheader'] = $data['user']->username . "&nbsp;&nbsp;(" . $data['user']->usergroup . ")";
			$template['nav2'] = $this->load->view('users/nav2',$data,true);
			$template['contentNav'] = $this->load->view('users/contentNav',$data,true);
			
			$arrJS['scripts'] = array('jquery-1.3.2.min','opm_scripts','shadowbox3','datepicker'); // 
			$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
		
			$this->load->view('global/maintemplate', $template);
		
		}
    
    }
    
    function add()
   	{
   		if (checkPerms('can_add_users',true)) {
   		
   			$data = array();
   			
   			$data['user'] = new stdClass();
   			
   			$data['user']->usergroupid = 0;
   			$data['user']->usergroupid2 = 0;
   			$data['user']->officeid = 0;
   			$data['addMode'] = true;
   			
   			$this->load->model('usergroups_model');
		   
			$template['page_title'] = "Add User";
			$template['bigheader'] = "Add User";
			$template['nav2'] = "Add User";
			
			$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
			
			$template['content'] = $this->load->view('users/basicinfo',$data,true);
			
			$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','datepicker'); // 
			$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
		
			$this->load->view('global/maintemplate', $template);
		
		}
    
    }
    
    function sendEmail()
   	{
   		if (checkPerms('can_email_users',true)) {
   		
   			$data = array();
   			
   			$data['user']->usergroupid = 0;
   			$data['user']->usergroupid2 = 0;
   			$data['addMode'] = true;
   			
   			$this->load->model('usergroups_model');
		   
			$template['page_title'] = "Send Email";
			$template['bigheader'] = "Send Email To Users";
			$template['nav2'] = $this->load->view('users/nav2',$data,true);

			$data['usergroups'] = $this->usergroups_model->fetchUsergroups();
			
			$template['content'] = $this->load->view('users/sendEmail',$data,true);
			
			$arrJS['scripts'] = array('jquery-1.3.2.min','opm_scripts','shadowbox3','dropdown'); // 
       		$template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);		
			
			$this->load->view('global/maintemplate', $template);
		
		}
    
    }
    
    function sendEmailHandler() {
    
    	if (checkPerms('can_email_users',true)) {
    	
    		$ugs = $this->input->post('usergroups');	
    		
       		// validate form
    		
    		if (!$ugs) {
    			
    			$this->opm->displayError("No usergroups selected. Cannot send email.");
				return false;
				
    		}
    		
    		if (!$subject = $this->input->post('subject')) {
    			
    			$this->opm->displayError("Email must have a Subject.");
				return false;
				
    		}
    		
    		if (!$body = $this->input->post('body')) {
    			
    			$this->opm->displayError("Email must have a body.");
				return false;
				
    		}
    		
    		
    	
			// lets assemble all the usergroups (with children) that should be emailed.
			
					
			
			$usergroups = array(); // the final list with children
			
			foreach ($ugs as $ugid=>$onoff) {
				
				$ugids = $this->usergroups_model->getChildren($ugid);
				$ugids[] = $ugid;
				$usergroups = array_merge($usergroups,$ugids);				
			
			}
			
			$strUGs = implode(",",$usergroups);    	
    	
    		$users = $this->users_model->fetchUsersFromUsergroups($strUGs);
    		
    		foreach ($users->result() as $u) {
	    	
	    		$recipients[] = $u->login;
    		
    		}
    		
    		if (!$recipients) {
    			
    			$this->opm->displayError("Email has no recipients.");
				return false;
    		
    		} else {
    			
    			// if we have more than 50 recipients, break the email up into 50 each.
    			
    			if (sizeof($recipients) > 50) {
    				
    				$counter = 0; // where we are at in the main array
    				
    				// we want to break up the array into chunks of 50
    				    			
    				while ($counter < sizeof($recipients)) {
    				
    					$recipChunks[] = array_slice($recipients,$counter,50);
    					$counter += 50;
    				
    				}
    				
    				$errCount = 0;
    				$counter2 = 0; // counts # of recipients sent to as a check.
    				
    				foreach ($recipChunks as $recips) {
    	
    					
    					if (!$this->opm->sendEmail($subject,$body,$recips,true))
    						$errCount++;
    						
    					$counter2 += sizeof($recips);
    				
    				}
    				
    				
    					
    				if ($errCount == 0) {
    				
    					$numEmailsSent = sizeof($recipChunks);
    				
    					$this->opm->displayAlert($numEmailsSent . " Emails Sent Successfully!","/users/sendEmail/");
						return true;
    				
    				} else {
    				
    					$numEmailsSent = sizeof($recipChunks) - $errCount;
    				
    					$this->opm->displayError($numEmailsSent . " sent successfully, ".$errCount." emails encountered errors and were not sent. Please contact OPM tech support.");
						return false;
    				
    				}
    			
    			
    			} else {
    			
    				
    				if ($this->opm->sendEmail($subject,$body,$recipients,true)) {
    			
	    				$this->opm->displayAlert("Email Sent Successfully!","/users/sendEmail/");
						return true;
	    			
	    			} else {
	    			
	    				$this->opm->displayError("There was an issue sending your email. Please contact OPM tech support.");
						return false;
	    			
	    			}
    			
    			
    			}
    			
    			
    			
    		
    		}
	    
	    	die();
    	
    	}
    	
    }
    
    function loadContent($id,$tabName)
    
    {
    	
    	global $formDisabled;
    	
    	if ($tabName == 'basicinfo') {
    		$this->load->model('users_model');
    		$this->load->model('usergroups_model');
    		$this->load->model('companies_model');
    		$this->load->model('offices_model');
    		
    		if (!checkPerms('can_edit_users'))
    			$formDisabled = true;
    		
    		$data['usergroups'] = usergroupArray2Select($this->usergroups_model->fetchUsergroups());
    		$data['offices'] = officeArray2Select($this->offices_model->fetchOffices());
    		$data['companies'] = $this->companies_model->fetchCompanies();
    		//print_r($data['companies']);
    		//die();
    		//$data['product'] = $this->products_model->fetchProductInfo($id);
    	
    	}
    	
    	if ($tabName == 'approvalProperties') {
    		$this->load->model('users_model');
    		$this->load->model('user_properties_model');
    		$this->load->model('properties_model');
    		
    		$data['properties'] = $this->properties_model->fetchProperties(false,true);
    		$data['userProperties'] = $this->user_properties_model->fetchApprovalProperties($id);
    		//$data['product'] = $this->products_model->fetchProductInfo($id);
    	
    	}
    	
    	if ($tabName == 'emailprefs') {
    	
    		$this->load->model('users_model');
    		$this->load->model('properties_model');
    		
    		$data['properties'] = $this->properties_model->fetchProperties();
    	
    	}
    	
    	if ($tabName == 'invoicing') {
    	
    		$this->load->model('users_model');
    		$this->load->model('properties_model');
    		$this->load->model('currencies_model');
    		
    		$data['referer'] = "users";
    		
    		$data['properties'] = $this->properties_model->fetchProperties();
    		$data['currencies'] = $this->currencies_model->fetchCurrencies();
    	
    	}
    	
    	if ($tabName == 'permissions') {
    	
    		if (checkPerms('can_edit_user_permissions',true)) {
    	
    			$this->load->model('users_model');
    		
    			$data['user'] = $id;
    			$data['permissions'] = $this->users_model->fetchPermissions($id);
    		
    		}
    	}
    		
    	$data['user'] = $this->users_model->fetchUserInfo($id);
		echo $this->load->view('users/'.$tabName,$data,true);
		
    
    }
    
    function save() {
    	
    	if (checkPerms('can_edit_users',true)) { // check permissions
    	
    		// let's make sure there isn't already a user with this email!
    		
    		
			
			$errors = "";
			$mode = "edit";
			
			if ($this->input->post('userid'))
				$postdata['userid'] = $this->input->post('userid');
			else
				$mode = "add";
				
			if (!$postdata['username'] = $this->input->post('username'))
				$errors .= "User Must have a Username!<br />";
			
			if (!$postdata['usergroupid'] = $this->input->post('usergroupid'))
				$errors .= "User Must have a User Group!<br />";
				
			//if (!$postdata['companyid'] = $this->input->post('companyid'))
			//	$errors .= "User Must have a Company!<br />";
				
			if ($mode != 'add' && $this->input->post('submitPassword')) {
				
				if (!$postdata['password'] = $this->input->post('password'))
					$errors .= "User Must have a Password!<br />";
					
				if ($this->input->post('password') != $this->input->post('password2'))
					$errors .= "Passwords do not match!<br />";
			}
				
			if (!$postdata['login'] = $this->input->post('login'))
				$errors .= "User Must have an Email Address!<br />";
				
			if (checkEmail($postdata['login']))
				$errors .= "Email Address Appears to be invalid<br />";
				
			if ($this->users_model->checkIfEmailExists($postdata['login'],$this->input->post('userid')))
				$errors .= "Email Address already exists in OPM<br />";
			
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
			$postdata['address'] = $this->input->post('address');
			$postdata['officeid'] = $this->input->post('officeid');
			$postdata['isactive'] = $this->input->post('isactive');
			$postdata['nv_customerid'] = $this->input->post('nv_customerid');
			$postdata['usergroupid2'] = $this->input->post('usergroupid2');
			$postdata['submitPassword'] =$this->input->post('submitPassword'); // this tells the model to save the password.
			
			
			if (is_uploaded_file($_FILES['avatar']['tmp_name'])) { // we have a file upload!
			
				// upload avatar
				
				$config['upload_path'] = $this->config->item('fileUploadPath') . "avatars/";// . $userid . "/";
				//die($config['upload_path']);
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size'] = '2000';
				$config['max_width'] = '48';
				$config['max_height'] = '48';
				$config['encrypt_name'] = true;
		
				$this->load->library('upload', $config);
				
				if ($this->upload->do_upload('avatar')) {
				
					$arrUpload = $this->upload->data();
					$postdata['avatar_path'] = $arrUpload['file_name'];
				
				} else {
				
					//die($this->upload->display_errors());
				}
			
			
			}
			
			// if we are in add mode, generate password!
			
			if ($mode == 'add') {
			
				$postdata['password'] = createRandOpmPassword();
			
			}
	
			
			if ($userid = $this->users_model->saveUserInfo($postdata)) {
			
				if ($mode == 'add') { // new user added, send email + add default prefs
				
					// default prefs!
					
					$this->load->model('preferences_model');
					
					// we need to make an array of licensee group ids, fun!
					
					$licUGids = $this->usergroups_model->getChildren($this->config->item('licenseeGroupID'));
					$licUGids[] = $this->config->item('licenseeGroupID');
					
					
					if ($postdata['usergroupid'] == $this->config->item('designersGroupID'))
						$defaultPrefs = $this->config->item('defaultDesignerPrefs');
					else if ($postdata['usergroupid'] == $this->config->item('propertyContactsGroupID'))
						$defaultPrefs = $this->config->item('defaultPropertyContactPrefs');
					else if (in_array($postdata['usergroupid'], $licUGids) || in_array($postdata['usergroupid'], $licUGids))
						$defaultPrefs = $this->config->item('defaultLicenseePrefs');
					else
						$defaultPrefs = $this->config->item('defaultPrefs');
					
					$prefData['prefs'] = array(); // init these vars
					$prefData['checkbox'] = array();
					
					foreach ($defaultPrefs as $prefid) {
					
						$prefData['prefs'][$prefid] = 'on'; // it's done this way due to checkbox behavior on pref selection on pref page
						$prefData['checkbox'][$prefid] = 'on'; // it's done this way due to checkbox behavior on pref selection on pref page
					
					}
					
					$prefData['userid'] = $userid;
					$prefData['userPropertyIDs'] = ""; // kludge
					
					$this->preferences_model->savePrefs($prefData);
				
					// send email
				
					$data['user'] = $this->users_model->fetchUserInfo($userid);
					$data['password'] = $postdata['password'];
				
					$subject = "Welcome to Bravado OPM";
					$body = $this->load->view('emails/newuser',$data,true);
					
					$recipients[] = $data['user']->login;
					
					if ($this->opm->sendEmail($subject,$body,$recipients)) {
					
						$this->opm->displayAlert("User Saved, email sent to " . $data['user']->login,"/users/view/" . $userid);
						return true;
					
					} else {
					
						$this->opm->displayError("User Has Been saved, but email could not be sent!","/users/search/");
						return true;
					
					}
				
				} else {
					
					$this->opm->displayAlert("User Has Been Saved!","/users/view/" . $userid);
					return true;
				
				}	
			
			
			} else {
			
				$this->opm->displayError("error_saving");
				return true;
			}
		
		}		
			
	}
	
	
	function saveInvoicing() {
    	
    	
    	if (checkPerms('can_edit_users') || ($this->userinfo->userid == $this->input->post('userid'))) { // check permissions
    		
			$mode = "edit";

			$postdata['userid'] = $this->input->post('userid');
			$postdata['taxid'] = $this->input->post('taxid');
			$postdata['vatnumber'] = $this->input->post('vatnumber');
			$postdata['submissionfee'] = $this->input->post('submissionfee');
			$postdata['ishourly'] = $this->input->post('ishourly');
			$postdata['caninvoice'] = $this->input->post('caninvoice');
			
			$postdata['staddress'] = $this->input->post('staddress');
			$postdata['staddress2'] = $this->input->post('staddress2');
			$postdata['city'] = $this->input->post('city');
			$postdata['state'] = $this->input->post('state');
			$postdata['zip'] = $this->input->post('zip');
			
			$postdata['referer'] = $this->input->post('referer');
			
			$postdata['currencyid'] = $this->input->post('currencyid');
			$postdata['hourlyrate'] = $this->input->post('hourlyrate');
			
			$postdata['notes'] = $this->input->post('notes');
			$postdata['notestoinvoices'] = $this->input->post('notestoinvoices');
			
			
			if (is_uploaded_file($_FILES['invoiceImage']['tmp_name'])) { // we have a file upload!
			
				// upload invoice_image
				
				$config['upload_path'] = $this->config->item('fileUploadPath') . "invoiceImages/";// . $userid . "/";
				//die($config['upload_path']);
				$config['allowed_types'] = 'gif|jpg|png';
				//$config['max_size'] = '2000';
				//$config['max_width'] = '48';
				//$config['max_height'] = '48';
				$config['encrypt_name'] = true;
		
				$this->load->library('upload', $config);
				
				if ($this->upload->do_upload('invoiceImage')) {
				
					$arrUpload = $this->upload->data();
					$postdata['invoiceimage_path'] = $arrUpload['file_name'];
				
				} else {
				
					//die($this->upload->display_errors());
				}
			
			
			}
	
			
			if ($userid = $this->users_model->saveInvoiceInfo($postdata)) {
			
				if ($postdata['referer'] == 'editMyInfo') {
					
					$this->opm->displayAlert("User Invoicing Info Has Been Saved!","/invoices/editInfo/" . $userid . "/invoicing");
					return true;
				
				} else {
				
					$this->opm->displayAlert("User Invoicing Info Has Been Saved!","/users/view/" . $userid . "/invoicing");
					return true;
					
				}
				
			
			} else {
			
				$this->opm->displayError("error_saving");
				return true;
			}
		
		} else {
		
			checkPerms('can_edit_users',true);
		
		}	
			
	}
	
	function savePermissions() {
    	
    	if (checkPerms('can_edit_user_permissions',true)) { // check permissions
    	

			$postdata['userid'] = $this->input->post('userid');
			$postdata['perms'] = $this->input->post('perms');
			$postdata['chkbox'] = $this->input->post('chkbox');
			
			
			
			if ($userid = $this->users_model->savePermissions($postdata)) {
			

				$this->opm->displayAlert("User Permission Info Has Been Saved!","/users/view/" . $userid . "/permissions");
				return true;
				
			
			} else {
			
				$this->opm->displayError("Error Saving Permissions!");
				return true;
			}
		
		}		
			
	}
	
	
	function addApprovalProperty() {
	

		$this->load->model('user_properties_model');
	
		$postdata['propertyid'] = $this->input->post('propertyid');
		$postdata['userid'] = $this->input->post('userid');
		
		
		if ($userid = $this->user_properties_model->addApprovalProperty($postdata)) {
		
			
			$this->opm->displayAlert("The property has been added to the user.","/users/view/" . $postdata['userid'] ."/approvalProperties");
			return true;	
		
		
		} else {
		
			$this->opm->displayError("Either the property is already associated with that user, or the property does not exist.","/users/view/" . $postdata['userid'] ."/approvalProperties");
			return true;
			
		}
		
			
	}
	
	function saveApprovalProperties() {
	

		$this->load->model('user_properties_model');

		
		$postdata['userid'] = $this->input->post('userid');
		$postdata['arrLineIDs'] = $this->input->post('arrLineIDs');
		$postdata['approvalrequired'] = $this->input->post('approvalrequired');
		$postdata['begindate'] = $this->input->post('begindate');
		$postdata['enddate'] = $this->input->post('enddate');
		
		
		if ($userid = $this->user_properties_model->saveApprovalProperties($postdata)) {
		
			
			$this->opm->displayAlert("All approval properties have been updated.","/users/view/" . $postdata['userid'] ."/approvalProperties");
			return true;	
		
		
		} else {
		
			$this->opm->displayError("Either the property is already associated with that user, or the property does not exist.");
			return true;
			
		}
		
			
	}
	
	function changeUser($userid) {
	
	
		if (in_array($this->userinfo->userid, $this->config->item('superAdmins'))) { 
		
			$this->session->set_userdata('impersonateUser', $userid);
		
			redirect("/");			
		
		} else {
			
			$this->opm->displayError("You don't have permission to do that.");
			return true;
			
		}
		
			
	}
	

    
    
    
}


?>