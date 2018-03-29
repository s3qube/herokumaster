<?php
class Cron extends CI_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('products_model');
    	$this->load->model('users_model');
    	
    	//if($_SERVER['SCRIPT_FILENAME'] != 'purgeFailedUploads.php')
 		//	exit; 

    }
    
    function sendInvoiceSummaryReports() {
    
    	$this->load->model('invoices_model');
    	$this->load->model('users_model');
    	
    	$statusIDs = implode(",",$this->config->item('notificationStatuses'));
    	
    	$notificationsToSend = array();
    
    	// first let's get all invoices with selected status
    	
    	$invoices = $this->invoices_model->fetchInvoices(false, 0,null, null, $statusIDs, null, null, null, null, null, null, null,"userid");
    	
    	foreach ($invoices->result() as $i) { // create an array for each user receiving notifications
    	
    		$notificationsToSend[$i->ownerid][] = array("invoiceid"=>$i->id,"statusid"=>$i->statusid,"status"=>$i->status);
    	    	
    	}
    	
    	foreach ($notificationsToSend as $uid=>$u) { // now loop through each user and send notification.
    	
    		$data['invoices'] = $u;
    		$subject = $this->load->view('emails/invNotificationSummary_subject',$data,true);
			$body = $this->load->view('emails/invNotificationSummary_body',$data,true);
			
			$user = $this->users_model->fetchUserInfo($uid);
			$recipients = array(0=>$user->login);
    		
    		$this->opm->sendEmail($subject,$body,$recipients);
    		
    		echo "<pre>";
    		print_r($u);
    	
    	
    	}
    	
    	
    	
    //	opmLog("Hey Log FILE");
    
    
    }
    
    
    function checkUnconfirmedUploads() {
    
    	// ok, so we want to check files that are over a half hour old 
    	// and see if filesize = dbfilesize.
    	// if so, confirm the upload (and email user)
    	// if not, 
    
    
    }
    
    function exportProductXml() {
   
   		$this->output->enable_profiler(FALSE);
	
		$fileName = "Products_" . mktime() . ".xml";
		$filePath = $this->config->item('ftpPath') . $fileName;
		
		$arrProductIDs = array();
	
		//if (checkPerms('can_export_products',true)) {
		
			$prods = $this->opm->fetchProductsForExport();
			
			$xmlProducts = ""; 
			
			$xmlProducts .= "<Products>";
			
			foreach ($prods as $p) {
							
				$data['p'] = $p;
				$xmlProducts .= $this->load->view('xml/product',$data,true);
				
				$arrProductIDs[] = $p['opm_productid'];
				
			}
			
			$xmlProducts .= "</Products>";
			
			if ($arrProductIDs) {
			
				if (file_put_contents($filePath, $xmlProducts)) {
						
				
					$this->products_model->setExportDate($arrProductIDs);
					
					
					echo $_SERVER['REMOTE_ADDR'] . " is the remote address<br/><br/>";
					
					die("Product export successfully completed on " . date('l jS \of F Y h:i:s A') . ". Exported ".sizeof($arrProductIDs)." products.");
					
					// CHANGE EXPORT DATE OF ALL PRODUCTS!!!
	
				}
				
			} else {
			
				die("Product export successfully completed on " . date('l jS \of F Y h:i:s A') . ". Nothing to export.");
			
			}
			
		//}
	
	}
	
	function importPaymentXML() {
		
		echo "<pre>";
		
		$this->load->model('invoices_model');
		
		$statusPaid = $this->config->item("invStatusPaid");
		
		$this->output->enable_profiler(FALSE);
		$payCount = 0;
		echo "Beginning import script.\n";
		
		$errors = array();
		
		$outputDir = $this->config->item('ftpPath')."Output/";
		
		if ($handle = opendir($outputDir)) {
		
		    while (false !== ($file = readdir($handle))) {

		        if ((substr($file,strlen($file)-4) == '.xml') && (substr($file,0,15) == "NavisionPayments")) { // only get xml files.
		        
		        	echo "Beginning import of file " . $file . "\n";
		        
		        	if ($xml = simplexml_load_file($outputDir . $file)) {
		        							
						foreach ($xml->Payment as $index=>$p) {
							
							$paymentErrors = sizeof($errors);
							$paymentCriticalErrors = 0;
							
							// load invoice data for this payment
							
							if (isset($p->Id)) {
							
								echo "\n\nBeginning import of Payment ID " . $p->Id . "\n";
							
								if (!$invoice = $this->invoices_model->fetchInvoice($p->Id)) { // make sure we can find associated invoice.
								
									$errors[] = "Cannot find Invoice # " . $p->Id . " in file " . $file . " payment # " . $index . ". PAYMENT SKIPPED.\n";
								
								} else {
								
									// confirm that we have all nessa data to import payment.
	
									if (isset($p->CheckNo)) {
									
										$checkNumber = $p->CheckNo;
									
									} else {
									
										$errors[] = "Payment for Invoice #" . $p->Id . " in file ". $file ." has no check number.\n";
										$paymentCriticalErrors++;
									
									}
										
									if (isset($p->PaymentDate) && strtotime($p->PaymentDate)) {
									
										$payDate = strtotime($p->PaymentDate);
									
									} else {
									
										$errors[] = "Could not parse Payment Date for Invoice #" . $p->Id . " in file ". $file .".\n";
										$paymentCriticalErrors++;
									
									}
									
									
									if (isset($p->PaymentAmount)) {
									
										$payAmount = $p->PaymentAmount;
									
									} else {
									
										$errors[] = "Invoice #" . $p->Id . " in file ". $file ." has no payment amount.\n";
										$paymentCriticalErrors++;
										
									}
									
									
									if ($payAmount != $invoice->total) {
									
										$errors[] = "Payment amount and Invoice total do not match on ID ". $p->Id . " in file ". $file .".\n";
									
									}
									
									if ($paymentCriticalErrors == 0) { // no critical errors on this payment, import.
									
										$sql = "UPDATE opm_invoices
												SET paymentdate = ".$this->db->escape($payDate).",
													checknumber = ".$this->db->escape($checkNumber).",
													statusid = ".$statusPaid.",
													lastmodified = ".mktime()."
													WHERE id = " . $this->db->escape($invoice->id);
													
										
										if (@$this->db->query($sql)) {
									
											if (sizeof($errors) == $paymentErrors) { // no errors on this payment.
												
												echo "Payment for Invoice ID " . $p->Id . " imported without errors.\n";
											
											} else {
											
												echo "Payment for Invoice ID " . $p->Id . " encountered errors.\n";
	
											}
											
										} else {
										
											$errors[] = "IMPORT SQL FAILED for payment ID " . $p->Id . ", PAYMENT SKIPPED.";
										
										}
									
									} else {
									
										echo "Payment for Invoice ID " . $p->Id . " encountered critical errors. PAYMENT SKIPPED!\n";
									
									}
									
									$payCount++;
								
								}
							
							} else {
							
								$errors[] = "Payment # " . $index . " in file " . $file . " has no Invoice ID. PAYMENT SKIPPED!\n";
							
							}

						}
			        
			        } else {
				
						$errors[] = "Could not parse XML in file " . $file . " \n.";
					
					}
			        
				} 
		    
		    }
		    
		    if ($errors) {
		    	
		    	$errTxt = "\nThe following errors were encountered during the import:\n";
		    	
		    	foreach ($errors as $err) {
		    	
		    		$errTxt .= $err;
		    	
		    	}
		    	
		    	echo $errTxt;
		    	
		    	// email administrators
		    	
		    	mail("tim@studio211.us","ERRORS ENCOUNTERED DURING PAYMENT IMPORT!",$errTxt );
		    	
		    }
		    
		
		    closedir($handle);
		    
		    die("\n\nPayment import successfully completed on " . date('l jS \of F Y h:i:s A') . ". Imported ".$payCount." payments. Encountered ".sizeof($errors)." errors.");

		    
		}
		

	}
	
	/*
	
	function importChargeXML() {
		
		echo "<pre>";
		
	//	$this->load->model('invoices_model');
		
		$this->output->enable_profiler(FALSE);
		$chgCount = 0;
		echo "Beginning import script.\n";
		
		$errors = array();
		
		$outputDir = $this->config->item('ftpPath')."Output/";
		
		if ($handle = opendir($outputDir)) {
		
		    while (false !== ($file = readdir($handle))) {

		        if ((substr($file,strlen($file)-4) == '.xml') && (substr($file,0,15) == "NavisionCharges")) { // only get xml files.
		        
		        	echo "Beginning import of file " . $file . "\n";
		        
		        	if ($xml = simplexml_load_file($outputDir . $file)) {
		        							
					
						foreach ($xml->Charge as $index=>$c) {
							
							$chargeErrors = sizeof($errors);
							$chargeCriticalErrors = 0;
							
							if (isset($c->NavisionChargeID)) {
							
								echo "\n\nBeginning import of Charge ID " . $c->NavisionChargeID . "\n";
								
								print_r($c);
								
									// confirm that we haven't already imported this payment
			
							
								
									// confirm that we have all nessa data to import payment.
	
									if (isset($c->ChargeType))
									
										$chargeType = $c->ChargeType;
									
									} else {
									
										$errors[] = "Charge for NV Chargeid #" . $c->NavisionChargeID . " in file ". $file ." has no charge type.\n";
										$paymentCriticalErrors++;
									
									}
									
									if (isset($c->PropertyCode) && $this->properties_model->checkNVPropertyCode($c->PropertyCode)) {
									
										$payDate = strtotime($p->PaymentDate);
									
									} else {
									
										$errors[] = "Could not parse Payment Date for Invoice #" . $p->Id . " in file ". $file .".\n";
										$paymentCriticalErrors++;
									
									}
										
									if (isset($p->PaymentDate) && strtotime($p->PaymentDate)) {
									
										$payDate = strtotime($p->PaymentDate);
									
									} else {
									
										$errors[] = "Could not parse Payment Date for Invoice #" . $p->Id . " in file ". $file .".\n";
										$paymentCriticalErrors++;
									
									}
									
									
									if (isset($p->PaymentAmount)) {
									
										$payAmount = $p->PaymentAmount;
									
									} else {
									
										$errors[] = "Invoice #" . $p->Id . " in file ". $file ." has no payment amount.\n";
										$paymentCriticalErrors++;
										
									}
									
									
									if ($payAmount != $invoice->total) {
									
										$errors[] = "Payment amount and Invoice total do not match on ID ". $p->Id . " in file ". $file .".\n";
									
									}
									
									if ($paymentCriticalErrors == 0) { // no critical errors on this payment, import.
									
										$sql = "UPDATE opm_invoices
												SET paymentdate = ".$this->db->escape($payDate).",
													checknumber = ".$this->db->escape($checkNumber).",
													statusid = ".$statusPaid.",
													lastmodified = ".mktime()."
													WHERE id = " . $this->db->escape($invoice->id);
													
										
										if (@$this->db->query($sql)) {
									
											if (sizeof($errors) == $paymentErrors) { // no errors on this payment.
												
												echo "Ch for Invoice ID " . $p->NavisionChargeID . " imported without errors.\n";
											
											} else {
											
												echo "Payment for Invoice ID " . $p->NavisionChargeID . " encountered errors.\n";
	
											}
											
										} else {
										
											$errors[] = "IMPORT SQL FAILED for payment ID " . $p->NavisionChargeID . ", PAYMENT SKIPPED.";
										
										}
									
									} else {
									
										echo "Payment for Invoice ID " . $c->NavisionChargeID . " encountered critical errors. PAYMENT SKIPPED!\n";
									
									}
									
									$chgCount++;
								
								
									
							} else {
							
								$errors[] = "Charge # " . $index . " in file " . $file . " has no Navision Charge ID. PAYMENT SKIPPED!\n";
							
							}

						}
			        
			        } else {
				
						$errors[] = "Could not parse XML in file " . $file . " \n.";
					
					}
			        
				} 
		    
		    }
		    
		    if ($errors) {
		    	
		    	$errTxt = "\nThe following errors were encountered during the import:\n";
		    	
		    	foreach ($errors as $err) {
		    	
		    		$errTxt .= $err;
		    	
		    	}
		    	
		    	echo $errTxt;
		    	
		    	// email administrators
		    	
		    	mail("tim@studio211.us","ERRORS ENCOUNTERED DURING PAYMENT IMPORT!",$errTxt );
		    	
		    }
		    
		
		    closedir($handle);
		    
		    die("\n\nPayment import successfully completed on " . date('l jS \of F Y h:i:s A') . ". Imported ".$chgCount." payments. Encountered ".sizeof($errors)." errors.");

		    
		}
		

	}
	
	*/
    
    /*function purgeFailedUploads() {
    	
    	// config
    	
    	$mfDir = $this->config->item('fileUploadPath') . "masterfiles/";
    	$sepDir = $this->config->item('fileUploadPath') . "separations/";
    	
    	// purge masterfiles
    	
    	$sql = "SELECT fileid FROM opm_masterfiles WHERE confirmed = 0";
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $r) {
    		
    		$filePath = $mfDir . $r->fileid;
    	
    		if (file_exists($filePath)) {
    		
    			//unlink($filePath);
    			echo "Deleted " .$filePath . " <br>";	

    		} else {
    		
    			echo $filePath . " does not exist. <br>";
    		
    		}
    		
    		$sql = "DELETE FROM opm_masterfiles WHERE fileid = " . $this->db->escape($r->fileid);
    		//echo $sql;
    		$this->db->query($sql);
    	
    	}
    	
    	// purge separations
    	
    	$sql = "SELECT fileid FROM opm_separations WHERE confirmed = 0";
    	$query = $this->db->query($sql);
    	
    	foreach ($query->result() as $r) {
    		
    		$filePath = $sepDir . $r->fileid;
    	
    		if (file_exists($filePath)) {
    		
    			unlink($filePath);
    			echo "Deleted " .$filePath . " <br>";	

    		} else {
    		
    			echo $filePath . " does not exist. <br>";
    		
    		}
    		
    		$sql = "DELETE FROM opm_separations WHERE fileid = " . $this->db->escape($r->fileid);
    		//echo $sql;
    		$this->db->query($sql);
    	
    	}
    	
    
    }*/


}