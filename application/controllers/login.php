<?php
class Login extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('users_model');
    }

	function index() 
	{
		$this->load->helper('cookie');
		
		// let's get cookie info if we have it 
		
		$data = array();
		
		if(get_cookie('username')) {
		
			$data['cookieInfo']->username = get_cookie('username');
			$data['cookieInfo']->password = get_cookie('password');
		
		} 
		
		// if we are already logged in, redirect to start page
		
		if ($this->session->userdata('logged_in'))
			redirect($this->config->item('startPage'), 'location');

		$template['is_login_page'] = true;
		$template['page_title'] = "Login";
		$template['content'] = $this->load->view('login',$data,true);
		$this->load->view('global/alerttemplate', $template);
	}
	
	function forgotPassword() 
	{
		$data = array();
		
		if ($this->session->userdata('logged_in'))
			redirect($this->config->item('startPage'), 'location');

		$template['is_login_page'] = true;
		$template['page_title'] = "Login";
		$template['content'] = $this->load->view('forgotPassword',$data,true);
		$this->load->view('global/alerttemplate', $template);
		
	}
	
	function forgotPWSubmit() 
	{
		$email = $this->input->post('email');
		
		if (!$email) {
			$this->opm->displayError("Email Address is Required.");
			return true;
		}
		
		// try to find address in DB 
		
		if ($userinfo = $this->users_model->retrievePassword($email)) {
		
			//print_r($userinfo);
			$password = createRandOpmPassword();//$this->opm->text_decrypt($userinfo->password);
			
			$postdata['newPassword'] = $password;
			$this->users_model->changePassword($postdata,$userinfo->userid,1);
			
			$recipients[] = $userinfo->login;
			
			$subject = "Bravado OPM password reset";
			
			$body = "Your temporary password is: " . $password;
			$body .= "\n\n Login here: \n\n";
			$body .= base_url();
			$body .="\n-Bravado OPM";
			
			if ($this->opm->sendEmail($subject,$body,$recipients)) {
			
				$data = array("emailSent"=>true);
			
				$template['content'] = $this->load->view('forgotPassword',$data,true);
				$this->load->view('global/alerttemplate', $template);
			
			} else {
			
				$this->opm->displayError("Your OPM Account was found, but a technical issue was encountered when sending you the email. Please contact your Bravado OPM rep!");
			
			}
		
		} else {
		
			$this->opm->displayError("Your OPM Account was not found!");
		
		}
		
	}
	
	function doLogin() {
		
		$this->load->helper('cookie');
	
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		if (!$username || !$password) {
			$this->opm->displayError("invalid_login");
			return true;
		}
			
		
		if ($userid = $this->users_model->checkLoginInfo($username,$password)) {
			
			// set session vars and redirect
			
			$newdata = array('userid'  => $userid,
							'logged_in' => TRUE);
							
			$this->session->set_userdata($newdata);
			
			// set cookies for "remember me"
			
			if ($this->input->post('rememberMe')) {
			
				set_cookie("username", $username);
				set_cookie("password", $password);

			
			}
			
			if ($this->session->userdata('loginRedirect')) {
				
				redirect($this->session->userdata('loginRedirect'), 'location');
			
			} else {
				
				redirect($this->config->item('startPage'), 'location');
			
			}
			
			
			
		} else {
		
			$this->opm->displayError("invalid_login");
			return true;
		}
	
	}
	
	function doLogout() {
	
		$this->session->sess_destroy();
		//$this->db_session->sess_destroy();
		redirect('/login');
		return true;
		
	}
	

}
?>