
<? //print_r($product) ?>

<script language="javascript">

	var options, a;
	
	jQuery(function(){
  	
  		options = { serviceUrl:'<?= base_url(); ?>ajax/invoiceQuery',minChars:2,deferRequestBy: 300 };
  		a = $('#query').autocomplete(options);
	
	});
	
	
	$(function(){
    	$("#addButton").click(function(){
	        $.post("<?= base_url(); ?>ajax/addInvoiceItem",{ searchProducts: $("#query").val(), invoiceid:<?=  $invoice->id ?> },
	        function(data){
	        	
	        	if (data == 'error')
	        		alert("there was a problem adding that product");
	        	else
	        		$('#itemList').append(data);
	        		
	        	
	            /*if(data.email_check == 'invalid'){
	 
	                 $("#message_post").html("<div class='errorMessage'>Sorry " + data.name + ", " + data.email + " is NOT a valid e-mail address. Try again.</div>");
	            } else {
	                $("#message_post").html("<div class='successMessage'>" + data.email + " is a valid e-mail address. Thank you, " + data.name + ".</div>");
	            }*/
	        });
	 
	        return false;
	 
	    });
	    
	    $("#addNoteButton").click(function(){
	        $.post("<?= base_url(); ?>ajax/addInvoiceNote",{ note: $("#note").val(), invoiceid:<?=  $invoice->id ?> },
	        function(data){
	        	
	        	if (data == 'error') { 
	        		alert("there was a problem adding that note");
	        	} else {
	        		reloadNotes();
	        		$("#noteField").fadeOut('slow');

	        	}
	        	
	            /*if(data.email_check == 'invalid'){
	 
	                 $("#message_post").html("<div class='errorMessage'>Sorry " + data.name + ", " + data.email + " is NOT a valid e-mail address. Try again.</div>");
	            } else {
	                $("#message_post").html("<div class='successMessage'>" + data.email + " is a valid e-mail address. Thank you, " + data.name + ".</div>");
	            }*/
	        });
	 
	        return false;
	 
	    });
	    
	    
	});
	
	// load invoice contents onload
	
		function reloadInvoice() {
		
			$('#itemList').load('<?= base_url(); ?>ajax/getInvoiceContents/<?=  $invoice->id ?>');
		
		}
		
		function reloadNotes() {
		
			$('#notes').load('<?= base_url(); ?>ajax/getInvoiceNotes/<?=  $invoice->id ?>');
		
		}
	
	   
      $(document).ready(function() {
      
         reloadInvoice();
         reloadNotes();
         getBillingInfo();
         
         origOwnerID = "<?= $invoice->ownerid ?>";
         origStatusID = "<?= $invoice->statusid ?>";
	
      });
      
	window.onload = function(){
      
		Shadowbox.init({
			skipSetup: true
		});
      	
      	// open a welcome message as soon as the window loads
	   /* Shadowbox.open({
	        content:    '<div id="welcome-msg">Welcome to my website!</div>',
	        player:     "html",
	        title:      "Welcome",
	        height:     350,
	        width:      350
	    });*/

      
	};

	function showHideNotes() {
		
		
		$("#noteField").fadeIn('slow');
	
	}

	function clearTextField(objField) {
	
		if (objField.value == 'Type Product ID, Property or Product Name Here...') {
		
			objField.style.color = '#000000';
			objField.value = '';
		
		}
	
	}
	
	function getTotals() {
	
		//$txt = $('.invoicePrice').serializeArray();
		//alert($txt);
	
		/*$('.invoicePrice').each(function(index,element) {
		
			alert($(this).attr("value"));
		
		});*/
		
	
	}
	
	function checkForm() {
	
		errors = 0;
	
		$('.invoicePrice').each(function(index,element) {
		
			if ($(this).attr("value") == 0) {
				alert("All line items must have a charge amount entered.");
				errors++;
				return false;
			}
		
		});
		
		if (errors > 0)
			return false;
		
		$('.invoiceChargeType').each(function(index,element) {
		
			if ($(this).attr("value") == 0) {
			
				alert("All line items must have a charge type selected.");
				errors++;
				return false;
				
			}
		
		});
		
		if (errors == 0)
			return true;
		else
			return false;
		
	
	}
	
	function getBillingInfo() {
		
		userid = $("#userid").val();
		
		if (userid > 0)
			$('#billingInfo').load('<?= base_url(); ?>ajax/getBillingInfo/' + userid);
	
	
	}
	
	function viewHistory(id) {

		contentUrl = base_url + 'ajax/invoiceHistoryView/<?=  $invoice->id ?>';

		/*Shadowbox.open({
			title:      '',
			type:       'iframe',
			content:    contentUrl,
			height:     540,
			width:     515
		});*/
		
		Shadowbox.open({
			height:     350,
	        width:      800,
	        content:    contentUrl,
	        player:     "iframe"
	        
	    });
	
	}
	
	function forwardInvoice() {
		
		newOwnerID = $("#ownerid").attr("value");
	
		newOwnerName = $("#ownerid option[value='" + newOwnerID + "']").text();
		
		//alert($("#ownerid").attr("value"));
		
		if (newOwnerID != origOwnerID) {
		
			if (confirm("Do you want to forward this invoice to " + newOwnerName + "?")) {
			
				$('#forward').val("1");
				//alert(document.invoiceform);//.submit();	
				document.forms["invoiceform"].submit();
			}
		
		}
		
		
	
	}
	
	function chgInvoiceStatus() {
		
		newStatusID = $("#statusid").attr("value");
		newStatus = $("#statusid option[value='" + newStatusID + "']").text();
		
		
		if (newStatusID != origStatusID) {
		
			if (confirm("Do you want to change this invoice to '" + newStatus + "'?")) {
			
				$('#changeStatus').val("1");
				//alert(document.invoiceform);//.submit();	
				document.forms["invoiceform"].submit();
				
				
				
			}
		
		}
	
	}
	
	

</script>

<form name="invoiceform" id="invoiceform" action="<?= base_url(); ?>invoices/save" method="post" enctype="Multipart/Form-Data" onsubmit="return checkForm();">
<input type="hidden" name="invoiceid" value="<?=  $invoice->id ?>" />

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<!--<h3 class="userField">Invoice ID : <?=  $invoice->id ?></h3>
				
				
				
				<? if ($locked) { ?>
					<input type="hidden" name="locked" value="1" />
				<? } ?>
				
				<br /><br />-->
			
			
			
				<h3 class="userField">User:</h3>
				
				<? if (isset($users)) { // We are an admin and can create invoices for others. List users. ?>
				
					<select name="userid" class="userField" id="userid" onchange="getBillingInfo();" <? if ($locked) echo "DISABLED"; ?> >
					
						<option value="0">Please Select...</option>
						
						<? foreach ($users->result() as $u) { ?>
						
							<option value="<?= $u->userid ?>" <? if ($invoice->userid == $u->userid) echo "SELECTED" ; ?>><?= $u->username ?> (<?= $u->usergroup ?>)</option>
						
						<? } ?>
					
					</select>
									
				<? } else { ?>
				
					<input type="text" name="invoiceid" value="<?=  $invoice->id ?>" class="userField" <? checkDisabled(); ?>/>
				
				<? } ?>		
				
				<br /><br />
				
				
				<h3 class="userField">Reference Number:</h3>
		
				<input type="text" name="referencenumber" value="<?= $invoice->referencenumber ?>" class="userField" <? if ($locked) echo "DISABLED"; ?> />
				
				
				
				<br /><br />
				
				
				
				<h3 class="userField"><?= ($invoice->statusid == 1 ? "Submit To:" : "Owner:" ) ?></h3>
				<input type="hidden" id="forward" name="forward" value="0" />
				<select name="ownerid" id="ownerid" class="userField" <?= ($invoice->statusid != 1 ? 'onchange="forwardInvoice();"' : null) ?> <? if ($invoice->statusid != 1 && !checkPerms('can_forward_invoices')) echo "DISABLED"; ?>>
				
					<option value="0">Please Select...</option>
					
					<? foreach ($productManagers->result() as $u) { ?>
					
						<option value="<?= $u->userid ?>" <? if ($invoice->ownerid == $u->userid) echo "SELECTED" ; ?>><?= $u->username ?></option>
					
					<? } ?>
				
				</select>
				
				<br /><br />
			
				<h3 class="userField">CC:</h3>
					
					<div class="selectField" style="height:100px;">
						
						<? foreach ($productManagersCC->result() as $u) { ?>
								
							<div id="div_user_id_<?= $u->userid ?>" class="selectItem <? if ($u->isassigned) echo "selectItemOn" ?>">
							
								<input class="selectItemChkbox" type="checkbox" id="user_id_<?= $u->userid ?>" name="ccUserIDs[<?= $u->userid ?>]" onChange="opm.changeOptionColorJQ('user_id',<?= $u->userid ?>);" <? if ($u->isassigned) echo "CHECKED"; ?> />
								<label for="user_id_<?= $u->userid ?>"><?= $u->username ?></label> 
							
							</div>
					
						<? } ?>
					
					</div>

				
				<br /><br />
	
			
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
				
				
				
				<table width="25%" cellpadding="0" cellspacing="0" border="0">
				
					<tr>
						<td>
							
							
				
							<h3 class="userField">Invoice Status:</h3>
							
							<input type="hidden" id="changeStatus" name="changeStatus" value="0" />

							<select name="statusid" id="statusid" class="userField" <?= (checkPerms('can_change_invoice_status') ? 'onchange="chgInvoiceStatus();"' : null) ?>>
							
								<option value="0">Please Select...</option>
								
								<? foreach ($statuses as $id => $status) { ?>
								
									<option value="<?= $id ?>" <? if ($invoice->statusid == $id) echo "SELECTED" ; ?>><?= $status ?></option>
								
								<? } ?>
							
							</select>
							
							<br /><br />
							
							<div id="billingInfo"></div>
							
							
						</td>
					</tr>
				
				</table>
				
				
				
				<!--<div id="prodSummaryImage"><div id="detailImageDiv"><img src="<?=base_url();?>/imageclass/view/<?=$product->default_imageid?>" width="350" height="350" border="0" id="detailImage"></div></div>-->
				
				
				
			</td>
		</tr>
	</table>

	<br /> <br />
	
	<div class="invoiceDiv"></div>
	
	<br />

	<!--<form name="addItemForm" id="addItemForm">-->

		<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
						
						<h3 class="userField">Add Line Item:</h3>
						
						<input type="text" id="query" name="searchProducts" value="Type Product ID, Property or Product Name Here..." onfocus="clearTextField(this);" style="width:600px; color:#696969;" class="userField" />
						
						<input type="button" style="background-color:#ffffff;color:#0000ef; height:25px;font-weight:bold;padding:4px;" value="add" id="addButton" />
						
						<br /><br />
						
					</td>		
				</tr>
		</table>
	
	<!--</form>-->
	
	<br /><br />


	<div id="itemList">
	
		
	
	</div>
	
	<div class="invoiceRow"> <!-- TOTALS -->

		<table border="0" class="invoiceLineItemTable" width="90%">
			<tr>
				<td></td>
				<td valign="top">
					<span class="invoiceProductName"></span>
					
					<div class="invoiceChargeRow"></div>
				</td>
				<td align="right"><div class="lineItemTotal">TOTAL: $<?= number_format($invoice->total,2) ?></div></td>
			</tr>
		</table>
	</div>
	
	<br />
	
	<div class="invoiceDiv"></div>
	
	<br />
	
		<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
						
						<h3 class="userField">Notes:</h3>
						
						<div style="text-align:right;"><a href="#" onclick="showHideNotes(); return false;">++ Add Note</a></div>
						
						<div id="noteField" style="display:none;">						
						<textarea id="note" name="note" style="width:500px; height:75px; color:#696969;" class="userField"></textarea>
						
						<input type="button" value="add" onclick="" id="addNoteButton" class="invoiceNoteBtn" />
						
						</div>

						
						<br /><br />
						
					</td>		
				</tr>
		</table>
	
	<br />
		
		<div id="notes">
		
		
		
		
		</div>

	<div style="text-align:right;">
		
		<? if ($invoice->statusid == 1) { ?>
		<input type="submit" name="update" value="Save" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">&nbsp;&nbsp;&nbsp;<br />
		<input type="submit" name="submitBtn" value="Submit Invoice" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">&nbsp;&nbsp;&nbsp;
		<? } ?>
		

		
	</div>


</form>


<br /><br /><br />


	