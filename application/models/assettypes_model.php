<?

class AssetTypes_model extends CI_Model {

    function AssetTypesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchAssetTypes()
    {
    
		$sql = "SELECT opm_asset_types.*
				FROM opm_asset_types
				ORDER BY displayorder";
				
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchAssetType($id)
    {
    
		$sql = "SELECT opm_asset_types.* FROM opm_asset_types WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Assettype Not Found!");
       		return false;
       	
       	}
        
    }
    
}

?>