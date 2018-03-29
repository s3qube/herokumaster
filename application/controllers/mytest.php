<?php
class Mytest extends CI_Controller {

	function __construct()
    {
    	parent::__construct();

    }
    
    function testCommandLine() {
    
    	die("This is workin...");
    
    }

	function sendemail()
	{
		$recipients[] = 'tim@studio211.us';
		$recipients[] = 'timedgar@mac.com';
		$subject = "TEST EMAIL";
		$body = "Yep";
		
		if ($this->opm->sendEmail($subject,$body,$recipients))
			die("email sent!");
		else
			die("email not sent!");
	
	} 
	
	function viewMFSize($fileid) {
	
		$filesize = filesize($this->config->item('fileUploadPath') . "masterfiles/" . $fileid);
		
		die("FILESIZE: " . $filesize);
	
	
	}
	
	function testHtmlPdf() {
	
		include(APPPATH.'libraries/html2fpdf/html2fpdf.php');
		
		$pdf=new HTML2FPDF();
		$pdf->AddPage();
		$fp = fopen(APPPATH."../../resources/testing/test_invoice.html","r");
		$strContent = fread($fp, filesize(APPPATH."../../resources/testing/test_invoice.html"));
		fclose($fp);
		$pdf->WriteHTML($strContent);
		$pdf->Output("sample.pdf");
		echo "PDF file is generated successfully!";
	
	
	}

}