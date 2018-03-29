<?php
class Templates extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->opm->checkLogin();
    	$this->opm->opmInit();
    	$this->load->model('properties_model');
    	$this->load->helper('text');
    	$this->opm->activeNav = 'production';
    	
    	global $searchArgList;
    	$searchArgList = array("searchText","firstLetter");
    	
    }

	function index() // index will be the property list.
	{
	
		redirect("/templates/search");	

	}
	
	function search($path = "") // index will be the property list.
	{	

		if (checkPerms('can_view_templates',true)) {
			
			if ($path)
				$path = urldecode($path) . "/";
		
			$path = str_replace("|","/",$path);
			
			// don't allow the user to get above the templates folder in the file system!
			
			$path = str_replace("..","",$path);
			
			$data['path'] = $path;
			
		
			$data['arrFiles'] = array();
			
			// determine go up url!
			
			if ($path) {
			
				$convPath = str_replace("/","|",$path) ;
			
				$splitPath = explode("|",$convPath);
								
				unset($splitPath[sizeof($splitPath)-1]);
				unset($splitPath[sizeof($splitPath)-1]);

				$strFolderPath = implode("|",$splitPath);
				
				$data['goUpURL'] = base_url() . "templates/search/" . urlencode($strFolderPath);
			
			}
			
			
		
			if ($handle = opendir($this->config->item('templatePath').$path)) {
	
			    while (false !== ($file = readdir($handle))) {
			        
			        if ($file != '.' && $file != '..' && !strstr($file, '.jpg') && ! (substr($file,0,1) == '.') ) {
			        
			        	$data['arrFiles'][]['filename'] = $file;
			        	$data['arrFiles'][sizeof($data['arrFiles'])-1]['filesize'] = filesize($this->config->item('templatePath').$path.$file);
			        		        			        
				        if (is_dir($this->config->item('templatePath').$path.$file)) {
				        	
				        	$data['arrFiles'][sizeof($data['arrFiles'])-1]['is_dir'] = true;
				        	$encodePath = str_replace("/","|",$path);
				        	$data['arrFiles'][sizeof($data['arrFiles'])-1]['folder_url'] = base_url() . "templates/search/" . urlencode($encodePath  . $file);
				        
				        }
				        	
				         if (!is_dir($this->config->item('templatePath').$path.$file)) {
				         
				         	$splitName = explode(".",$file);
				         	$thumbName = $splitName[0].".jpg";
				         	
				         	$encodePath = str_replace("/","|",$path);
				         	//print_r($thumbName);
				         	
				         	if (file_exists($this->config->item('templatePath').$path.$thumbName)) 
				     			$data['arrFiles'][sizeof($data['arrFiles'])-1]['thumb_url'] = $encodePath.$thumbName;
				         	
				     			
				     		$data['arrFiles'][sizeof($data['arrFiles'])-1]['download_url'] = base_url() . "templates/download/" . urlencode($encodePath  . $file);

				         
				         }
				        	
			        
			        }
			        
			    }
				
			    closedir($handle);
			}
			
			// sort arrFiles by filename!
			
			multi2dSortAsc($data['arrFiles'], 'filename');
			
			// sort files, put directories first!
			
			foreach ($data['arrFiles'] as $key => $f) {
			
				if (isset($f['is_dir']))
					$arrTemp[] = $f;
			
			}
			
			foreach ($data['arrFiles'] as $key => $f) {
			
				if (!isset($f['is_dir']))
					$arrTemp[] = $f;
			
			}
			
			if (isset($arrTemp))
				$data['arrFiles'] = $arrTemp;
			else
				unset($data['arrFiles']);
				
			
			
			
			
			$template['nav2'] = "Search Templates";
			
			//$config['full_tag_open'] = '<p>';
			//$config['full_tag_close'] = '</p>';
			
			$template['page_title'] = "Search Templates";
			$template['bigheader'] = "Search Templates";
			//$template['contentNav'] = $this->load->view('templates/searchNav',$data,true);
			$template['content'] = $this->load->view('templates/search', $data,true);
			
	        $arrJS['scripts'] = array('mootools-release-1.11','opm_scripts'); // 
	        $template['javascripts'] = $this->load->view('global/javascripts',$arrJS,true);
	        
			$this->load->view('global/maintemplate', $template);
			
		}
	
	}
	
	
	function fetchThumb($filename) {
	
		if (checkPerms('can_view_templates',true)) {

			$filename = urldecode($filename);
			$filename = str_replace("|","/",$filename);
			$path = $this->config->item('templatePath').$filename;
			//die($path);
			$fileData = file_get_contents($path);
			
			header("Content-type: " . 'image/jpeg');
			echo $fileData;
			
			die();
		
		}
	
	
	}
	
	function download($path) {
	
	
		if (checkPerms('can_view_templates',true)) {
	
		
			$path = urldecode($path);
			$splitPath = explode("|",$path);
			$filename = $splitPath[sizeof($splitPath)-1];
			
			$path = str_replace("|","/",$path);
			$path = $this->config->item('templatePath').$path;
	
			$fileData = file_get_contents($path);
			
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			//header("Content-Type: " . $image->image_type);
			// change, added quotes to allow spaces in filenames, by Rajkumar Singh
			header("Content-Disposition: attachment; filename=\"". $filename ."\";" );
			header("Content-Transfer-Encoding: binary");
			
			echo $fileData;
			
			die();
		
		}
	
	
	}
	

}
?>