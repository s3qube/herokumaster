
<div id="invLineItem_<?= $i['opm_productid'] ?>" class="invLineItem" style="page-break-inside:avoid">
				
	<table border="0" cellpadding="0" cellspacing="0" width="740">
		<tr>
			<td valign="top" width="62">
				<? if ($mode == 'print') { ?>
					<img src="<?=base_url();?>imageclass/viewThumbnail/<?= $i['default_imageid'] ?>/496" class="invLIImg" id="invLIImg_<?= $i['opm_productid'] ?>" alt="invImgPlaceHolder" width="62" height="62" />
				<? } else { ?>
					<img src="<?=base_url();?>imageclass/viewThumbnail/<?= $i['default_imageid'] ?>/62" class="invLIImg" id="invLIImg_<?= $i['opm_productid'] ?>" alt="invImgPlaceHolder" width="62" height="62" />
				<? } ?>
			
			</td>
			<td valign="top" class="invLiProdText">
				<a href="<?= base_url();?>products/view/<?= $i['opm_productid'] ?>" target="_blank"><?= $i['productText'] ?> <?= ($i['propertycode'] == '9999' && checkPerms('view_invoice_9999')) ? "<span style='color:red;'>(9999)</span>" : null ?></a>
			
				<div class="liChargeDetail" id="liChargeDetail_<?= $i['opm_productid'] ?>">
				
					<table border="0" cellpadding="0" cellspacing="0" width="380">
					
						<? foreach ($i['charges'] as $c) { ?>
							
							<tr>
								<? if ($c['hours'] > 0) { ?>
								
									<td width="260"><? if ($mode != 'print' && $invoice->canEdit) { ?><a href="#" onclick="editCharge(<?= $c['id'] ?>,<?= $c['opm_productid'] ?>); return false;" class="invItemLink" title="Edit This Charge"><? } ?><?= $c['chargetype'] ?> - <?= $c['hours'] ?> hrs @ $<?= number_format($c['hourlyrate'],2) ?>/hr<? if ($mode != 'print' && $invoice->canEdit) { ?><span class="invoiceClickToEdit"> edit</span><? } ?></a></td>
								
								<? } else { ?>
								
									<? if ($c['chargetypeid'] == $this->config->item('invCTOther')) { ?>
								
										<td width="260"><? if ($mode != 'print' && $invoice->canEdit) { ?><a href="#" onclick="editCharge(<?= $c['id'] ?>,<?= $c['opm_productid'] ?>); return false;" class="invItemLink" title="Edit This Charge"><? } ?><?= $c['chargedescription'] ?><? if ($mode != 'print' && $invoice->canEdit) { ?><span class="invoiceClickToEdit"> edit</span><? } ?></a></td>
								
									<? } else { ?>
									
										<td width="260"><? if ($mode != 'print' && $invoice->canEdit) { ?><a href="#" onclick="editCharge(<?= $c['id'] ?>,<?= $c['opm_productid'] ?>); return false;" class="invItemLink" title="Edit This Charge"><? } ?><?= $c['chargetype'] ?><? if ($mode != 'print' && $invoice->canEdit) { ?><span class="invoiceClickToEdit">  edit</span><? } ?></a></td>
									
									<? } ?>
								
								<? } ?>
								
								<td align="right"><?= $invoice->currencysymbol ?><?= $c['chargeamount'] ?></td>
								
								<td align="right">
								
									<? if (checkPerms('can_view_invoice_channelinfo')) { ?>
									
										<span id="invChannel_<?= $c['id'] ?>">
											
											<? if ($c['channelcode']) { ?>
												
												<? if (checkPerms('can_edit_invoice_channelinfo') && $invoice->canEdit) { ?>
											
													<a href="#" onclick="return false;" class="invoiceChnlTrig" id="<?= $c['id'] ?>"><?= $c['channelcode'] ?></a>
											
												<? } else { ?>
												
													<?= $c['channelcode'] ?>
												
												<? } ?>
											
											<? } else { ?>
									
												<? if (checkPerms('can_edit_invoice_channelinfo')) { ?>
											
													<a href="#" onclick="return false;" class="invoiceChnlTrig" id="<?= $c['id'] ?>">chnl</a>
											
												<? } ?>
									
											<? } ?>
									
										</span>
									
									<? } ?>
									
								</td>
								<td><a href="#" onclick="addLineItemNote(<?= $c['id'] ?>);return false;" class="invNoteTrig" id="invNoteTrig_<?= $c['id'] ?>"><img src="<?= base_url()?>resources/images/invEditPencil.gif" class="invNoteIcon" id="note_<?= $c['id'] ?>" width="12" /></a></td>
								
							</tr>
							
							<? if ($c['notes']) { ?>
			
								<div id="notesTooltip_<?= $c['id'] ?>" class="detailNoteTooltip"><div class="detailNoteTooltipNotes"><?= $c['notes'] ?></div></div>				
							
							<? } ?>
						
						<? } ?>

					</table>
					
				
				</div>
			
			</td>
			<td valign="top" align="right" class="invLiProdText"><?= $invoice->currencysymbol ?><?= number_format($i['totalCharges'], 2); ?><br />
				
				<? if ($mode != 'print' && $invoice->canEdit) { ?>
				
					<select name="options" style="margin-top:10px;font-size:7.5pt;width:90px;background-color:#ffffff;color:#999999;border-color:#cccccc;" onchange="if (this.value != '0') executeInvAction(<?= $c['id'] ?>,this.value,<?= $c['opm_productid'] ?>);">
						<option value="0">ACTIONS</option>
						<option value="0">-----------</option>
						<option value="removeProduct">Remove Product</option>
						<option value="addCharge">Add Charge</option>
					</select>
				
				<? } ?>
				
			</td>
		</tr>
	</table>


</div>

<div class="invoiceDiv"></div>

<? if ($mode != 'print') { ?>

<div id="invoiceImgTooltip_<?= $i['opm_productid'] ?>" class="invoiceImgTooltip">
				
	<img src="<?=base_url();?>imageclass/viewThumbnail/<?= $i['default_imageid'] ?>/350" class="invoiceTooltipImg" width="350" height="350" />

</div>



<script language="javascript">

	$("#invLIImg_<?= $i['opm_productid'] ?>").ready(function()
    { 
       
		$("#invLIImg_<?= $i['opm_productid'] ?>").tooltip({

			// use div.tooltip as our tooltip
			tip: '#invoiceImgTooltip_<?= $i['opm_productid'] ?>',
			
			position: 'center right',
			
			effect: 'fade'


	
		});

   
    });

    
    $("#liChargeDetail_<?= $i['opm_productid'] ?>").ready(function()
    { 
    
    	invoiceTooltip("a.invoiceChnlTrig");
		detailNotesTooltip("a.invNoteTrig");
   
    });
    

</script>

<? } ?>
