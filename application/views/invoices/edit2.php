
<? if ($mode != 'print') { ?>

	<script language="javascript">
	
		// below variable controls display of onunload message. can be set false for 
		showWarningMessage = true;
	
		window.onload = function(){
	      
			Shadowbox.init({
			
				skipSetup: true
			
			});
			
			<? if ($mode == 'add') { ?>
			
				initInvoice();
			
			<? } ?>
			
			<? if (isset($addProductID)) { // we are adding a product?>
			
				contentUrl = base_url + 'invoices/addEditCharge/<?=  $invoice->id ?>/0/<?=  $addProductID ?>';
				openShadowbox(contentUrl,550,600);
			
			<? } ?>
			      	      
		};
		
		function invoiceTooltip(target_items){
			
			 $(target_items).each(function(i){
					
				var my_tooltip = $("#invoiceChannelSelector");
		
				$(this).mouseover(function(){
						
						// set the value of channelChargeID, so it can be set via ajax
						
						$('#channelChargeID').val($(this).attr('id'));
						
						// get the position of the trigger
						
						var pos = $(this).offset();  
						var width = $(this).width();
						
						// hide, move, then fade in the tooltip in the right position
						
						my_tooltip.css( { "display" : "none" } ).css( { "left": (pos.left + width) + "px", "top":pos.top + "px" } ).fadeIn(100);
				
				});
				
				my_tooltip.mouseleave(function(){
				
					my_tooltip.fadeOut(400);
				
				});
			
			
			});
		
		}
		
		function hideAllDetailNotes() {
		
			$("div.detailNoteTooltip").each(function(i){
		
				$(this).fadeOut(200);
		
			});
		
		}
		
		function detailNotesTooltip(target_items){
			
			 $(target_items).each(function(i){
			 
			 	detailID = $(this).attr('id').substr(12);
				var notesTooltip = $("#notesTooltip_" + detailID);

				$(this).mouseover(function(){
						
						hideAllDetailNotes();
						
						detailID = $(this).attr('id').substr(12);
						var notesTooltip = $("#notesTooltip_" + detailID);

						// get the position of the trigger
						
						var pos = $(this).offset();  
						var width = $(this).width();
						
						// hide, move, then fade in the tooltip in the right position
						
						notesTooltip.css( { "display" : "none" } ).css( { "left": (pos.left + width) + "px", "top":pos.top + "px" } ).fadeIn(200);
				
				});
				
				notesTooltip.mouseleave(function(){
					
					// WE MUST HIDE ALL NOTES!
					hideAllDetailNotes();
					//notesTooltip.fadeOut(400);
					
				});
				
				/*my_tooltip.mouseleave(function(){
				
					my_tooltip.fadeOut(400);
				
				});*/
			
			
			});
		
		}
		
		
		function assignChannel(channelcode,all) {
		 
			var chargeid = $('#channelChargeID').val();
			
			$.post('<?= base_url(); ?>invoices/ajaxAssignChannelCode/', { chargeid: chargeid, channelcode: channelcode, all: all }, function(data) {
				
				//alert(data);
				
				if (data != 'error' && data != 'noharley') {
				
					$("#invoiceChannelSelector").css( { "display" : "none" } );
					
					if (!all) {
					
						$('#'+chargeid).html(data);
					
					} else {
						
						reloadInvoice();
						//alert("changing all codezz!");
						
					}
				
				} else if(data == 'noharley') {
				
					alert("Harley Channel Code cannot be assigned to non-Harley product.");
				
				} else {
				
					alert("Channel Code could not be assigned. Please contact OPM tech support.");
				
				}
		
			});
			
			//alert("assigning channelcode:"+channelcode+" to chargeid:"+chargeid);
		
		}
		
		
		function openShadowbox($url,$width,$height) {
		
			Shadowbox.open({
				height:     $height,
		        width:      $width,
		        content:    contentUrl,
		        player:     "iframe"
		        
		    });
		
		}
		
		function checkForm() {
		
			showWarningMessage = false;
		
		}
		
		<? if (isset($invoice->id)) { ?>
		
		
			<? if ($invoice->statusid == $this->config->item("invStatusInProgress")) { ?>
		
				window.onbeforeunload = function() {
			      
			      	if (showWarningMessage)
						alert("Navigating away from this tab will save your invoice as a draft that remains editable!");
					      	      
				};
			
			
			<? } ?>
		
		
			function reloadNotes() {
				
				$('#notes').load('<?= base_url(); ?>ajax/getInvoiceNotes/<?=  $invoice->id ?>');
				
			}
			
			
			function addNote() {
		
				contentUrl = base_url + 'invoices/createSaveNote/<?=  $invoice->id ?>';
				openShadowbox(contentUrl,500,260);	
			
			}
			
			function addLineItemNote(chargeID) {
		
				contentUrl = base_url + 'invoices/createSaveLINote/' + chargeID;
				openShadowbox(contentUrl,500,260);	
			
			}
			
			function addProduct() {
		
				contentUrl = base_url + 'invoices/addProduct/<?=  $invoice->id ?>';
				openShadowbox(contentUrl,550,600);	
			
			}
			
			/*function addProduct2() {

				contentUrl = base_url + 'invoices/addProduct2/<?= $invoice->id; ?>';
				openShadowbox(contentUrl, 760, 360);
			
			}*/
			
			function editCharge(chargeID,opm_productid) {
		
				
		
				contentUrl = base_url + 'invoices/addEditCharge/<?=  $invoice->id ?>/' + chargeID + "/" + opm_productid;
				openShadowbox(contentUrl,550,600);	
			
			}
			
			function reloadInvoice() {
				
				$('#invItems').load('<?= base_url(); ?>ajax/getInvoiceContents/<?=  $invoice->id ?>/<?=$mode?>');
				
			}
			
			function executeInvAction(invoiceDetailID,action,opm_productid) {
			
				//alert("invdetailid:"+invoiceDetailID+"\naction:"+action);
				
				if (action == 'addCharge') {
				
					contentUrl = base_url + 'invoices/addEditCharge/<?=  $invoice->id ?>/0/' + opm_productid;
					openShadowbox(contentUrl,550,600);
				
				} else if (action == 'removeProduct') {
				
					if (confirm("Are you sure you want to remove this product?")) {
					
						location.href = base_url + 'invoices/removeProduct/<?=  $invoice->id ?>/' + opm_productid;
					
					}
				
				}
			
			}
			
			function editInvoice() {
	
				contentUrl = base_url + 'invoices/editInvoice/<?= $invoice->id ?>';
				openShadowbox(contentUrl,550,600);	
			
			}
			
			function viewHistory(id) {
	
				contentUrl = base_url + 'ajax/invoiceHistoryView/<?=  $invoice->id ?>';
				
				Shadowbox.open({
					height:     350,
			        width:      800,
			        content:    contentUrl,
			        player:     "iframe"
			        
			    });
			
			}
			
			function printInvoice() {
			
				location.href = base_url + 'invoices/showPrintable/' + <?=  $invoice->id ?>;
			
			}
		
		<? } ?>
		
		function initInvoice() {
	
			contentUrl = base_url + 'invoices/initInvoice/';
			openShadowbox(contentUrl,550,600);	
		
		}
		
		function confirmDeleteInvoice() {
		
			if (confirm("Are you sure you want to delete this invoice?")) {
			
				document.invoiceform.deleteMe.value = 1;
				document.invoiceform.submit();
			
			} else {
			
				return false;
			
			}
				
			
		
		}
		
		
		 $(document).ready(function() {
	      
	      	      	
	     	 <? if ($mode != 'add' && $mode != 'print') { ?>
	      	
				reloadInvoice();
				reloadNotes();
	      	
	      	<? } ?>
	      	 
	
	      });
	
	</script>
	
<? } else { ?>

	<script language="javascript">
	
		$(document).ready(function() {
		
			//setTimeout("window.print()",1500);
		
		});
	
	</script>


<? } ?>

<?

	// PROPERLY DISPLAY TAX ID…
	
	if (isset($invoice->id) && $invoice->taxid) {
	
		if ((checkPerms('can_view_taxid') || ($this->userinfo->userid == $invoice->userid)) && ($mode != 'print')) {
		
			$taxid = $invoice->taxid;
		
		} else {
			
			$taxid = 'XXX-XX-' . substr($invoice->taxid,7) ;
		
		}
	
	} else { 
	
		$taxid = '';
	
	}

?>

<form name="invoiceform" id="invoiceform" action="<?= base_url(); ?>invoices/save" method="post" enctype="Multipart/Form-Data" onsubmit="return checkForm();">

<input type="hidden" name="deleteMe" value="0" />

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	
	<tr>
		
		<td>
		
			<? if (isset($invoice->id)) { ?>
				
				<input type="hidden" name="invoiceid" value="<?= $invoice->id ?>" />
				<div style="float:right;"  <? if ($mode != 'print' && $invoice->canEdit == true) { ?> class="invModule" onclick="editInvoice();" <? } else { ?> class="invModuleNoEdit" <? } ?>>
				
					<? if ($mode == 'print') { ?>
						ID: #<?= $invoice->id; ?><br />
					<? } ?>
					CLIENT INVOICE #: <? if (isset($invoice->referencenumber)) echo $invoice->referencenumber; ?><br />
					TAX ID: <?= $taxid ?> <br />
					<? if ($invoice->vatnumber) { ?> VAT #: <?= $invoice->vatnumber ?> <br /> <? } ?>
					<? if ($invoice->submitdate) { ?>
						INVOICE DATE: <?= opmDate($invoice->submitdate) ?>
					<? } else { ?>
						CREATED: <?= opmDate($invoice->createdate) ?>
					<? } ?>
				
				</div>
		
			<? } ?>
			
			<!--<img src="<?= base_url(); ?>resources/images/inv_placeholder.gif" alt="inv_placeholder" width="243" height="62" />-->
			
			<? if ($mode == 'edit' || $mode == 'print') { ?>
			
				<? if ($invoice->invoice_imagepath) { ?>
				
					<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/<?= $invoice->id ?>" alt="" width="450" />
				
				<? } else if (isset($invoice->username)) { ?>
				
					<img src="<?= base_url(); ?>resources/autoInvoiceImage.php?text=<?= $invoice->username ?>" alt="inv_placeholder" />
		
				<? }  ?>

			<? } ?>
			
			<br />
			
			<? if (is_object($invoice)) { ?>
			
				<div style="margin-top:0px; width:220px; margin-bottom:30px;" <? if ($mode != 'print' && $invoice->canEdit == true) { ?> class="invModule" onclick="editInvoice();" <? } else { ?> class="invModuleNoEdit" <? } ?>>
					<? if (isset( $invoice->username)) { ?>
						
						<?= $invoice->staddress ?><br />
						<?= ($invoice->staddress2 ? $invoice->staddress2 . "<br />" : null) ?>
						<?= $invoice->city ?>, <?= $invoice->state ?>, <?= $invoice->zip ?>
					
				
					<? } ?> 
				</div>
			
			
			
				<? if ($mode != 'print' && $invoice->canEdit == true) { ?><div class="invAddItemArea" ><a href="#" onclick="addProduct(); return false;" class="invAddItem">ADD ITEM</a></div><? } ?>
				<div id="invoiceLIArea">
					
					<div id="invItems"><? if (isset($invoiceContents)) echo $invoiceContents; ?></div>
					
					
				
				</div>
				
				<br /><br />
				
				<? if ($mode != 'print') { ?><div class="invAddItemArea" style="margin-top:-7px;"><a href="#" onclick="addNote(); return false;" class="invAddItem">ADD NOTE</a></div><? } ?>
			
			
				<div id="invoiceNoteArea">
				
					<div id="notes">
						<? if (isset($invoiceNotes)) echo $invoiceNotes; ?>
					</div>
				
				
				</div>
				
				<div style="" class="invBillTo">Bill To: <?= $invoice->billto ?><br />Attention: <?= $invoice->attention ?><br />Terms: NET 30 DAYS</div>
			
			
			<? } ?>
			
		</td>
	
	</tr>
	
</table>

<br />

<? if (isset($invoice->statusid) && $invoice->statusid == $this->config->item('invStatusInProgress') && $mode != 'print') { ?>

	<div style="text-align:right; margin-right:35px;"><input type="submit" name="submitBtn" value="Submit Invoice" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'"></div>

<? } ?>

<!--

<? if (isset($invoice->statusid) && $invoice->statusid != $this->config->item('invStatusSentToNavision') && $invoice->statusid != $this->config->item('invStatusDeleted') && $invoice->statusid != $this->config->item('invStatusPaid') && $mode != 'print') { ?>

	<br /><div style="text-align:right; margin-right:35px;"><input type="submit" name="deleteBtn" value="Delete Invoice" class="invoiceBtn" onclick="return confirmDeleteInvoice();" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'"></div>
	
	<br />

<? } ?>

<? if ($mode != 'print') { ?>

	<div style="text-align:right; margin-right:35px;"><input type="button" name="printBtn" value="Print Invoice" class="invoiceBtn" onclick="return printInvoice();" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'"></div>
	
	<br />

<? } ?>
-->
</form>

<? if ($mode != 'add') { ?>

	<form name="invoiceActionsForm" method="post" action="<?= base_url() ?>invoices/approveForward">
		
		<input type="hidden" name="invoiceid" value="<?= $invoice->id ?>" />
	
	<? if ((checkPerms('can_use_pre_approve_forward_button')) && ($invoice->statusid == $this->config->item('invStatusSubmitted')) && ($mode != 'print')) { ?>
		<!--<div style="text-align:right; margin-right:35px;">
		
			<input type="submit" name="preapproveBtn" value="Pre-Approve Invoice and Forward To:" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
			<select name="forwardToID1" class="preApproveSelect">
				
				<option value="0">Pick User...</option>
								
				<? foreach ($productManagers->result() as $u) { ?>
				
					<option value="<?= $u->userid ?>"><?= $u->username ?></option>
				
				<? } ?>
			
			</select>
			
			<input type="submit" name="preapproveBtn" value="Go &gt;&gt;" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
		</div>-->
		
	<? } ?>
	
	<br />
	
	<? if ((checkPerms('can_use_approve_forward_button')) && ($invoice->statusid == $this->config->item('invStatusSubmitted') || $invoice->statusid == $this->config->item('invStatusPreapproved')) && ($mode != 'print')) { ?>
		<div style="text-align:right; margin-right:35px;">
		
			<!--<input type="submit" name="approveBtn" value="Approve Invoice and Forward To:" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
			<select name="forwardToID2" class="preApproveSelect">
				
				<option value="0">Pick User...</option>
								
				<? foreach ($productManagers->result() as $u) { ?>
				
					<option value="<?= $u->userid ?>"><?= $u->username ?></option>
				
				<? } ?>
			
			</select>-->
			
			<input type="hidden" name="forwardToID2" value="<?= $invoice->ownerid ?>" />
			
			<input type="submit" name="approveBtn" value="I Approve This Invoice" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
		</div>
		
		
	<? } ?>
	
	<? if ((checkPerms('can_use_invoice_forward_button')) && ($invoice->statusid == $this->config->item('invStatusSubmitted') || $invoice->statusid == $this->config->item('invStatusPreapproved')) && ($mode != 'print')) { ?>
		
		<br />
		
		<div style="text-align:right; margin-right:35px;">
		
			<input type="submit" name="forwardBtn" value="Forward To:" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
			<select name="forwardToID3" class="preApproveSelect">
				
				<option value="0">Pick User...</option>
								
				<? foreach ($productManagers->result() as $u) { ?>
				
					<option value="<?= $u->userid ?>"><?= $u->username ?></option>
				
				<? } ?>
			
			</select>
			
			<input type="submit" name="forwardBtn" value="Go &gt;&gt;" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
		</div>
		
		
	<? } ?>
	
	<? if ((checkPerms('can_resubmit_invoices')) && ($invoice->statusid == $this->config->item('invStatusSentToNavision')) && ($mode != 'print')) { ?>
		
		<br />
		
		<div style="text-align:right; margin-right:35px;">
		
			<input type="submit" name="resubmitBtn" value="Set Status Back To Approved" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
		</div>
		
		
	<? } ?>
	
	<? if ((checkPerms('can_reverseapprove_invoices')) && ($invoice->statusid == $this->config->item('invStatusApproved')) && ($mode != 'print')) { ?>
		
		<br />
		
		<div style="text-align:right; margin-right:35px;">
		
			<input type="submit" name="reverseApproveBtn" value="Reverse Invoice Approval" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'">
			
		</div>
		
		
	<? } ?>
	
	</form>

<? } ?>

<? if ($mode != 'print') { ?>

	<div class="invoiceChannelSelector" id="invoiceChannelSelector">
		
		<form name="chnlSelectForm">
		
			<input type="hidden" id="channelChargeID" value="" />
			
			<div class="invoiceChnlOptions">
				
				<? foreach ($channels->result() as $ch) { ?>
				
					<a href="#" onclick="assignChannel('<?= $ch->channelcode ?>',0); return false;" class="invoiceChannels"><?= $ch->channelcode ?> - <?= $ch->channel ?> </a> <a href="#" onclick="assignChannel('<?= $ch->channelcode ?>',1); return false;" class="invoiceChannels" style="color:red; font-size:7pt;">chg all</a><br />
				
				<? } ?>
	
			
			</div>
		
		</form>
	
	</div>

<? } ?>

<pre>
	<? // print_r($invoice); ?>
</pre>



	