<?

class Categories_model extends CI_Model {

    function CategoriesModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchCategories() {
    
		$sql = "SELECT opm_categories.*
				FROM opm_categories
				LEFT JOIN opm_products ON opm_products.categoryid = opm_categories.categoryid";
				
		if (USE_PERMISSION_QUERY)
    		$sql .= PERMISSION_QUERY . " WHERE canview.id IS NOT NULL";
				
		$sql .=" GROUP BY opm_categories.categoryid
				ORDER BY opm_categories.category";
        	
        $query = $this->db->query($sql);
        return $query;
    }
    
    function fetchCategoriesArray($opm_productid = null, $productlineid = null) // options allow for pulling categories w/ associations
    {
    
    	// declare vars
    	
    	$query_join = "";
    	$query_select = "";
		
		/*if ($opm_productid) { // check if this product is visible by categories
       	
       		$query_join = "LEFT JOIN opm_products_categories ON opm_products_categories.categoryid = opm_categories.categoryid AND opm_products_categories.opm_productid = '".$opm_productid."'";
			$query_select = ", opm_products_categories.id AS isassigned";
		
		}*/
		// build array of categories, parent child style  
    	
    	$refs = array();
		$list = array();

		$sql = "SELECT opm_categories.categoryid, opm_categories.parentid, opm_categories.category
				$query_select
				FROM opm_categories 
				$query_join
				ORDER BY opm_categories.category";
				
				
		$query = $this->db->query($sql);
		
		// the code below puts the groups into a neat parent/child array
		
		foreach ($query->result() as $data) {
			$thisref = &$refs[ $data->categoryid ];
			
			$thisref['categoryid'] = $data->categoryid;
			$thisref['parentid'] = $data->parentid;
			$thisref['category'] = $data->category;
			
			if(isset($data->isassigned))
				$thisref['isassigned'] = true;
		
			if ($data->parentid == 0) {
				$list[ $data->categoryid ] = &$thisref;
			} else {
				$refs[ $data->parentid ]['children'][ $data->categoryid ] = &$thisref;
			}
		}
		
		return $list;
		 	
        
    }
    
    
    function fetchCategory($id) {
    
		$sql = "SELECT opm_categories.* FROM opm_categories WHERE categoryid = " . $this->db->escape($id);
       	$query = $this->db->query($sql);
       	
       	if ($query->num_rows() > 0) {
       	
       		return $query->row();
       	
       	} else {
       	
       		$this->opm->displayError("Category Not Found!");
       		return false;
       	
       	}
        
    }
	//Globosoft
	function fetchFirstLetterLinks($activeLetter = null) { //  get distinct first letters of category names!
    
    	    
    	$sql = "SELECT DISTINCT LEFT(opm_categories.category, 1) AS categoryFirstLetter FROM opm_categories
    			WHERE isactive = 'Y'
    			ORDER BY category";
    	
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $row) {
    		
    		if ($activeLetter == $row->categoryFirstLetter)
    			$arrFL[] = "<a href=\"".base_url()."categories/search/0/".$row->categoryFirstLetter."\" class=\"redLink\">".$row->categoryFirstLetter."</a>&nbsp;&nbsp;";
    		else
    			$arrFL[] = "<a href=\"".base_url()."categories/search/0/".$row->categoryFirstLetter."\">".$row->categoryFirstLetter."</a>&nbsp;&nbsp;";

    	}
		
		return $arrFL;
		
		}
		
		

	function fetchListCategories($inc_inactive = false, $inc_noproducts = false, $returnTotal = false, $searchText = null, $offset = 0, $perPage = null, $firstLetter = null, $inc_nopermission = false)
    {
    	$query_where = "";
    	$query_join = "";
    	
    	if (!$inc_inactive)
    		$query_where .= " AND opm_categories.isactive = 'Y'";
    		
    	if (USE_PERMISSION_QUERY) {
    	
    		$query_join .= PERMISSION_QUERY;
    		$query_where .= " AND canview.id IS NOT NULL";
    	
    	}
   
    	
    	if ($searchText)
    		$query_where .= " AND opm_categories.category LIKE '%".$searchText."%'";
    		
    	if ($firstLetter)
    		$query_where .= " AND opm_categories.category LIKE '".$firstLetter."%'";
    	
		
		$sql = "SELECT opm_categories.*
				FROM opm_categories
				LEFT JOIN opm_products ON opm_products.categoryid = opm_categories.categoryid
				$query_join
					WHERE opm_categories.categoryid  IS NOT NULL
					$query_where
				GROUP BY opm_categories.categoryid
				ORDER BY opm_categories.category";

    	if ($perPage)
    		$sql .=" LIMIT $offset, $perPage";
    	        	
        $query = $this->db->query($sql);
    
    	if (!$returnTotal)
    		return $query;
    	else
    		return $query->num_rows();
    
    }
	
	
	function saveCategoryInfo($arrPostData) {
         	
		if (isset($arrPostData['categoryid'])) { // we have a categoryid, update
     	
     		$sql = "UPDATE opm_categories
     				SET category = ". $this->db->escape($arrPostData['category']) .",
     				parentid = " . $this->db->escape($arrPostData['parentcategoryid']);
     							
     				
     		$sql .= " WHERE categoryid = '".$arrPostData['categoryid']."'";
     
    	
    		if ($this->db->query($sql)) {
    		
    			return $arrPostData['categoryid'];
    		
    		} else {
    		
    			return false;
    		
    		}
    		
     	
     	
     	} else { // new category, insert
     	
     		
     		$sql = "INSERT INTO opm_categories (parentid,category,isactive)
    			VALUES (".$this->db->escape($arrPostData['parentcategoryid']).",".$this->db->escape($arrPostData['category']).",'Y')";
       		
       		if ($query = $this->db->query($sql)) {
       		
       			return $this->db->insert_id(); 
       		
       		} else {
       		
       			return false;
       		
       		}
       		
     	
     	}
    
    }
	
	 function fetchCategoryInfo($id) {
    
    	$sql = "SELECT * FROM opm_categories WHERE categoryid = '".$id."'";
    	$query = $this->db->query($sql);
    	$result = $query->row();
    	
    	return $result;
    
    }

}
//
?>