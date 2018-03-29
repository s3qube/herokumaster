<?

class Articles_model extends CI_Model {

    function ArticlesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchArticles()
    {
    
		$sql = "SELECT opm_articles.*
				FROM opm_articles ";
				
		$sql .=" GROUP BY opm_articles.id
				ORDER BY opm_articles.article";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchArticle($id)
    {
    
		$sql = "SELECT opm_articles.* FROM opm_articles WHERE id = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Article Not Found!");
       		return false;
       	
       	}
        
    }
	
	/*
	
	function saveArticle($arrPostData) {
	
         	
		if ($arrPostData['articleid']) { // we have a articleid, update
     	
     		$sql = "UPDATE opm_articles
     				SET article = ". $this->db->escape($arrPostData['article']) .",
     				code = ". $this->db->escape($arrPostData['code']) . ",
     				id = ". $this->db->escape($arrPostData['id']);
    
     							
     				
     		$sql .= " WHERE id = '".$arrPostData['articleid']."'";
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['articleid'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new body style, insert
     	
     		
     		$sql = "INSERT INTO opm_articles (article,code,categoryid)
    			VALUES (".$this->db->escape($arrPostData['article']).",".$this->db->escape($arrPostData['code']).",".$this->db->escape($arrPostData['categoryid']).")";
       		
       		if ($query = $this->db->query($sql)) {
       		
       			return $this->db->insert_id(); 
       		
       		} else {
       		
       			return false;
       		
       		}
       		
     	
     	}
    
    }

	*/

}


//
?>