

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
						
							
							<input type="button" name="sepsReady" onclick="document.sendEmailForm.action.value = 'sepsReady'; openUserPicker(<?=$product->opm_productid?>,'screenPrinters');" value="Send Separations Print Ready Email"> <br /> <br />
							<input type="button" name="sepsUpdated" onclick="document.sendEmailForm.action.value = 'sepsUpdated'; openUserPicker(<?=$product->opm_productid?>,'screenPrinters');" value="Send Separations Updated Email">
							
							<br /> <br /> <br />
							
							<input type="button" name="artworkReady" onclick="document.sendEmailForm.action.value = 'artworkReady'; openUserPicker(<?=$product->opm_productid?>,'separators');" value="Send Artwork Ready To Separate Email"> <br /> <br />
							<input type="button" name="artworkUpdated" onclick="document.sendEmailForm.action.value = 'artworkUpdated'; openUserPicker(<?=$product->opm_productid?>,'separators');" value="Send Artwork Updated Email">
							
							<br /> <br /> <br />
							
							<input type="button" name="newDesignProject" onclick="document.sendEmailForm.action.value = 'newDesignProject'; document.sendEmailForm.submit();" value="Send New Design Project Email"> <br /> <br />
							
						</td>
					</tr>
				</table>
				
			</form>
		
		</td>
		
		<td valign="top" align="right">
		
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
				
				<!-- RETAIL ACCOUNTS -->
				<!--
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
								
								
								
								<div class="accountsItem" id="account_1">Hot Topic</div>	
								<img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1">						
								<div class="accountsItem" id="account_1">Spencer's</div>	
						
						
							</td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
						</tr>
					</table>
				-->
				
				<!-- END RETAIL ACCOUNTS-->
				
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




	
	