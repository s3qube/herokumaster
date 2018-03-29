<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
	
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery-1.3.2.min.js"></script>
			
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery.validate.pack.js"></script>

	
		<script language = "javascript">
		
			<? if (isset($result)) { ?>
				
					window.parent.showWarningMessage = false;
					window.parent.location.href = "<?= base_url() ?>invoices/edit/<?= $id ?>";
					var timerID = setTimeout("window.parent.Shadowbox.close();", 600);
			
			<? } ?>
			
			$(document).ready(function () {

				$("#invForm").validate();
				
				jQuery.validator.addMethod("uniqueRef", function(value, element) {
					
					var response;
					
					if ($("#userid").val()) {
					
						$.ajax({
						
							type: "POST",
							url: "<?= base_url() ?>invoices/checkRef/",
							data:{ referencenum: value, userid: $("#userid").val() },
							async:false,
						
							success:function(data){
						
								response = data;
					    
					        }
					    
					    });
					    
					} else {
						
						return true;
						
					}
				    
				   // alert(response);
				    
				   // return false;
				    
				    if(response == '0') {
				    
				        return true;
				    
				    } else if(response == '1') {
					    
					    return false;
				
					} else if(response == 'logout') {
						
						return false;
					}
				
				}, "Number has been used already.");
				
				
				<? if (isset($invoice)) { ?>

					origOwnerID = "<?= $invoice->ownerid ?>";
					origStatusID = "<?= $invoice->statusid ?>";
				
				<? } ?>
			     
			
			});

			function getProducts(propertyid) {
				
				$('#productSelect').load('<?= base_url(); ?>invoices/ajaxGetProducts/' + propertyid);
		
			}
			
			
			function checkForm() {
			
				//document.prodForm.opm_productid.value = $("#opm_prodid").val();
				//return true;
			
			}
			
			
			
			function forwardInvoice() {
		
				<? if ($mode != 'add') { ?>
		
					newOwnerID = $("#ownerid").attr("value");
				
					newOwnerName = $("#ownerid option[value='" + newOwnerID + "']").text();
					
					//alert($("#ownerid").attr("value"));
					
					if (newOwnerID != origOwnerID) {
					
						if (confirm("Do you want to forward this invoice to " + newOwnerName + "?")) {
						
							$('#forward').val("1");
							//alert(document.invoiceform);//.submit();	
							document.forms["invForm"].submit();
						}
					
					}
				
				<? } ?>
			
			}
				
			function chgInvoiceStatus() {
			
				<? if ($mode != 'add') { ?>
		
					newStatusID = $("#statusid").attr("value");
					newStatus = $("#statusid option[value='" + newStatusID + "']").text();
					
					
					if (newStatusID != origStatusID) {
					
						if (confirm("Do you want to change this invoice to '" + newStatus + "'?")) {
						
							$('#changeStatus').val("1");
							//alert(document.invoiceform);//.submit();	
							document.forms["invForm"].submit();
							
						}
					
					}
				
				<? } ?>
			
			}
			
			
			
			function updateBillingInfo() {
			
				url = '<?= base_url(); ?>ajax/getInvoiceBillingInfo/' + $("#userid").attr("value");;
			
				$('#billingInfo').load(url);

			
			}
			
		</script>
		
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_invoicepopup_styles.css">
	</head>
	<body>
		
		<? if (!isset($result)) { ?>
		
		
			<div id="container">
				
				<? if ($mode == 'add') { ?>
				
					<form name="invForm" id="invForm" method="POST" action="<?= base_url(); ?>invoices/initInvoice/" enctype="Multipart/Form-Data">
				
				<? } else { ?>
				
					<form name="invForm" id="invForm" method="POST" action="<?= base_url(); ?>invoices/editInvoice/<?= $invoice->id ?>" enctype="Multipart/Form-Data">
									
				<? } ?>
					
					
					<? if (checkPerms('can_change_invoice_user') && $mode == 'add') { ?>
					
						<h3 class="invoicePopLabel">User:</h3>
						
						<select name="userid" id="userid" class="invoicePopField required" onchange="updateBillingInfo();">
				
							<option value="">Please Select...</option>
							
							<? foreach ($designers->result() as $u) { ?>
							
								<option value="<?= $u->userid ?>" <?= (isset($invoice->userid) && ($invoice->userid == $u->userid)) ? "SELECTED" : null ?>><?= $u->username ?></option>
							
							<? } ?>
						
						</select>
					
					<? } else { ?>
					
						
						<input type="hidden" name="userid" value="<?= $userid ?>" />
					
					
					<? } ?>
					
					
				
					
					
					<h3 class="invoicePopLabel">Reference Number:</h3>
					
					<input type="text" name="referencenumber" class="invoicePopField required uniqueRef" maxlength="17" style="width:200px;" value="<?= (isset($invoice->referencenumber)) ? $invoice->referencenumber : null ?>" />
				
				<!-- BILL TO COMPANY --->
				
				
				<h3 class="invoicePopLabel">Bill To:</h3>
					
				<select name="companyid" id="companyid" class="invoicePopField required">
				
					<option value="">Please Select...</option>
					
					<? foreach ($companies->result() as $c) { ?>
					
						<option value="<?= $c->id ?>" <?= (isset($invoice->companyid) && ($invoice->companyid == $c->id)) ? "SELECTED" : null ?>><?= $c->name ?></option>
					
					<? } ?>
				
				</select>
				
				<br />
				
				
				
				<!-- / BILL TO COMPANY -->
				
				
					
				<h3 class="invoicePopLabel"><?= ($mode == 'add') ? "Submit To:" : "Owner:" ?></h3>
					
				<input type="hidden" id="forward" name="forward" value="0" />
				<select name="ownerid" id="ownerid" class="invoicePopField required" onchange="forwardInvoice();">
				
					<option value="">Please Select...</option>
					
					<? foreach ($productManagers->result() as $u) { ?>
					
						<option value="<?= $u->userid ?>" <?= (isset($invoice->ownerid) && ($invoice->ownerid == $u->userid)) ? "SELECTED" : null ?>><?= $u->username ?></option>
					
					<? } ?>
				
				</select>
				
				<br />
			
				<h3 class="invoicePopLabel">CC:</h3>
					
					<div>
						
						<? foreach ($productManagersCC->result() as $u) { ?>
								
							<div id="div_user_id_<?= $u->userid ?>" class="selectItem <? if ($u->isassigned) echo "selectItemOn" ?>">
							
								<input class="selectItemChkbox" type="checkbox" id="user_id_<?= $u->userid ?>" name="ccUserIDs[<?= $u->userid ?>]" <? if ($u->isassigned) echo "CHECKED"; ?> />
								<label for="user_id_<?= $u->userid ?>"><?= $u->username ?></label> 
							
							</div>
					
						<? } ?>
					
					</div>
					
					<br />
					
					
				
					<div id="billingInfo">
					
						<? if (!checkPerms('can_change_invoice_user') || ($mode == 'edit')) { ?>
				
							<h3 class="invoicePopLabel">Verify Billing Info / Tax ID / VAT:</h3>
								
								<? if ($mode == 'add') { ?>
								
									<?= $user->staddress ?><br />
									<?= ($user->staddress2 ? $user->staddress2 . "<br />" : null) ?>
									<?= $user->city ?>, <?= $user->state ?> <?= $user->zip ?><br />
								
								<? } else { ?>
								
									<?= $user->staddress ?><br />
									<?= ($user->staddress2 ? $user->staddress2 . "<br />" : null) ?>
									<?= $user->city ?>, <?= $user->state ?> <?= $user->zip ?><br />
								
								<? } ?>
								
							<h3 class="invoicePopLabel">Tax ID:</h3>
							
								<? if ($mode == 'add') { ?>
								
									<?= $user->taxid ?>
								
								<? } else { ?>
								
									<textarea name="taxid"><?= $invoice->taxid ?></textarea>
								
								<? } ?>
							
							<br />
							
							<h3 class="invoicePopLabel">Tax ID:</h3>
							
								<? if ($mode == 'add') { ?>
								
									<?= $user->vatnumber ?>
								
								<? } else { ?>
								
									<textarea name="vatnumber"><?= $invoice->vatnumber ?></textarea>
								
								<? } ?>
							
							<br />
								
							<h3 class="invoicePopLabel">Invoice Image:</h3>
							
								<? /*if (isset($invoice) && isset($invoice->invoice_imagepath)) { ?>
									
									<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/<?= $invoice->id ?>" alt="" />
									<input type="hidden" name="invoice_imagepath" value="<?= $user->invoiceimage_path ?>" />
									<br />
								
								<? } else if ($user->invoiceimage_path) { ?>
								
									<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/0/<?= $user->userid ?>" alt="" />
									<input type="hidden" name="invoice_imagepath" value="<?= $user->invoiceimage_path ?>" />
									<br />
									
								<? } */?>
								
								<? if ($user->invoiceimage_path) { ?>
								
									<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/0/<?= $user->userid ?>" alt="" />					
								
								<? } else { ?>
								
									No Image Uploaded.
								
								<? } ?>
									
								<!--Upload Image Below (jpg or gif, maximum 400x150 pixels).<br />
								<input type="file" name="invoiceImage" />-->
								
								<br />
								
								Is all the above info correct? If not, <a href="#" onclick="window.parent.location.href='<?= base_url(); ?>invoices/editInfo'; return false">click here to update your info</a>.
					 					
						<? } ?>
						
					</div>
				
					
					<? if ($mode == 'edit') { ?>
				
						<? if (checkPerms('can_change_invoice_status')) { ?>
				
							<h3 class="invoicePopLabel">Invoice Status:</h3>
									
							<input type="hidden" id="changeStatus" name="changeStatus" value="0" />
		
							<select name="statusid" id="statusid" class="invoicePopField" <?= (checkPerms('can_change_invoice_status') ? 'onchange="chgInvoiceStatus();"' : null) ?>>
							
								<option value="0">Please Select...</option>
								
								<? foreach ($statuses as $id => $status) { ?>
								
									<option value="<?= $id ?>" <? if ($invoice->statusid == $id) echo "SELECTED" ; ?>><?= $status ?></option>
								
								<? } ?>
							
							</select>
							
							<br /><br />
						
						<? } else { ?>
						
							<input type="hidden" name="statusid" value="<?= $invoice->statusid ?>" />
						
						<? } ?>
					
					
						<div class="invoicePopBtn" id="selBtn"><input type="submit" name="saveInvoice" class="invoicePopBtn" value="Save Invoice" /></div>
				
				
					<? } else { ?>
					
						<div class="invoicePopBtn" id="selBtn"><input type="submit" name="createInvoice" class="invoicePopBtn" value="Create Invoice" /></div>

					<? } ?>
				
				</form>
			</div>
			
			<br />
			
		<? } else { ?>
		
			
			<br /><br /><br />
				
			<div style="text-align:center;"><?= $resultText ?></div>
				
		
		<? } ?>
		
	
	</body>
</html>