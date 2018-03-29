
<? //print_r($user) ?>

<form name="userform" action="<?= base_url(); ?>users/saveInvoicing" method="post" enctype="Multipart/Form-Data">
	

	<input type="hidden" name="userid" value="<?= $user->userid ?>">
	<input type="hidden" name="referer" value="<?= $referer ?>">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
					<? if (checkPerms('can_edit_users')) { ?>
			
				
						<h3 class="userField">Can Use Invoicing System ?</h3>
					
						<input type="checkbox" name="caninvoice" <?= (isset($user->caninvoice) && $user->caninvoice == 1 ? "CHECKED" : null) ?>>
					
						<br /><br />
					
				
					<? }  else { ?>

						<input type="hidden" name="caninvoice" value="on" />
					
					<? } ?>
					
					<!--<h3 class="userField">Billing Info<br /></h3>-->
					
					<h3 class="userField">Street Address</h3>
					
					<textarea name="staddress" class="userField" <? checkDisabled(); ?>><?= (isset($user->staddress) ? $user->staddress : null) ?></textarea>
					
					<br /><br />
					
					<h3 class="userField">Street Address 2</h3>
					
					<textarea name="staddress2" class="userField" <? checkDisabled(); ?>><?= (isset($user->staddress2) ? $user->staddress2 : null) ?></textarea>
					
					<br /><br />
					
					<h3 class="userField">City</h3>
					
					<input name="city" class="userField" size="6" value="<?= (isset($user->city) ? $user->city : null) ?>"<? checkDisabled(); ?> />
					
					<br /><br />
					
					<h3 class="userField">State</h3>
					
					<input name="state" class="userField" size="6" value="<?= (isset($user->state) ? $user->state : null) ?>"<? checkDisabled(); ?> />
					
					<br /><br />
					
					<h3 class="userField">Zip</h3>
					
					<input name="zip" class="userField" size="6" value="<?= (isset($user->zip) ? $user->zip : null) ?>"<? checkDisabled(); ?> />
					
					<br /><br />
					
					<h3 class="userField">User Notes</h3>
			
					<textarea name="notes" class="userField" <? checkDisabled(); ?>><?= (isset($user->notes) ? $user->notes : null) ?></textarea>
			
					<br /><br />
			
					<h3 class="userField">Add Notes To Invoices ?</h3>
					
					<input type="checkbox" name="notestoinvoices" <? checkDisabled(); ?> <?= (isset($user->notestoinvoices) && $user->notestoinvoices == 1 ? "CHECKED" : null) ?>>

							
										
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
				
				<table width="25%" cellpadding="0" cellspacing="0" border="0">
				
					<tr>
						<td>
						
							<h3 class="userField">Invoice Image <small><!--(48x48 pixels)--></small></h3>
							
							<? if ($user->invoiceimage_path) { ?>
							
								<img src="<?= base_url() ?>resources/files/invoiceImages/<?=$user->invoiceimage_path?>" width="250" />
							
							<? } ?>
							
							<br /><br />
							
							<input type="file" name="invoiceImage" <? checkDisabled(); ?>/>
							
							<br /><br />
					
							<h3 class="userField">Tax ID</h3>
							
							<input type="text" name="taxid" value="<?= (isset($user->taxid) ? $user->taxid : null) ?>" class="userField" <? checkDisabled(); ?>/>
							
							<br /><br />
							
							<h3 class="userField">Vat #</h3>
							
							<input type="text" name="vatnumber" value="<?= (isset($user->vatnumber) ? $user->vatnumber : null) ?>" class="userField" <? checkDisabled(); ?>/>
							
							<br /><br />
							
							<h3 class="userField">Submission Fee</h3>
							
							<input type="text" name="submissionfee" value="<?= (isset($user->submissionfee) ? $user->submissionfee : null) ?>" class="userField" <? checkDisabled(); ?>/>
							
							<br /><br />
							
							<h3 class="userField">Is Hourly ?</h3>
							
							<input type="checkbox" name="ishourly" <? checkDisabled(); ?> <?= (isset($user->ishourly) && $user->ishourly == 1 ? "CHECKED" : null) ?>>
							
							<br /><br />
							
							<h3 class="userField">Hourly Rate</h3>
							
							<input type="text" name="hourlyrate" value="<?= (isset($user->hourlyrate) ? $user->hourlyrate : null) ?>" class="userField" <? checkDisabled(); ?>/>
							
							<br /><br />
							
							<h3 class="userField">Currency</h3>
									
							<select name="currencyid" class="userField">
							
								<option value="0" <? if ($user->currencyid == 0) echo "SELECTED"; ?>>Please Select...</option>
							
								<? foreach ($currencies->result() as $c) { ?>
								
									<option value="<?= $c->id ?>" <? if ($user->currencyid == $c->id) echo "SELECTED"; ?>><?= $c->currency ?></option>
								
								<? } ?>
							
							</select>
						
					
						</td>
					</tr>
				
				</table>
				
				
				
				<!--<div id="prodSummaryImage"><div id="detailImageDiv"><img src="<?=base_url();?>/imageclass/view/<?=$product->default_imageid?>" width="350" height="350" border="0" id="detailImage"></div></div>-->
				
				
				
			</td>
		</tr>
	</table>
	
	<br />
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
	
		<? if (!isset($addMode)) {  // edit mode ?>
		
			<tr>
				<td align="right"><input type="submit" value="Save" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } else { ?>
		
			<tr>
				<td align="right"><input type="submit" value="Add User (email will be sent)" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } ?>
	
	</table>
	

</form>

<br />	
	