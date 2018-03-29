<?php
class Dashboard extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('products_model');
    	$this->opm->activeNav = '';
    	
    	
    	if ($this->config->item('debugMode') == true)
    		$this->output->enable_profiler(TRUE);
    		
    		
    }

	function index() // index will be the property list.
	{
	
		// dashboard stats!
		
			// # of products
			// # of users
			// # of properties
			// # of masterfiles (with sizes)
			// # of seps (with sizes)
	
		$data = array();
		
		
		// GET HUMAN READABLE AVAILABLE + TOTAL DISK SPACE!

	    $bytes = disk_total_space($this->config->item('fileUploadPath')); 
	    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	    $base = 1024;
	    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
	    
		$data['stats']['availSpace'] = sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';

	    
	    $bytes = disk_free_space($this->config->item('fileUploadPath')); 
	    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	    $base = 1024;
	    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
	    
	    $data['stats']['freeSpace'] = sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
	
		
		// GET TOTAL PRODUCTS
		
		$data['stats']['totalProducts'] = $this->products_model->getTotalSystemProducts(true);
		
		// GET TOTAL MASTERFILES
		
		//$this->files_model->
		
		//$data['stats']['totalMasterfiles'] = "";
	   
			
		$arrJS['scripts'] = array('mootools-release-1.11','opm_scripts','shadowbox-mootools','shadowbox','dropdown'); // 
        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);

		$template['page_title'] = "Welcome To OPM";
        $template['bigheader'] = "Welcome to Bravado OPM!";
        $template['nav2'] = "&nbsp;";
		
		$template['content'] = $this->load->view('dashboard',$data,true);
				
		$this->load->view('global/maintemplate', $template);
		
	}

}

?>