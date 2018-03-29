<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->bravws->checkLogin();
    	$this->bravws->wsInit();
    	//$this->load->model('products_model');
    	//$this->load->model('properties_model');
    	//$this->load->helper('text');
    	//$this->bravws->activeNav = 'products';
    	
    	//if ($this->config->item('debugMode') == true)
    	//	$this->output->enable_profiler(TRUE);
    		
    }

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
        public function index()
        {
            require_once APPPATH."/third_party/PHPExcel.php"; 
            echo APPPATH."third_party/PHPExcel.php"; 
            echo 'test';
        }
	
	
	public function showCart() {

	   	$arrCart = $this->bravws->fetchCart();
    
	   	echo "<pre>";
	   	print_r($arrCart);
    
    }
    
    public function addItem() {
		
		$arrItem = array(
		
			"productid"=>"134",
			"productname"=>"Jor's Product",
			"sizeid"=>"1",
			"price"=>"42.00"
		
		);
		
	   	if ($this->bravws->addItemToCart($arrItem)) {
		   	
		   	$arrCart = $this->bravws->fetchCart();
		   	echo "<pre>";
		   	print_r($arrCart);
		   	
	   	} else {
		   	
		   	die("yo");
		   	
	   	}
    
	   	
    
    }
 public function sessDestroy() {
	$this->session->sess_destroy();
 }
 public function sessCart() {
 print_r($this->session->userdata('cart'));
 }
 
 function searchProperties($term='tr', $limit = 6){
      $this->db->like('property', $term);
      $this->db->join('opm_products', 'opm_products.propertyid = properties.propertyid', 'left');
      $this->db->join('opm_ws_products', 'opm_ws_products.opm_productid = opm_products.opm_productid', 'left');
      $this->db->order_by('property');
      $this->db->group_by('property');
      $this->db->limit($limit);
      $query = $this->db->get('properties');
      //print_r($query->result());
      echo '<pre>', HtmlSpecialChars(print_r($query->result()), 1), '</pre>'; 
    }
   
    
    
    public function loadProducts($offset = 0, $perPage = null, $propertyid = null, $categoryid = null, $searchtext = null, $productcode = null,$orderBy = null, $orderByAscDesc = null) {
	    
	    
	    $html = "";
	    
     	$products = $this->products_model->fetchProducts(false,$offset,$perPage,$propertyid,$categoryid,0,0,'id','asc');
	    
	    foreach ($products->result() as $p) {
		    
		    $data['p'] = $p;
		    
		    $html .= $this->load->view('products/item',$data,true);
		    
	    }
	    
	    
	    echo $html;
	    
    }
	
	
	public function checkLogin() {

		$this->load->helper('cookie');

	   	$username = $this->input->post('email');
	   	$password = $this->input->post('password');
	   	
		
		if (!$username || !$password) {
			
			die("invalid_login");
		
		}
			
		
		if ($userid = $this->users_model->checkLoginInfo($username,$password)) {
			
			// set session vars and redirect
			
			$newdata = array('userid'  => $userid,
							'logged_in' => TRUE);
							
			$this->session->set_userdata($newdata);
			
			die("success");
			
			// set cookies for "remember me"
			
			/*if ($this->input->post('rememberMe')) {
			
				set_cookie("username", $username);
				set_cookie("password", $password);

			
			}
			
			if ($this->session->userdata('loginRedirect')) {
				
				redirect($this->session->userdata('loginRedirect'), 'location');
			
			} else {
				
				redirect($this->config->item('startPage'), 'location');
			
			}*/
			
			
			
		} else {
		
			die("invalid_login");
		}


    
    }
    
    //Receives: json packet from product.view()
    //Adds it to session cart array
    public function addToSessionCart() {
       	$this->load->library('session');
          $json_string = $this->input->post('json');
          
          $cart_array = json_decode($json_string,true);//added item to put into cart
        
          $cart = $this->session->userdata('cart');
         if(is_array($cart))//check if cart has something in it already
            {
            if(is_array($cart_array[0]))//if more than one item was added, sent via json
            {
                  foreach($cart_array as $cart_item)
                  {
                   array_push($cart,$cart_item);//add each new addition to cart array
                   }
            }
            else
            {
            array_push($cart,$cart_array);//add one new addition to cart array
            }
           $this->session->set_userdata('cart', $cart);
            }
         else //empty cart in session
         {
             $empty_cart = array();
             if(is_array($cart_array[0]))//if more than one item was added, sent via json
                {
                      foreach($cart_array as $cart_item)
                      {
                       array_push($empty_cart,$cart_item);//add each new addition to cart array
                       }
                }
                else
                {
                array_push($empty_cart,$cart_array);//add one new addition to cart array
                }
          $this->session->set_userdata('cart', $empty_cart);
         }
          
        
        
         // echo '<pre>', HtmlSpecialChars(print_r($cart, 1)), '</pre>';
    }
	
    
    
     public function editSessionCart() {
       
          $json_string = $this->input->post('json');
          
          $cart_array = json_decode($json_string,true);//added item to put into cart
          //echo $json_string;
          //print_r($cart_array);
          $cart = $this->session->userdata('cart');
          
            if(is_array($cart))//check if cart has something in it already
            {
                $index = 0; 
                foreach ($cart as $item)
                {
                    if($item['sku'] == $cart_array['sku']) //then delete item
                     {
                      
                      $edited_item = $item;//get original values
                      $edited_item['qty'] = $cart_array['qty'];
                      array_splice($cart, $index, 1);//remove item at index
                      array_push($cart,$edited_item);//add each edited addition to cart array
                     
                       //echo '<pre>cart', HtmlSpecialChars(print_r($cart, 1)), '</pre>';
                     }
                
                     $index++;
                 }
            }
             $this->session->set_userdata('cart', $cart);//reset the cart without item
            
         
     }
     public function removeItemFromSessionCart() {
       
          $json_string = $this->input->post('json');
          
          $cart_array = json_decode($json_string,true);//added item to put into cart
 
          //grab current session cart
          $cart = $this->session->userdata('cart');
          //echo '<pre>before', HtmlSpecialChars(print_r($cart, 1)), '</pre>';
          if(is_array($cart))//check if cart has something in it already
          {
             $index = 0; 
             foreach ($cart as $item)
             {
                 //echo $item['sku'].'equal? '.$cart_array['sku'];
                 if($item['sku'] == $cart_array['sku']) //then delete item
                 {
                  array_splice($cart, $index, 1);//remove item at index
                  $this->session->set_userdata('cart', $cart);//reset the cart without item
                 }
                 $index++;
             }
          }
         
          
     }//end remove item from session cart
     
     
     
     //**********SEARCH
     public function search(){
      
	  if(!empty($_GET)){
        $term = $this->input->get("term");
        echo 't'.$term;
        $this->load->model('genres_model');
        $this->load->model('categories_model');
        $this->load->model('products_model');
        
        $artists = $this->products_model->searchProperties($term, 3);
        foreach ($artists->result() as $art) {           
          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$art->property.' :: Artist</div></div>', 'value' => $art->property." :: Artist", 'link' => base_url().'products/byArtist/'.$art->propertyid);
        }
        
        $genres = $this->genres_model->searchGenres($term, 3);
        foreach ($genres->result() as $g) {           
          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$g->genre.' :: Genre</div></div>', 'value' => $g->genre." :: Genre", 'link' => base_url().'products/byGenre/'.$g->id);
        }
        
        $categories = $this->categories_model->searchCategories($term, 3);
        foreach ($categories->result() as $cat) {           
          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$cat->category.' :: Category</div></div>', 'value' => $cat->category." :: Category", 'link' => base_url().'products/byCategory/'.$cat->categoryid);
        }
        
        $this->load->view('global/json_tpl', array('json' => json_encode($response)));
      }
	 $response = 'testing';
        $this->load->view('global/json_tpl', array('json' => json_encode($response)));  
	  
    }

    public function readExcel()
    {
     $this->load->library('excel');
    
     $objReader = PHPExcel_IOFactory::createReader('Excel5');
     //$path = base_url().'resources/Zion2014.xlsx';
     $objPHPExcel = $objReader->load('resources/Zion2014.xls');
     $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
     
     
//     Grab find opm_products.product_code = style code from excel
//      if array[s-1x] has value, then add 48 (SM, small)
//					add 45 (MD, medium)
//					add 44(LG, large)
//					add 49 (XL, extra large)
//		to opm_ws_products_sizes.sizeid,  with ppm_productid, sku, isactive, createdby, lastmodified, modifiedby from opm_products, opm_skus
//
//      if array[2x] has value, then add 18 (2x, 2 extra large)
//
//      if array[3x] has value, then add 23 (3x, 3 extra large)
//
//       note: the numbers come from the ppm_sizes table, they are size_ids
     //stylecode = E index
     
     $date = new DateTime();
     $timestamp = $date->getTimestamp();
     $i = 0;
     unset($sheetData[1]); unset($sheetData[0]);//remove headers from excel
     foreach ($sheetData as $data) {
	//$data contains opm_products info for product with productcode= $stylecode
        echo '<pre>The excel data', HtmlSpecialChars(print_r($data, 1)), '</pre>';
        
        //for clarity
        $style_code =   $data['E'];
        $small_lg   =   $data['G'];
        $double_xl  =   $data['H'];
        $triple_xl  =   $data['I'];
        $NA         =   $data['J'];
        
       
        $insert_data_to_opm = FALSE;//when there is a match, call insertDataOpmWsProducts($opm_product_id, $base_price)
        
        
        $this->db->where("opm_products.productcode = '$style_code'");  
        $query = $this->db->get('opm_products');//getting info from opm.products for this stylecode
        $count = $query->num_rows(); //counting result from query
        
        $i++;
        if($count > 0)
        {    
        foreach ($query->result() as $row)
        {           
            if($i > 10){}//for testing, prevent too many entries at once
            else{
                       // echo '<pre>The opm_prouduct data style_code = productcode ', HtmlSpecialChars(print_r($row, 1)), '</pre>';
                        //INSERT FOR EACH SIZE IF IT EXISTS IN THE EXCEL
                        //add opm_ws_products_sizes.sizeid,  with ppm_productid, sku, isactive, createdby, lastmodified, modifiedby from opm_products, opm_skus
                        //to opm_ws tables
//                        [G] => S-1X
//                        [H] => 2X
//                        [I] => 3X
                        if(isset($small_lg) && strlen($small_lg)>1)//s-1x, insert data into opm_ws_product_sizes
                            {//add 4 entries into opm_ws
                             $sku_1 = $style_code.'48';
                             $sku_2 = $style_code.'45';
                             $sku_3 = $style_code.'44';
                             $sku_4 = $style_code.'49';
                             $base_price = $small_lg;//for clarity
                             $insert_data = array(
                                        array(
                                                'sizeid' => 48 ,
                                                'opm_productid' => $row->opm_productid ,
                                                'sku' => $sku_1,
                                                'isactive' => 1 ,
                                                'createdby' => 1,
                                                'lastmodified' =>  $timestamp,
                                                'modifiedby'=> 1
                                        ),
                                        array(
                                                'sizeid' => 45 ,
                                                'opm_productid' =>  $row->opm_productid ,
                                                'sku' => $sku_2,
                                                'isactive' => 1 ,
                                                'createdby' => 1,
                                                'lastmodified' =>  $timestamp,
                                                'modifiedby'=> 1
                                        ),
                                        array(
                                                  'sizeid' => 44 ,
                                                  'opm_productid' =>  $row->opm_productid ,
                                                  'sku' => $sku_3,
                                                  'isactive' => 1 ,
                                                  'createdby' => 1,
                                                  'lastmodified' =>  $timestamp,
                                                  'modifiedby'=> 1
                                               ),
                                        array(
                                                  'sizeid' => 49 ,
                                                  'opm_productid' =>  $row->opm_productid ,
                                                  'sku' => $sku_4,
                                                  'isactive' => 1 ,
                                                  'createdby' => 1,
                                                  'lastmodified' =>  $timestamp,
                                                  'modifiedby'=> 1
                                               )
                             );
                             
                             $this->insertSizesOpmWsProductSizes($insert_data, $sku_1);
                             echo 'inserted into opmwsproduct sizes';
                             print_r($insert_data);
                             echo '<br><br>';
                             if(!$insert_data_to_opm)
                                 {
                                 $this->insertDataOpmWsProducts($row->opm_productid, $base_price); 
                                 $insert_data_to_opm = TRUE;
                                 }
                             
                             }
                             
                             if(isset($double_xl) && strlen($double_xl)>1)
                             {
                                 $sku_1 = $style_code.'18';
                                 $base_price = $double_xl;//for clarity
                                 $insert_data = array(
                                                'sizeid' => 18 ,
                                                'opm_productid' => $row->opm_productid ,
                                                'sku' => $sku_1,
                                                'isactive' => 1 ,
                                                'createdby' => 1,
                                                'lastmodified' =>  $timestamp,
                                                'modifiedby'=> 1
                                        );
                                 
                                 $this->insertSizesOpmWsProductSizes($insert_data, $sku_1);
                                 echo 'inserted into opmwsproduct sizes';
                                    print_r($insert_data);
                                    echo '<br><br>';
                                
                                 if(!$insert_data_to_opm)
                                 {
                                 $this->insertDataOpmWsProducts($row->opm_productid, $base_price); 
                                 $insert_data_to_opm = TRUE;
                                 }
                                
                             }
                              
                             if(isset($triple_xl ) && strlen($triple_xl )>1)
                             {   $sku_1 = $style_code.'23';
                                 $base_price = $triple_xl;//for clarity
                                 $insert_data = array(
                                                'sizeid' => 23 ,
                                                'opm_productid' => $row->opm_productid ,
                                                'sku' => $sku_1,
                                                'isactive' => 1 ,
                                                'createdby' => 1,
                                                'lastmodified' =>  $timestamp,
                                                'modifiedby'=> 1
                                        );
                                 
                                 $this->insertSizesOpmWsProductSizes($insert_data, $sku_1);
                                 echo 'inserted into opmwsproduct sizes';
                             print_r($insert_data);
                             echo '<br><br>';
                                 if(!$insert_data_to_opm)
                                 {
                                 $this->insertDataOpmWsProducts($row->opm_productid, $base_price); 
                                 $insert_data_to_opm = TRUE;
                                 }
                             }
                            
                       
                             if(isset($NA ) && strlen($NA )>2)
                             {  
                                 
                                 $sku_1 = $style_code.'999';
                                 $base_price = $NA;//for clarity
                                 $insert_data = array();
                                                $insert_data['sizeid'] = 999;
                                                $insert_data['opm_productid'] = $row->opm_productid;
                                                $insert_data['sku'] = $sku_1;
                                                $insert_data['isactive'] = 1;
                                                $insert_data['createdby'] = 1;
                                                $insert_data['lastmodified'] =  $timestamp;
                                                $insert_data['modifiedby'] = 1;
                                        
                                 echo '<H1> match</h1><br><br>';
                                 print_r($insert_data);
                                 echo '<br>';
                                 $this->insertSizesOpmWsProductSizes($insert_data, $sku_1);
                                 $this->insertDataOpmWsProducts($row->opm_productid, $base_price); 
                             }
                             
                             
                        $insert_data_to_opm = FALSE;  //reset match condition

                     
                  
            } 
                $i++;     
                    
            }//end opm_products foreach
        }//end count if
        
        
        
        
        
                
        }
    }
    
    public function insertDataOpmWsProducts($opm_product_id, $base_price)
    {
    //add product to opm_ws_products once (NOT 4 TIMES LIKE SIZES)
                             //set flag to true or check if exists when inserting
                             //sitebrandid = 4, opm_productid = $row->opm_product_id, isactive = 1, baseprice = $base_price, isfeatured = 0, deletedate = 0, genreid = 0 (not avail 6/4/2014)
                              
        $query = $this->db->get_where('opm_ws_products', array(//making selection
            'opm_productid' => $opm_product_id
        ));
        $count = $query->num_rows(); //counting result from query

        if ($count === 0) {
        $insert_data_opm_ws_products = 
                                        array(
                                                'sitebrandid' => 4,
                                                'opm_productid' => $opm_product_id,
                                                'isactive' => 1 ,
                                                'baseprice' => $base_price,
                                                'isfeatured' => 0,
                                                'deletedate' => 0,
                                                'genreid' => 0
                                               
                                        );
                              $query = $this->db->insert('opm_ws_products', $insert_data_opm_ws_products); 
                              return $query;
        }
                              
        }
    
    //PASS IN SKU TO CHECK IF IT EXISTS IN DB ALREADY
    public function insertSizesOpmWsProductSizes($insert_data, $sku_1)
    {
        $query = $this->db->get_where('opm_ws_product_sizes', array(//making selection
            'sku' => $sku_1
        ));
        $count = $query->num_rows(); //counting result from query

       
        if ($count === 0) {
            $depth = $this->array_depth($insert_data);//get depth
            
            if($depth >1)
            {
            $query = $this->db->insert_batch('opm_ws_product_sizes', $insert_data); 
            return $query;
            }
            else
            {
            $this->db->insert('opm_ws_product_sizes', $insert_data); 

            }
            
            }
    }

    
    public function array_depth(array $array) {
    $max_depth = 1;

    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = array_depth($value) + 1;

            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}
function searchProperties2($term='b', $limit = 6){
      $this->db->like('property', $term);
      $this->db->join('opm_products', 'opm_products.propertyid = properties.propertyid', 'left');
      $this->db->join('opm_ws_products', 'opm_ws_products.opm_productid = opm_products.opm_productid', 'union');
      $this->db->order_by('property');
      $this->db->group_by('property');
      $this->db->limit($limit);
      $query= $this->db->get('properties');
      foreach($query->result() as $row)
      {print_r($row);
      }
      return $this->db->get('properties');
      
    }
    
    
     //**********SEARCH
     public function search2()
             {
     // if(!empty($_GET)){
        $term = "b";
       
        //$term = $this->input->get("term");
        $this->load->model('genres_model');
        $this->load->model('categories_model');
        $this->load->model('products_model');
        
        //grab artist info that matches from opm_product and opm_ws_products (union)
        $artists = $this->products_model->searchProperties($term, 3);
        foreach ($artists->result() as $art) {           
          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$art->property.' :: Artist</div></div>', 'value' => $art->property." :: Artist", 'link' => base_url().'products/byArtist/'.$art->propertyid);
          //grab 3 products from this artist to attach underneath
          
            $products_of_artist = $this->products_model->fetchProductsFromPropertyID($art->propertyid, 3);
            echo 'num rows '.$products_of_artist->num_rows();
            
            foreach($products_of_artist->result() as $product)
            {
           
            $response[$product_num] = array('label' => '<div class="hoverSearchResult productSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$product->productname.' :: Product</div></div>', 'value' => $product->productname." :: Product", 'link' => base_url().'products/view/'.$product->opm_productid);
            //echo '<pre>bb', HtmlSpecialChars(print_r($product_array), 1), '</pre>'; 
            
            }
           
          }
         echo '<pre>bb', HtmlSpecialChars(print_r($response), 1), '</pre>'; 
        
        }
             
          public function searchprop2()
         {
        $this->load->helper('url');
            
        // if(!empty($_GET)){
        $term = "b";
       
        //$term = $this->input->get("term");
        $this->load->model('genres_model');
        $this->load->model('categories_model');
        $this->load->model('products_model');
        
        //grab artist info that matches from opm_product and opm_ws_products (union)
        
          
          $products_of_artist = $this->products_model->fetchProductsFromPropertyID(1066, 6);
          echo 'num rows '.$products_of_artist->num_rows();
          foreach($products_of_artist->result() as $product)
          {
              echo 'bb';
          $product_array[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$product->productname.' :: Product</div></div>', 'value' => $product->productname." :: Product", 'link' => base_url().'products/view/'.$product->opm_productid);
          echo '<pre>bb', HtmlSpecialChars(print_r($product_array), 1), '</pre>'; 
            
          }
          
        }
        public function searchGenres($term='R'){
        
        $genres = $this->genres_model->searchGenres($term, 3);
        foreach ($genres->result() as $g) {           
          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$g->genre.' :: Genre</div></div>', 'value' => $g->genre." :: Genre", 'link' => base_url().'products/byGenre/'.$g->id);
           echo '<pre>bb', HtmlSpecialChars(print_r($response), 1), '</pre>'; 
          
        }
        }
//        $genres = $this->genres_model->searchGenres($term, 3);
//        foreach ($genres->result() as $g) {           
//          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$g->genre.' :: Genre</div></div>', 'value' => $g->genre." :: Genre", 'link' => base_url().'products/byGenre/'.$g->id);
//          
//          
//        }
//        
//        $categories = $this->categories_model->searchCategories($term, 3);
//        foreach ($categories->result() as $cat) {           
//          $response[] = array('label' => '<div class="hoverSearchResult"><div class="resImg"><img src="'.base_url().'imageclass/viewThumbnail/0/56" class="resThumb" /></div><div class="resText">'.$cat->category.' :: Category</div></div>', 'value' => $cat->category." :: Category", 'link' => base_url().'products/byCategory/'.$cat->categoryid);
//        }
//        
//        
        //$this->load->view('global/json_tpl', array('json' => json_encode($response)));
      //}
    
        
}
/* End of file ajax.php */
/* Location: ./application/controllers/welcome.php */
 

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */