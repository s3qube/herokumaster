<?

class Preferences_model extends CI_Model {

    function PreferencesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    
    function getPrefs($userid)
    {
		
		$sql = "SELECT opm_preferences.*, opm_prefgroups.*, opm_user_preferences.lineid as has_pref FROM opm_preferences
				LEFT JOIN opm_prefgroups ON opm_prefgroups.prefgroupid = opm_preferences.prefgroupid
				LEFT JOIN opm_user_preferences ON opm_user_preferences.prefid = opm_preferences.prefid AND opm_user_preferences.userid = '".$userid."'
				ORDER BY opm_prefgroups.displayorder, opm_preferences.displayorder";
		
		return $this->db->query($sql);
        
        
    }
    
    function savePrefs($postdata)
    {
    
    	if ($postdata['userid']) {
    	
    		// first delete all prefs
    		
    		$sql = "DELETE FROM opm_user_preferences WHERE userid = " . $this->db->escape($postdata['userid']);
    		$this->db->query($sql);
    		
    		// then save prefs
    		
    		if (is_array($postdata['prefs'] )) {
    		
				foreach ($postdata['prefs'] as $prefid => $x) {
				
					if (isset($postdata['checkbox'][$prefid])) {
					
						$sql = "INSERT INTO opm_user_preferences (userid,prefid) VALUES(".$this->db->escape($postdata['userid']).",".$this->db->escape($prefid).")";
						$this->db->query($sql);
					}
				
				}
    		
    		}
    		
    		$sql = "DELETE FROM opm_user_properties WHERE userid = " . $this->db->escape($postdata['userid']);
    		$this->db->query($sql);
    		
    		if (is_array($postdata['userPropertyIDs'] )) {
    		
				foreach ($postdata['userPropertyIDs'] as $propid) {
					
					$sql = "INSERT INTO opm_user_properties (userid,propertyid,rcvemail) VALUES(".$this->db->escape($postdata['userid']).",".$this->db->escape($propid).",1)";
					$this->db->query($sql);
					
				
				}
    		
    		}
    		
    		return true;
    		
    	
    	} else {
    	
    		return false;
    	
    	}
		
		//echo "<pre>";
		
		//print_r($postdata);
		
		//echo "</pre>";
        
        
    }
    
   

}

?>