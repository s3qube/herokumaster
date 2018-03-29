<?

class invoicepdf extends CI_Controller {

    function invoicePdf()
    {
    	parent::__construct();
        
        $this->load->model("invoices_model");
        $this->load->model("products_model");
		$this->load->model("images_model");
		$this->load->model("users_model");
		
    }

    function gen($id)
    {
    
    	// get the invoice
    	
    	if(!$data['invoice'] = $this->invoices_model->fetchInvoice($id))
    		$this->opm->displayError("Invoice Not Found");
    		
    	
    	$data['user'] = $this->users_model->fetchUserInfo($data['invoice']->userid);
    		
    	// define some constants for inv header.
    
    	define("INVOICEID", $id);
    	define("REFNUM", $data['invoice']->referencenumber);
    	
    	if ($data['user']->invoiceimage_path)
    		define("INVOICEIMAGE_PATH", base_url() . "resources/files/invoiceImages/" . $data['user']->invoiceimage_path);
    	
    	
    	// we need to create temp files for all images!
    	
    	$createdImages = array(); // array to store created temp images for later deletion.
    	
    	foreach ($data['invoice']->items as $i) {
    		
    		if ($i->default_imageid != '0') {
    		
    			if ($this->images_model->writeThumbnailTempFile($i->default_imageid))
    				$createdImages[$i->id] = $i->default_imageid;
    			
    		}
    	
    	}
    	
    	$data['createdImages'] = $createdImages;
    	
        $this->load->library('pdf');

        // set document information
        $this->pdf->SetTopMargin(50);
        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF<?, example, test, guide');
        
        // set font
        $this->pdf->SetFont('dejavusansmono', '', 13);
        
        // add a page
        $this->pdf->AddPage();
     	        	
		$html = $this->load->view('invoices/pdfInvoiceData', $data, true);
        
       // die($html);
        
        $this->pdf->writeHTML($html, true, false, true, false, '');
        
        // print a line using Cell()
        //$this->pdf->Cell(0, 12, 'Example 001 - BOBOBOBOBOBOBO', 1, 1, 'C');
        
        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I');
        
        // delete temp images!
        
        foreach ($createdImages as $iID) {
        
        	unlink($this->config->item("webrootPath") . "resources/images/temp/invoice/" . $iID . ".jpg");
        
        }
        
    }
} 


?>