<!--<pre>
	<? //print_r($product) ?>
</pre>-->


<script>
$(document).ready(function() {
	
	$('.prodSummaryForumPost img').each(function(){

	    if($(this).width()>100){
	      //  $(this).width(600).css('cursor','pointer').click(function(){$(this).css('width','');});
	        $(this).css('width','100');
	        $(this).wrap("<a href='" + $(this).attr("src") + "' />");
	    }

	});
	
	$("#noteShower").click(function() {

		$("#sampleNotesArea").fadeIn('fast');
		return false;
	});

	// hide spinners
	$("img.spinner").hide();
	
	// listen for clicks on release buttons in the purchases table
	$("table#latest-purchase input.release").click(function() {

		// which row was clicked?
		var id = $(this).parents("tr").attr("id").replace(/purchase-id-/g, "");
		
		// show the spinner
		$("tr#purchase-id-" + id + " td.spinner-column img.spinner").show();

		// fade the latest purchase
		$("tr#purchase-id-" + id).fadeOut();
		
		// no double clicks
		$("table#latest-purchase input.release").attr("disabled", "disabled");

		
		$.ajax({

			type: "post",
			url: "/products/releasePurchase",
			data: { 

				id: id 
			},
			success: function(json) {

				if(json.release_response.type == "error") {

					// hide the spinner
					$("tr#purchase-id-" + id + " td.spinner-column img.spinner").hide();
					$("tr#purchase-id-" + id).fadeIn();
					
					// alert error
					alert(json.release_response.message);
				}

				else {

					// hide the spinner
					$("tr#purchase-id-" + id + " td.enddate img.spinner").hide();
					$("tr#purchase-id-" + id + " td.enddate span").text(json.release_response.message).show();
					$("tr#purchase-id-" + id + " td.release-button").text("");
				}

				// allow clicks on "release" buttons again
				$("table#latest-purchase input.release").removeAttr("disabled");				
			},
			dataType: "json"
		});
		
	});
});
</script>

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		
				<!-- GENERAL SUMMARY INFO -->
				
				<table border="0" width="95%" class="prodSummaryTable">
					
					<tr>
					
						<td class="prodSummaryInfoHeader">OPM ID:</td>
						<td class="prodSummaryInfo"><?= $product->opm_productid ?></td>
						
					</tr>
					
					<? if (checkPerms('can_view_productlines') && $product->productline) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Product Line(s):</td>
							<td class="prodSummaryInfo"><?= $product->productline ?></td>
						</tr>
					
					<? } ?>
					
					<!--<tr>
						<td class="prodSummaryInfoHeader">Product Name:</td>
						<td class="prodSummaryInfo"><?= $product->productname ?></td>
					</tr>-->
					
					<? if (checkPerms('can_view_shortname') && $product->shortname) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Short Name:</td>
							<td class="prodSummaryInfo"><?= $product->shortname ?></td>
						</tr>
					
					<? } ?>
					
					<? if ($product->category) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Category:</td>
							<td class="prodSummaryInfo"><?= $product->category ?></td>
						</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_bodystyle') && $product->bodystyle) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Body Style:</td>
							<td class="prodSummaryInfo"><?= $product->bodystyle ?></td>
						</tr>
					
					<? } ?>
					
					<? if ($product->numprints) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader"># of Prints:</td>
							<td class="prodSummaryInfo"><?= $product->numprints ?></td>
						</tr>
					
					<? } ?>
					
					<? if ($product->territories) { ?>
						
						<tr>
							<td class="prodSummaryInfoHeader">Available In:</td>
							<td class="prodSummaryInfo"><?= $product->territories ?></td>
						</tr>
						
					<? } ?>
					
					<? if (checkPerms('can_view_productcodes') && $product->productcode) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Product Code:</td>
							<td class="prodSummaryInfo"><?= $product->productcode ?></td>
						</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_licenseecode') && $product->licenseecode) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Licensee Code:</td>
							<td class="prodSummaryInfo"><?= $product->licenseecode ?></td>
						</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_designcode') && $product->designcode) { ?>
					
						<tr>
							<td class="prodSummaryInfoHeader">Design Code:</td>
							<td class="prodSummaryInfo"><?= sprintf("%04d", $product->designcode); ?></td>
						</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_designers') && $product->designers) { ?>
					
					<tr>
						<td class="prodSummaryInfoHeader">Designer:</td>
						<td class="prodSummaryInfo"><?= buildDesignerList($product->designers) ?></td>
					</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_productdesc') && $product->productdesc) { ?>
					
					<tr>
						<td class="prodSummaryInfoHeader">Description:</td>
						<td class="prodSummaryInfo"><?= $product->productdesc ?></td>
					</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_summary_separators') && $product->separators) { ?>
					
					<tr>
						<td class="prodSummaryInfoHeader">Separator(s):</td>
						<td class="prodSummaryInfo"><?= buildAbbrList($product->separators) ?></td>
					</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_summary_screenprinters') && $product->screenprinters) { ?>
					
					<tr>
						<td class="prodSummaryInfoHeader">Screen Printer(s):</td>
						<td class="prodSummaryInfo"><?= buildAbbrList($product->screenprinters) ?></td>
					</tr>
					
					<? } ?>
					
					<? if (checkPerms('can_view_licensees') && $product->licensees) { ?>
					
					<tr>
						<td class="prodSummaryInfoHeader">Licensee(s):</td>
						<td class="prodSummaryInfo"><?= buildAbbrList($product->licensees) ?></td>
					</tr>
					
						<? if (in_array($this->config->item('licenseeGroupID'),$product->usergroups)) { ?>
						
							<tr>
								<td class="prodSummaryInfoHeader">Prop Licensee(s):</td>
								<td class="prodSummaryInfo"><?= buildAbbrList($product->propLicensees) ?> </td>
							</tr>
						
						<? } ?>
					
					<? } ?>
					
					<? if ($product->copyright || $product->copyrightaddendums) { ?>
					
					<tr>
						<td class="prodSummaryInfoHeader">Copyright:</td>
						<td class="prodSummaryInfo"><?= $product->copyright ?> <?= ($product->copyrightaddendums ? $product->copyrightaddendums : null) ?></td>
					</tr>
					
					<? } ?>
					
					<? if ($product->filmlocations && checkPerms('can_view_film_locations') && $product->filmlocations) { ?>
						
						<tr>
							<td class="prodSummaryInfoHeader">Print + Garment Info:</td>
							<td class="prodSummaryInfo"><?= $product->filmlocations ?></td>
						</tr>
						
					<? } ?>
					
					<? if ($product->filmnumber && checkPerms('can_view_film_number') && $product->filmnumber) { ?>
						
						<tr>
							<td class="prodSummaryInfoHeader">Film Number:</td>
							<td class="prodSummaryInfo"><?= $product->filmnumber ?></td>
						</tr>
						
					<? } ?>
					
					<? if ($product->artworkcharges && checkPerms('can_view_artwork_charges') && $product->artworkcharges) { ?>
						
						<tr>
							<td class="prodSummaryInfoHeader">Artwork Charges:</td>
							<td class="prodSummaryInfo"><?= $product->artworkcharges ?></td>
						</tr>
						
					<? } ?>
					
					<? if ($product->presentationstyles && checkPerms('can_view_presentation_styles') && $product->presentationstyles) { ?>
						
						<tr>
							<td class="prodSummaryInfoHeader">Presentation Styles:</td>
							<td class="prodSummaryInfo"><?= $product->presentationstyles ?></td>
						</tr>
						
					<? } ?>
					
					<? if (checkPerms('summary_can_view_createdby')) { ?>
						
						<tr>
							<td class="prodSummaryInfoHeader">Created By:</td>
							<td class="prodSummaryInfo"><?= $product->createdbyname ?></td>
						</tr>
						
					<? } ?>
					
					<? if (checkPerms('can_edit_products'))  { ?>
						<tr>
							<td colspan="2" align="right"><a href="<?= base_url(); ?>products/edit/<?= $product->opm_productid?>" class="blueLink"><img src="<?= base_url(); ?>resources/images/btn_edit.gif" border="0"></a></td>
						</tr>
					<? } ?>
				</table>
				
				<!-- / GENERAL SUMMARY INFO -->
				
				<!-- PURCHASE INFO -->
				<?php if(count($product->purchases) > 0): ?>
	
					<br /><br />
		
					<table id="latest-purchase" border="0" width="95%" cellpadding="2" cellspacing="0">	
						
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="3">Latest Purchase</td>	
						</tr>
						
						<tr id="purchase-id-<?= $product->purchases[0]['id']; ?>">
							
							<td class="latest-purchase-info prodSummaryAppRow1">
								<strong><?= ucwords($product->purchases[0]['pt_pasttense']); ?> by: </strong>
								<?= $product->purchases[0]['account']; ?> on 
								<?= date('m-d-y', $product->purchases[0]['timestamp']); ?>
								<? if($product->purchases[0]['isexclusive'] == 1): ?>
									(EXCLUSIVE)
								<? endif; ?>
							</td>
							
							<td class="spinner-column" style="width: 16px; height: 16px; padding-right: 8px;">
								<img class="spinner" src="/resources/images/spinner.gif" />
							</td>
							
							<td class="release-button">
								<? if($product->purchases[0]['enddate'] > $now || $product->purchases[0]['enddate'] == 0): ?>
									<input class="release" type="button" value="Release" />
								<? endif; ?>
							</td>
						</tr>
					</table>
					
					<br /><br />
					
					<table id="purchase-history" border="0" width="95%" cellpadding="2" cellspacing="1">	
						
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="4">Purchase history</td>	
						</tr>
						
						<tr>
							<td class="prodSummaryTableHeadRow">Account</td>
							<td class="purchasetype prodSummaryTableHeadRow">Type</td>
							<td class="timestamp prodSummaryTableHeadRow">When</td>
							<td class="enddate prodSummaryTableHeadRow">End date</td>
						</tr>
						
						<? foreach($product->purchases as $purchase): ?>
							
							<tr id="purchase-id-<?= $purchase['id']; ?>">
								
								<td class="prodSummaryAppRow1"><?= $purchase['account']; ?></td>
								
								<td class="purchasetype prodSummaryAppRow1">
									<?= $purchase['purchasetype']; ?>
								</td>
								
								<td class="timestamp prodSummaryAppRow1">
									<?= date('m-d-y', $purchase['timestamp']); ?>
								</td>
								
								<td class="enddate prodSummaryAppRow1">
									<? if($purchase['enddate'] != 0): ?>
									
										<?= date('m-d-y', $purchase['enddate']); ?>
									
									<? else: ?>
									
										<?= 'None set'; ?>
										
									<? endif; ?>
									
								</td>
							
							</tr>	
					
						<? endforeach; ?>
					
					</table>
					
				<? endif; ?>
				
				<!-- / PURCHASE INFO -->
				
				<br /><br />
				
				<? if (checkPerms("can_view_channels")) { ?>
				

						<table border="0" width="95%">
					
						
							<tr bgcolor="#ededed">
								<td class="prodSummaryTableHeader" colspan="3">Artist Channels:</td>	
							</tr>		
								
								
							<?= $this->opm->displayChannels($product->opm_productid); ?>								

							</table>
	
				
				<br /><br />
				
				<? } ?>
				
				<? if (checkPerms('can_email_contacts')) { ?>
				
				<table border="0" width="95%" cellpadding="0" cellspacing="0">	
					<tr bgcolor="#ededed">
						<td class="prodSummaryTableHeader" colspan="2">Email Notifications</td>	
						<td><a href="#" onclick="return false;" onmouseover="mopen('appMenu_999999')" onmouseout="mclosetime()"><img src="<?=base_url();?>resources/images/arrDown.gif" alt="" width="8" height="7" border="0" id="appMenuOpener_999999" /></a></td>
					</tr>
				</table>
				
				<? } ?>
				
				<br /><br />
				
				<? if (in_array($product->approvalstatusid, $this->config->item('phase2Statuses')))  {  // don't show sample approval if we aren't even concept approved.?>
				
					<table border="0" width="95%" cellpadding="0" cellspacing="0">	
					
						<? if (checkPerms('can_view_sample_approval')) { ?>
					
							<tr bgcolor="#ededed">
								<td class="prodSummaryTableHeader" colspan="2">Sample Approval: <span id="sampApprovalStatus" class="prodSummaryAppRow_<?= $product->sampleappstatusid ?>"><?= $product->sampleappstatus ?></span></td>	
								<? if (checkPerms('can_edit_samp_approval')) {  ?>
									<td><a href="#" onclick="return false;" onmouseover="mopen('appMenu_x888888')" onmouseout="mclosetime()"><img src="<?=base_url();?>resources/images/arrDown.gif" alt="" width="8" height="7" border="0" id="appMenuOpener_x888888" /></a></td>
								<? } ?>
							</tr>
						
						<? } ?>
						
						
								<!--<tr>
									<td class="prodSummaryTableHeadRow">Contact</td>
									<td class="prodSummaryTableHeadRow">Status</td>
									<Td></td>
								</tr>-->
								
									<form id="sampleDateForm" method="post" action="<?= base_url() ?>products/saveSampleDates"><input type="hidden" name="jow" value="my nammmeizz jow!!" />

									
									<? if (checkPerms('can_view_sample_sent')) { ?>
									
										<tr>
											<td class="prodSummaryAppRow1">Sample Sent:</td>
											<td class="prodSummaryAppRow">
										
												<? if (checkPerms('can_edit_sample_sent')) { ?>
												
													<a href="#" id="samplesentdisplay"><?= ($product->samplesentdate) ? opmDate($product->samplesentdate,true) : "--" ?></a><? if (checkPerms('can_add_sample_notes')) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" id="noteShower" class="blueLink" style="font-weight:normal;">Add Notes<? } ?></a>
													<input type="hidden" name="samplesentdate" id="samplesentdate" value="" />
												
												<? } else { ?>
												
													<?= opmDate($product->samplesentdate,true); ?>
												
												<? } ?>
										
											</td>
											<td><img src="<?=base_url();?>resources/images/x.gif" height="25" width="1" /></td>
	
										</tr>
									
									<? } ?>
									
									<? if (checkPerms('can_view_sample_rec')) { ?>
									
										<tr>
										
											<td class="prodSummaryAppRow1">Sample Received:</td>
											<td class="prodSummaryAppRow">
											
											<? if (checkPerms('can_edit_sample_rec')) { ?>
											
												<a href="#" id="samplerecdisplay"><?= ($product->samplerecdate) ? opmDate($product->samplerecdate,true) : "--" ?></a>
												<input type="hidden" name="samplerecdate" id="samplerecdate" value="" />
												
											<? } else { ?>
											
												<?= opmDate($product->samplerecdate,true); ?>
											
											<? } ?>
										
											</td>
											<td><img src="<?=base_url();?>resources/images/x.gif" height="25" width="1" /></td>
											
											
											
										</tr>
									
									<? } ?>
									
									</form>
									
									<? if (checkPerms('can_view_sample_notes')) { ?>
									
										<tr>
											
											<td colspan="3" style="padding-left:10px;">
												
												<div id="sampleNotesArea" style="display:none;">
												
													<textarea id="sampleNotes" style="width:350px; height:100px;"></textarea><br /><br />
													<input type="submit" id="sampleNotesSubmit" value="Save" />
												
												</div>
												
												
												<div id="sampleNotesDiv"><?= nl2br($product->samplenotes); ?></div>
											
											</td>
											
										</tr>
									
									<? } ?>
						
						
					</table>
					
					
					
					
					
					
					<br /><br />
					
				<? } ?>
				
				
					
					<!-- APPROVAL STATUS -->
					
					<table border="0" width="95%">
					
						<? if (checkPerms('can_view_approval_status')) { ?>
						
							<tr bgcolor="#ededed">
								<td class="prodSummaryTableHeader" colspan="3">Approval Status: <span class="prodSummaryAppRow_<?= $product->approvalstatusid ?>"><?= $product->approvalstatus ?></span></td>	
								<? if (checkPerms('can_expire_products')) {  ?>
									<td><a href="#" onclick="return false;" onmouseover="mopen('appMenu_x888889')" onmouseout="mclosetime()"><img src="<?=base_url();?>resources/images/arrDown.gif" alt="" width="8" height="7" border="0" id="appMenuOpener_x888889" /></a></td>
								<? } ?>
							</tr>
						
						<? } ?>

						
						<? if (checkPerms('can_view_approval_status_detail') && (in_array($product->approvalstatusid, $this->config->item('phase2Statuses')))) { ?>
							
							
								<tr>
									<td class="prodSummaryTableHeadRow">Contact</td>
									<td class="prodSummaryTableHeadRow">Status</td>
									<Td></td>
								</tr>
								
								<? foreach ($product->approvalInfo as $contact) { ?>
									
									<tr>
										<td class="prodSummaryAppRow1"><?= $contact->username ?></td>
										<td class="prodSummaryAppRow_<?= $contact->approvalstatusid ?>"><?= ($contact->approvalrequired ? $contact->approvalstatus : "<span class='grayout'>Not Required</span>") ?></td>
										
										<? if (checkPerms('can_verbally_approve') || checkPerms('can_reverse_approvals')) { ?>
										
											<td><a href="#" onclick="return false;" onmouseover="mopen('appMenu_<?= $contact->userid ?>')" onmouseout="mclosetime()"><img src="<?=base_url();?>resources/images/arrDown.gif" alt="" width="8" height="7" border="0" id="appMenuOpener_<?= $contact->userid ?>"/></a></td>
										
										<? } ?>
									
									</tr>
								
				
								<? } ?>
							
					
						<? } ?>
					</table>
					
					
					<? if (isset($product->myStatus) && ($product->approvalstatusid == 0)) { // this means user is approval contact, and product isn't already approved. ?>
					
						<br /><br />
						
						<table border="0">
						
							<tr>
								<td class="prodSummaryTableHeadRow">My Status:</td> 
								<td class="prodSummaryAppRow_<?= $product->myStatus->approvalstatusid ?>"><?= $product->myStatus->approvalstatus ?></td>
							</tr>
							
						</table>
						
						<? if ($product->myStatus->approvalstatusid == 0) { ?>
						
						
							<!--<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/approve"><img src="<?=base_url();?>resources/images/btn_approve.gif" alt="image" width="295" height="30" border="0" /></a></div>-->
						
						 	<!-- I APPROVE BTN -->
						 	
						 	<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="#" onclick="opm.showHideDiv('revisions2'); return false;"><img src="<?=base_url();?>resources/images/btn_approve.gif" alt="image" width="295" height="30" border="0" /></a></div>
						 
						 	<!--<button type="button" style="margin-top:10px;" name="" value="" onclick="opm.showHideDiv('revisions2'); return false;" class="chgStatusBtn">I APPROVE THIS DESIGN</button>-->

						 
							<div id="revisions2">
								
								<form name="revisionsform2" method="post" action="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/approvewrevisions">
							
									<textarea class="txtRevisions" name="revisions" onfocus="if(this.value=='If you have any revisions, please enter them here...') this.value = '';">If you have any revisions, please enter them here...</textarea><br />
									
									<input type="submit" name="submitRevs" value="Submit" class="revSubmit">
								
								</form>
								
							</div>
						 	
						 	
						 	<!-- APPROVE W/ REVISIONS BTN -->
						
							<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="#" onclick="opm.showHideDiv('revisions1'); return false;"><img src="<?=base_url();?>resources/images/btn_approvewrevisions.gif" alt="image" width="295" height="30" border="0" /></a></div>
							<div id="revisions1">
								
								<form name="revisionsform1" method="post" action="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/approvewrevisions">
							
									<textarea class="txtRevisions" name="revisions" onfocus="if(this.value=='Enter Revisions Here...') this.value = '';">Enter Revisions Here...</textarea><br />
									
									<input type="submit" name="submitRevs" value="Submit" class="revSubmit">
								
								</form>
								
							</div>
							<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="#" onclick="opm.showHideDiv('revisions3'); return false;"><img src="<?=base_url();?>resources/images/btn_resubmitwrevisions.gif" alt="image" width="295" height="30" border="0" /></a></div>
							<div id="revisions3">
								
								<form name="revisionsform3" method="post" action="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/resubmitwrevisions">
							
									<textarea class="txtRevisions" name="revisions" onfocus="if(this.value=='Enter Revisions Here...') this.value = '';">Enter Revisions Here...</textarea><br />
									
									<input type="submit" name="submitRevs" value="Submit" class="revSubmit">
								
								</form>
								
							</div>
							<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/reject"><img src="<?=base_url();?>resources/images/btn_reject.gif" alt="image" width="295" height="30" border="0" /></a></div>
							
							
						<? } ?>
					
					<? } ?>
					
					
					
					<? foreach ($product->approvalInfo as $contact) { ?>
					
						
						<div class="appMenu" id="appMenu_<?= $contact->userid ?>"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
							
							<? if ($contact->approvalstatusid == 0 && checkPerms('can_verbally_approve')) {  // status is pending, allow verbal approvals ?>
							
							<a href="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/verballyApprove/<?= $contact->userid ?>">Verbally Approve</a>
	        				
	        				<? } ?>
	        				
	        				<? if ($contact->approvalstatusid == 0 && checkPerms('can_verbally_reject')) {  // status is pending, allow verbal approvals ?>
							
							<br /><a href="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/verballyReject/<?= $contact->userid ?>">Verbally Reject</a>
	        				
	        				<? } ?>
	        				
	        				<? if ($contact->approvalstatusid != 0 && checkPerms('can_reverse_approvals')) {  // status is not pending, allow undos ?>
	        				
	        					<a href="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/undo/<?= $contact->userid ?>">Reverse This</a>
							
							<? } ?>
						</div>
					
					<? } ?>
					
				<? if (checkPerms('can_approve_concepts') && (!in_array($product->approvalstatusid, $this->config->item('phase2Statuses')))) { ?>
					
					<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="<?=base_url();?>products/changeConceptApprovalStatus/<?= $product->opm_productid ?>/approveconcept"><img src="<?=base_url();?>resources/images/btn_approveConcept.gif" alt="image" width="295" height="30" border="0" /></a></div>
				
				<? } ?>
				
				<? if (checkPerms('can_approve_concepts') && (!in_array($product->approvalstatusid, $this->config->item('phase2Statuses')))) { ?>
					
					<div class="btnApprovalOFF" onmouseover="this.className='btnApprovalON'" onmouseout="this.className='btnApprovalOFF'"><a href="<?=base_url();?>products/changeConceptApprovalStatus/<?= $product->opm_productid ?>/approveconcept"><img src="<?=base_url();?>resources/images/btn_approveConcept.gif" alt="image" width="295" height="30" border="0" /></a></div>
				
				<? } ?>
					
				
				<form name="sendEmailForm" method="post" action="<?=base_url();?>email/sendEmailToContacts/<?= $product->opm_productid ?>">
				
					<input type="hidden" name="opm_productid" value="<?=$product->opm_productid?>" />
					<input type="hidden" name="notificationComment" />
					<input type="hidden" name="emailType" />
					<input type="hidden" name="mode" />
					
				</form>
				
				<!-- / APPROVAL STATUS -->
				
				<? if (checkPerms('can_view_exploit_status')) { ?>
					
					<br /><br />
					
					<table border="0" width="95%" cellpadding="0" cellspacing="0">	
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="2">Exploitation Status: <span id="sampApprovalStatus" class="prodSummaryExploitRow_<?= $product->exploitstatusid ?>"><?= $product->exploitstatus ?></span></td>	
							<? if (checkPerms('can_edit_exploit_status')) {  ?>
								<td><a href="#" onclick="return false;" onmouseover="mopen('appMenu_x888881')" onmouseout="mclosetime()"><img src="<?=base_url();?>resources/images/arrDown.gif" alt="" width="8" height="7" border="0" id="appMenuOpener_x888881" /></a></td>
							<? } ?>
						</tr>
					</table>
					
				<? } ?>
				
				<? if (checkPerms('can_view_usage_rights')) { ?>
					
					<br /><br />
					
					<table border="0" width="95%" cellpadding="0" cellspacing="0">	
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="2">Usage Rights: <span id="sampApprovalStatus" class="prodSummaryUsageRow_<?= $product->usagestatusid ?>"><?= $product->usagestatus ?></span></td>	
							<? if (checkPerms('can_edit_usage_rights')) {  ?>
								<td><a href="#" onclick="return false;" onmouseover="mopen('appMenu_x888882')" onmouseout="mclosetime()"><img src="<?=base_url();?>resources/images/arrDown.gif" alt="" width="8" height="7" border="0" id="appMenuOpener_x888882" /></a></td>
							<? } ?>
						</tr>
					</table>
					
				<? } ?>
				
				
				<!-- EMAIL MENU -->
					
					<div class="appMenu" id="appMenu_999999"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
						
						<? if (checkPerms('can_email_contacts')) {  // status is pending, allow verbal approvals ?>
						
							<a href="#" onclick="document.sendEmailForm.mode.value = 'newProduct'; openUserPicker(<?=$product->opm_productid?>,'newProduct'); return false;">Send New Product Email</a><br />
        					<a href="#" onclick="document.sendEmailForm.mode.value = 'productUpdated'; openUserPicker(<?=$product->opm_productid?>,'productUpdated'); return false;">Send Product Updated Email</a>
        				
        				<? } ?>
        				
					</div>
				
				<!-- / EMAIL MENU -->
				
				<!-- SAMPLE APPROVAL MENU -->
					
					<div class="appMenu" id="appMenu_x888888"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
						
						<? if (checkPerms('can_edit_samp_approval')) {  ?>
						
							<? if (!$product->sampleappstatusid) { ?>
						
								<a href="<?=base_url();?>products/setSampAppStatus/<?= $product->opm_productid ?>/1/">Set To Approved</a>
        					
        					<? } else { ?>
								
								<a href="<?=base_url();?>products/setSampAppStatus/<?= $product->opm_productid ?>/0/">Set To Pending</a>
        				
        					<? } ?>	
        			
        				<? } ?>
        				
					</div>
				
				<!-- / SAMPLE APPROVAL MENU -->
				
				<!-- EXPLOITATION STATUS MENU -->
					
					<div class="appMenu" id="appMenu_x888881"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
						
						<? if (checkPerms('can_edit_exploit_status')) {  ?>
						
							<? if ($product->exploitstatusid == $this->config->item('exStatusActive')) { ?>
						
								<a href="<?=base_url();?>products/setExploitStatus/<?= $product->opm_productid ?>/<?= $this->config->item('exStatusInactive') ?>/">Set To Inactive</a>
        					
        					<? } else { ?>
								
								<a href="<?=base_url();?>products/setExploitStatus/<?= $product->opm_productid ?>/<?= $this->config->item('exStatusActive') ?>/">Set To Active</a>
        				
        					<? } ?>	
        			
        				<? } ?>
        				
					</div>
				
				<!-- / EXPLOITATION STATUS MENU -->
				
				<!-- USAGE RIGHTS MENU -->
					
					<div class="appMenu" id="appMenu_x888882"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
						
						<? if (checkPerms('can_edit_usage_rights')) {  ?>
						
							<? if ($product->usagestatusid == $this->config->item('usageStatusCleared')) { ?>
						
								<a href="<?=base_url();?>products/setUsageStatus/<?= $product->opm_productid ?>/<?= $this->config->item('usageStatusUncleared') ?>/">Set To Uncleared</a>
        					
        					<? } else { ?>
								
								<a href="<?=base_url();?>products/setUsageStatus/<?= $product->opm_productid ?>/<?= $this->config->item('usageStatusCleared') ?>/">Set To Cleared</a>
        				
        					<? } ?>	
        			
        				<? } ?>
        				
					</div>
				
				<!-- / USAGE RIGHTS MENU -->
				
				<!-- EXPIRY MENU -->
					
					<div class="appMenu" id="appMenu_x888889"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
						
						<? if (checkPerms('can_complete_revisions') && $product->approvalstatusid == $this->config->item('appStatusAwaitingRevisions')) {  ?>
						
								<a href="<?=base_url();?>products/changeApprovalStatus/<?= $product->opm_productid ?>/revisionscomplete/">Revisions Complete</a><br />		
        			
        				<? } ?>
						
						<? if (checkPerms('can_expire_products')) {  ?>
						
							<? if ($product->approvalstatusid != $this->config->item('appStatusExpired')) { ?>
						
								<a href="<?=base_url();?>products/expireProduct/<?= $product->opm_productid ?>/1/">Expire Product</a>
        					
        					<? } else { ?>
								
								<a href="<?=base_url();?>products/expireProduct/<?= $product->opm_productid ?>/0/">Un-Expire Product</a>
        				
        					<? } ?>	
        			
        				<? } ?>
        				
					</div>
				
				<!-- / EXPIRY MENU -->
				
				<? if (checkPerms('can_view_forum_summary')) { ?>
				
					<br /><br />
					
					<!-- Latest Forum Entry -->
					
					<table border="0" width="95%">
						<tr>
							<td class="prodSummaryTableHeader" colspan="10">Forum Activity</td>
						</tr>
						<tr>
							
							<? if ($product->latestForum) { ?>
								
								<td>
									<table border="0" cellpadding="3" width="90%" style="margin-top:10px" align="center">
										<tr>
											<td><? $this->opm->displayAvatar($product->latestForum->userid); ?></td>
											<td>
												<div class="prodSummaryForumHeader">Posted <?=opmDate($product->latestForum->timestamp)?> by <?= $product->latestForum->postname ?></div>
												<div class="prodSummaryForumTitle"><?=$product->latestForum->posttitle?></div>
												<div class="prodSummaryForumPost"><?=character_limiter($product->latestForum->post,100)?></div>
											</td>
										</tr>
										<tr>
											<td colspan="2" align="right"><a href="javascript:opm.changeContent(5,'forum','products');" class="redLink">view forum</a></td>
										</tr>
									</table>
								</td>	
							
							<? } else { ?>
							
								<td class="prodSummaryAppRow1"><br />No Forum Entries.</td>	
								
							<? } ?>
							
						</tr>
					</table>
					
					
					<!-- / Latest Forum Entry -->
				
				<? } ?>
		
		</td>
		<td valign="top" align="right">
			
			<div id="prodSummaryImage"><? if (checkPerms('can_view_visuals')) { ?><div id="detailImageDiv"><a href="#" onclick="detailImageShadowbox(); return false;"><img src="<?= base_url(); ?>imageclass/viewThumbnail/<?= $product->default_imageid ?>/350" width="350" border="0" id="detailImage"></a></div></div>
			
			<div id="prodSummaryThumbnails">
				
				<table border="0" cellpadding="0" cellspacing="0" align="left">
					<tr>
						<? 
							$count = 1;
							
							if ($product->imageids) {
							
								foreach ($product->imageids as $tnid) { 
							
							?>
							
									<td>
										<div class="prodSummaryImageTN">
											<a href="javascript:opm.swapDetailImg('<?=base_url();?>imageclass/viewThumbnail/<?=$tnid?>/350');" onmouseover="mopen_label('appMenu_img<?=$tnid?>')" onmouseout="mclosetime()"><img src="<?=base_url();?>/imageclass/viewThumbnail/<?=$tnid?>" width="60" border="0" id="appMenuOpener_img<?=$tnid?>"></a>
										</div>
										<div class="thumbnailArrowDiv"><img src="<?=base_url();?>/resources/images/thumbnailArrow.gif" alt="image" width="11" height="9" /></div>
									</td>
														
							<? 
									$count++;
									
									if ($count > 5) {
									
										$count = 1;
										echo "</tr><tr><td colspan='10'><br/></td></tr><tr>";
									
									}
								
							
								} 
								
							}
						
						?>
						
						
					</tr>	
				</table>
			
			<? } else { ?>	
			
				<div id="detailImageDiv"><img src="<?= base_url(); ?>resources/images/x.gif" width="350" height="350" border="0" id="detailImage"></div>
			
			<? } ?>
			
			<br /> <br />
			
			<? if (checkPerms("can_view_available_colors")) { ?>
			
				<div id="colors" style="clear:both;margin-top:50px; text-align:left">
				
					<table border="0" width="100%" cellpadding="0" cellspacing="0">	
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="2">Available Colors</td>	
							<td>&nbsp;</td>
						</tr>
					</table>
					
					<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center" style="margin-top:20px;">
						
	
						
						<? foreach ($product->colors as $c) { ?>
						
						<tr>
							<td class="prodColor"><?= $c['color'] ?></td>
							<td align="right"><a href="<?= base_url() ?>colors/removeColor/<?= $product->opm_productid ?>/<?= $c['id'] ?>">remove</a></td>
						</tr>
							
						<? } ?>
					
					</table>
	
					<? if (checkPerms("can_add_available_colors") && (sizeof($product->colors) == 0)) { ?>
					
						<br /><br />
						
						<form name="addColorForm" method="post" action="<?= base_url(); ?>colors/addColorToProduct/">
						
							<input type="hidden" name="opm_productid" value="<?= $product->opm_productid ?>" />
						
							<select name="colorid" id="colorid" style="height:24px;">
								<option value="0">Add Color...</option>
								
								<? foreach ($colors->result() as $c) { ?>
								
									<option value="<?= $c->id?>"><?= $c->color ?></option>
								
								<? } ?>
								
							</select>
							
							<input type="image" src="<?= base_url(); ?>resources/images/btn_add.gif" align="absmiddle" style="border:none;" border="0">
					
						</form>
					
					<? } ?>
				
				</div>
				
			<? } ?>
			
			<? if (checkPerms("can_view_available_sizes")) { ?>
			
				<div id="sizes" style="clear:both;margin-top:50px; text-align:left">
				
					<table border="0" width="100%" cellpadding="0" cellspacing="0">	
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="2">Available Sizes</td>	
							<td>&nbsp;</td>
						</tr>
					</table>
					
					<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center" style="margin-top:20px;">
						
	
						
						
						
						<tr>
							<td class="prodColor"><?= $sizeString ?></td>
						</tr>
			
					
					</table>
	
					<? if (checkPerms("can_add_available_sizes")) { ?>
						
						<div id="manageSizes" style="margin-top:10px; color:#666666;">	<a href="javascript:openSizesWindow(<?= $product->opm_productid ?>);"><span style="color:#999999;font-size:16pt;padding-top:20px;">+</span>&nbsp;&nbsp;Manage Sizes</a> </div>
					
					<? } ?>
				
				</div>
				
			<? } ?>
			
			<? if (checkPerms("can_view_linked_products")) { ?>
			
				<div id="sizes" style="clear:both;margin-top:50px; text-align:left">
				
					<table border="0" width="100%" cellpadding="0" cellspacing="0">	
						<tr bgcolor="#ededed">
							<td class="prodSummaryTableHeader" colspan="2">Linked Products:</td>	
							<td>&nbsp;</td>
						</tr>
					</table>
					
					<br />
										
					<? foreach ($product->links as $l) { ?>
					
						<? //print_r($l) ?>
					
						<div style="margin-left:10px; margin-bottom:3px;"><a href="<?= base_url(); ?>products/view/<?= $l['linked_id'] ?>" class="blueLink" style="font-weight:normal;"><?= $l['linked_id'] ?> - <?= $l['productname'] ?></a><? if (checkPerms("can_remove_product_links")) { ?>&nbsp;&nbsp;<a href="<?= base_url(); ?>products/removeLink/<?= $product->opm_productid ?>/<?= $l['id'] ?>/<?= $l['linked_id'] ?>" style="color:#999999; font-weight:normal;">remove</a><? } ?></div>
				
					<? } ?>
					
					<? if (checkPerms("can_add_linked_products")) { ?>
							
						<div id="manageSizes" style="margin-top:10px; color:#666666;">	<a class="blueLink" style="font-weight:normal;" href="javascript:openLinkProductWindow(<?= $product->opm_productid ?>);"><span style="color:#999999;font-size:16pt;padding-top:20px;">+</span>&nbsp;&nbsp;Add Linked Product</a> </div>
					
					<? } ?>
				
				
				</div>
				
			<? } ?>
			
		</td>
	</tr>
</table>

<!-- IMAGE LABEL TOOLTIPS! -->

<?  


	foreach ($images->result() as $img) {  

		if ($img->image_label) {

?>

			<div class="appMenu" id="appMenu_img<?=$img->imageid?>"  onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
		
				<h1><?=$img->image_label?></h1>						
						
			</div>

<? 
		}

	} 

?>

<br />	
	