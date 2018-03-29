<?

class Channels_model extends CI_Model {

    function ChannelsModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchChannels($opm_productid = 0) {
    
    	// first we need property id for that product.
    	
    	if ($opm_productid != 0) {
    	
	    	$sql = "SELECT propertyid FROM opm_products WHERE opm_productid = " . $opm_productid;
	    	$query = $this->db->query($sql);
	    	$propertyid = $query->row()->propertyid;
    	
    	} else {
    	
    		$propertyid = 0;
    	
    	}
    
		$sql = "SELECT opm_channels.id, opm_channels.channel,
				opm_products_channels.id AS isassigned,
				opm_products_channels.isexception AS isexception,
				opm_property_channels.id AS isdefault
				FROM opm_channels
				LEFT JOIN opm_products_channels ON opm_channels.id = opm_products_channels.channelid AND opm_products_channels.opm_productid = ".$opm_productid . "
				LEFT JOIN opm_property_channels ON opm_channels.id = opm_property_channels.channelid AND opm_property_channels.propertyid = ".$propertyid."
				GROUP BY opm_channels.id";
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
			
			$channels[$x]['opm_productid'] = $opm_productid;		
			$channels[$x]['channelid'] = $data->id;
			$channels[$x]['channel'] = $data->channel;
			$channels[$x]['isexception'] = false;
			
			if ($opm_productid != 0) {
			
				if(isset($data->isassigned)) {
					$channels[$x]['isassigned'] = true; }
				else {
					$channels[$x]['isassigned'] = false; }
					
				if(isset($data->isdefault)) {
					$channels[$x]['isdefault'] = true; }
				else {
					$channels[$x]['isdefault'] = false; }
					
				if ($data->isexception) {
				
					$channels[$x]['isexception'] = true;
					$channels[$x]['isassigned'] = false;
			
				}
				
			}
				
			$x++;
					
		}

		
		return($channels);
    
    }
    
    
    function fetchPropertychannels($propertyid) {
    
		$sql = "SELECT opm_channels.id, opm_channels.channel, opm_property_channels.id AS isassigned
				FROM opm_channels
				LEFT JOIN opm_property_channels
				ON opm_channels.id = opm_property_channels.channelid
				AND opm_property_channels.propertyid = ".$propertyid;
		
		$query = $this->db->query($sql);
		
		$x = 0;
		
		foreach ($query->result() as $data) {
					
			$channels[$x]['channelid'] = $data->id;
			$channels[$x]['channel'] = $data->channel;
			
			if(isset($data->isassigned)) {
				$channels[$x]['isassigned'] = true; }
			else {
				$channels[$x]['isassigned'] = false; }
				
			$x++;
					
		}
		
		return($channels);
    
    }
    
	function fetchChannel($id)
    {
    
		$sql = "SELECT opm_channels.* FROM opm_channels WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("channel Not Found!");
       		return false;
       	
       	}
        
    }
    
    function createException($opm_productid,$channelid) {
    
    
    	// first delete any records from product_terr table where productid + terrid are present
    	
    	$sql = "DELETE FROM opm_products_channels WHERE opm_productid = " . $this->db->escape($opm_productid) . " AND channelid = " . $this->db->escape($channelid);
       	$query = $this->db->query($sql);
    	
    	
    	// then create exception record
    	
    	$sql = "INSERT INTO opm_products_channels (opm_productid,channelid,isexception) 
    			VALUES(" . $this->db->escape($opm_productid) . "," . $this->db->escape($channelid) . ",1)";
       	
       	if ($query = $this->db->query($sql))
       		return true;
       	else
       		return false;
    
    }
    
    function cancelException($opm_productid,$channelid) {
   
    	//  delete any records from product_terr table where productid + terrid are present
    	
    	$sql = "DELETE FROM opm_products_channels WHERE opm_productid = " . $this->db->escape($opm_productid) . " AND channelid = " . $this->db->escape($channelid);
       	$query = $this->db->query($sql);
    
       	
       	if ($query = $this->db->query($sql))
       		return true;
       	else
       		return false;
    
    }
    
    
}

?>
