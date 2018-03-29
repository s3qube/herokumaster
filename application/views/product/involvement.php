<script language="javascript">

	$(".chzn-select").chosen();
	
	$(".chzn-select").chosen().change(function() {
		
		// disable select while waiting for ajax to complete.
		
		$selectElement = $(this);
		
		$selectElement.attr('disabled', true).trigger("liszt:updated");
		
		var parentUGID = $selectElement.attr('id').substring(4);
				
		var UGids = $selectElement.val();
				
		// update DB with new info
		
		var url = base_url + 'ajax/updateUsergroups/';
		
		$.post(url, { opm_productid: <?= $product->opm_productid ?>, usergroupids: UGids, parentid: parentUGID},
   	
	   		function(data) {
	    
	    	//	alert(data);
	    		
	    		if (data == 'SUCCESS') {
	    		
	    			$selectElement.attr('disabled', false).trigger("liszt:updated");
	    			
	    		
	    		} else {
	    		
	    			alert("An error was encountered. Usergroups could not be updated. Please reload this page and try again." + data);
	    		
	    		}
	    
	    	}
	     
	 	);
		
		//alert($(this).val());
		//alert($(this).children("option:selected").text());
	
	});

</script>

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			
			<div id="inv_groups">
				<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
					<tr>
						<td class="boldHeader">Product Viewable By:</td>
						<td align="right"><!--<a href="javascript:changeGroup(<?=$product->opm_productid?>,1,1);"><img src="<?=base_url();?>/resources/images/inv_groupchk.gif" width="26" height="26" border="0"></a><a href="#"><img src="<?=base_url();?>/resources/images/inv_groupx.gif" width="26" height="26" border="0"></a>--></td>
					</tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td></td>
					</tr>
					<tr>
						<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
					</tr>
					<tr>
						<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
							
							
							
							<?= $this->opm->displayUsergroups($product->opm_productid); ?>								
							
					
					
						</td>
					</tr>
					<tr>
						<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
					</tr>
				</table>
			</div>	
			
			<!-- display UGs that are multiple select -->
			
			<? foreach ($selectUGs as $ugid=>$ug) { ?>
			
				<h3><?= $ug['usergroup'] ?></h3>
				
				<?
					
						//print_r($ug['children']);
						//die();
					
					?>
				
				<select class="chzn-select" id="ugs_<?= $ugid ?>" name="blah_<?= $ugid ?>" MULTIPLE>
					
						
					<? foreach ($ug['children'] as $ug2) { ?>
					
						<option value="<?= $ug2['usergroupid'] ?>" <? if (isset($ug2['isassigned'])) { echo "SELECTED"; } ?>><?= $ug2['usergroup'] ?></option>
					
					<? } ?>
				
				</select>				
				
			
			<? } ?>
			
			<br /><br />
			
			<form name="sendEmailForm" action="<?= base_url(); ?>email/sendProductionEmail/<?=$product->opm_productid?>" method="post">

				<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
					<tr>
						<td class="boldHeader">Notify Screenprinters / Separators / Designers:</td>
					</tr>
					<tr>
						<td><br /></td>
					</tr>
					<tr>
						<td>
						
							<input type="hidden" name="action" value="" /> <!-- THIS WILL BE SET BY SHADOWBOX IFRAME TO TELL CONTROLLER WHICH EMAIL TO SEND -->
							<input type="hidden" name="recipientUGs" value="" /> <!-- DITTOZ -->
							<input type="hidden" name="notificationComment" value="" /> <!-- DITTOZ -->
						
							
							<input type="button" name="sepsReady" onclick="document.sendEmailForm.action.value = 'sepsReady'; openUserPicker(<?=$product->opm_productid?>,'screenPrinters');" value="Send Separations Print Ready Email"> <br /> <br />
							<input type="button" name="sepsUpdated" onclick="document.sendEmailForm.action.value = 'sepsUpdated'; openUserPicker(<?=$product->opm_productid?>,'screenPrinters');" value="Send Separations Updated Email">
							
							<br /> <br /> <br />
							
							<input type="button" name="artworkReady" onclick="document.sendEmailForm.action.value = 'artworkReady'; openUserPicker(<?=$product->opm_productid?>,'separators');" value="Send Artwork Ready To Separate Email"> <br /> <br />
							<input type="button" name="artworkUpdated" onclick="document.sendEmailForm.action.value = 'artworkUpdated'; openUserPicker(<?=$product->opm_productid?>,'separators');" value="Send Artwork Updated Email">
							
							<br /> <br /> <br />
							
							<input type="button" name="newDesignProject" onclick="document.sendEmailForm.action.value = 'newDesignProject'; openUserPicker(<?=$product->opm_productid?>,'newDesign');" value="Send New Design Project Email"> <br /> <br />
							
						</td>
					</tr>
				</table>
				
			</form>
		
		</td>
		
		<td valign="top" align="right">
		
			<? if (checkPerms("can_view_territories")) { ?>
		
				<div id="">
				
					<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
						<tr>
							<td class="boldHeader">Territories:</td>
							<td align="right"><!--<a href=""><img src="<?=base_url();?>/resources/images/inv_groupchk.gif" width="26" height="26" border="0"></a><a href="#"><img src="<?=base_url();?>/resources/images/inv_groupx.gif" width="26" height="26" border="0"></a>--></td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td></td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
						</tr>
						<tr>
							<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
								
								
								
								<?= $this->opm->displayTerritories($product->opm_productid); ?>								
								
						
						
							</td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
						</tr>
					</table>
					
					<br /><br />
				
				<? } ?>
				
				<? if (checkPerms("can_view_rights")) { ?>
				
					<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
						<tr>
							<td class="boldHeader">Rights:</td>
							<td align="right"><!--<a href=""><img src="<?=base_url();?>/resources/images/inv_groupchk.gif" width="26" height="26" border="0"></a><a href="#"><img src="<?=base_url();?>/resources/images/inv_groupx.gif" width="26" height="26" border="0"></a>--></td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td></td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
						</tr>
						<tr>
							<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
								
								
								
								<?= $this->opm->displayRights($product->opm_productid); ?>								
								
						
						
							</td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
						</tr>
					</table>
					
					<br /><br />
					
				<? } ?>
				
				<? if (checkPerms("can_view_accounts")) { ?>
				
					<? if ($product->approvalstatusid == 1 || $product->approvalstatusid == 2) { // only show accounts if prod is approved?> 
					
					
					
						<!-- RETAIL ACCOUNTS -->
						
							<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
								<tr>
									<td class="boldHeader">Accounts:</td>
									<td align="right"></td>
								</tr>
							</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td></td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
								</tr>
								<tr>
									<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
										
										<? foreach ($accounts->result() as $a) { ?>
										
											<div id="account_<?= $a->accountid ?>">
			
												<table border="0" cellpadding="0" cellspacing="0" width="290" align="center">
													
													<tr id="invTerritoryRowTr_<?= $a->accountid ?>" class="">
														<td style="width:51px;" valign="top"><img src="<?=base_url();?>/resources/images/accountsIcon.gif" alt="accountsIcon" width="30" height="25" border="0" style="margin-top:4px;margin-left:3px;"><img src="<?=base_url();?>/resources/images/x.gif" width="1" height="36" align="top"/></td>
														<td id="invTerritoryRowTd_<?= $a->accountid ?>" class="invGroupText"><?= $a->account ?></td>
														
														<? if (checkPerms('can_mark_purchased')) { ?>
													
															<td align="right"><input type="button" class="btnAcctPurchase" value=" PURCHASE / HOLD " onclick="openAccountsWindow(<?= $product->opm_productid ?>,<?= $a->accountid ?>)" />&nbsp;&nbsp;&nbsp;</td>
													
														<? } ?>
														
													</tr>
													
													<tr>
														<td colspan="3"><img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1"></td>
													</tr>
													
												</table>
											
											</div>
										
										<? } ?>
										
										
										
										<!--<div class="accountsItem" id="account_1">Hot Topic</div>	
										<img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1">						
										<div class="accountsItem" id="account_1">Spencer's</div>-->	
								
								
									</td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
								</tr>
							</table>
					
						
						<!-- END RETAIL ACCOUNTS-->
				<? } ?>
			
			<? } ?>
			
						
			</div>
						
		</td>
	</tr>
</table>

<br /><br />

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
				
			
		
		</td>
		
	</tr>
</table>




	
	