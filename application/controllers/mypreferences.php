<?php
class Mypreferences extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->opm->activeNav = '';
    	$this->load->model('preferences_model');
    	$this->load->model('properties_model');
    }

	function index() // index will be the property list.
	{
	
		$data = array();

		$template['page_title'] = "My Preferences";
        $template['bigheader'] = "My Preferences";
        $template['nav2'] = "My Preferences";
        
        $data['preferences'] = $this->preferences_model->getPrefs($this->userinfo->userid);
        $data['properties'] = $this->properties_model->fetchProperties();
        $data['userProperties'] = $this->properties_model->fetchUserProperties($this->userinfo->userid);
        
        $arrJS['scripts'] = array('jquery-1.6.2.min','jquery.validate-1.8.1.min','opm_scripts'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
		
		$template['content'] = $this->load->view('myprefs',$data,true);
		$this->load->view('global/maintemplate', $template);
		
	}
	
	function save() // now we gotta save the prefs
	{
	
		if (checkPerms('can_edit_preferences',true)) { // check permissions	
		
			$errors = "";
		
			if (!$postdata['userid'] = $this->input->post('userid'))
				$errors .= "There was an error saving preferences.";
				
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
			$postdata['prefs'] = $this->input->post('prefs');
			$postdata['checkbox'] = $this->input->post('chkbox');
			$postdata['userPropertyIDs'] = $this->input->post('userPropertyIDs');
			
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			die();*/
	
			
			if ($this->preferences_model->savePrefs($postdata)) {
			
				
				$this->opm->displayAlert("Preferences Have Been Saved!","/mypreferences");
				return true;	
			
			
			} else {
			
				$this->opm->displayError("Preferences Could Not Be Saved");
				return true;
			}
		
		}
		
	}
	
	function changePass() {
	
		//if (checkPerms('can_change_password',true)) { // check permissions	

			$errors = "";
		
			if (!$postdata['currentPassword'] = $this->input->post('currentPassword'))
				$errors .= "No current password was entered. <br />";
				
			if (!$postdata['newPassword'] = $this->input->post('newPassword'))
				$errors .= "No new password was entered. <br />";
				
			if (!$postdata['newPasswordConf'] = $this->input->post('newPasswordConf'))
				$errors .= "No confirmation password was entered. <br />";
				
			if ($this->input->post('newPassword') != $this->input->post('newPasswordConf'))
				$errors .= "New Password and confirmation password do not match. <br />";
				
			if ($errors) {
		
				$this->opm->displayError($errors);
				return false;
			
			}
			
			if ($this->userinfo->password_changed) {
				
				$hashed = $this->userinfo->password;
				$password = $this->input->post('newPassword');
				
				if ($this->phpass->check($password, $hashed)) {
				
					$this->opm->displayError("New password is same as old.");
					return false;
				
				}
				
				
			} else {
				
				
				// check if old password is correct
			
				if ($postdata['currentPassword'] != $this->userinfo->password) {
				
					$this->opm->displayError("Current password does not match password on record.");
					return false;
				
				}
				
				if ($postdata['currentPassword'] == $postdata['newPassword']) {
				
					$this->opm->displayError("New Password is the same as old. You must change your password.");
					return false;
				
				}
				
				
			}
			
			// make sure new password meets reqs
			
			$pwd = $postdata['newPassword'];
			
			$pwErrString = checkPWReqs($pwd);
			
			if ($pwErrString != 1) {
								
				$this->opm->displayError("New Password does not meet the following requirements:<br /><br />" . $pwErrString);
				return false;
				
			}
			
			
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			die();*/
	
			
			if ($this->users_model->changePassword($postdata)) {
			
				
				$this->opm->displayAlert("Password Successfully Changed!","/mypreferences");
				return true;	
			
			
			} else {
			
			
				$this->opm->displayError("Password Could Not Be Changed");
				return true;
				
			}
		
	//	}
		
	}

}

?>