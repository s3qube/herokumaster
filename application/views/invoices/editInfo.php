

<form name="userform" action="<?= base_url(); ?>invoices/saveInfo" method="post" enctype="Multipart/Form-Data">

	<input type="hidden" name="userid" value="<?= $u->userid ?>">

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Username</h3>
					
					<input type="text" name="username" value="<?= (isset($u->username) ? $u->username : null) ?>" class="userField" <? checkDisabled(); ?>/>
					
					<br /><br />
					
					<? if (checkPerms('can_change_navision_customer_code')) { ?>
					
						<h3 class="userField">Navision Vendor Code</h3>
						
						<input type="text" name="nv_customerid" value="<?= (isset($u->nv_customerid) ? $u->nv_customerid : null) ?>" class="userField" <? checkDisabled(); ?>/>
						
						<br /><br />
					
					<? } else { ?>
					
						<input type="hidden" name="nv_customerid" value="<?= $u->nv_customerid ?>" />
					
					<? }?>
					
					<h3 class="userField">Email Address</h3>
					
					<input type="text" name="login" value="<?= (isset($u->login) ? $u->login : null) ?>" class="userField" <? checkDisabled(); ?>/>
					
					<br /><br />
					
					<? if (!isset($addMode) && checkPerms('can_change_user_passwords')) {  // only show password if in edit mode ?>
					
						<input type="hidden" name="submitPassword" value="true"> <!-- this tells the form handler to look for passwords! -->
						
						<h3 class="userField">Password</h3>
						
						<? if (checkPerms('can_view_passwords')) { ?>
							<input type="text" name="password" value="<?= (isset($u->password) ? $u->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } else { ?>
							<input type="password" name="password" value="<?= (isset($u->password) ? $u->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } ?>
						
						<br /><br />
						
						<h3 class="userField">Confirm Password</h3>
						
						<? if (checkPerms('can_view_passwords')) { ?>
							<input type="text" name="password2" value="<?= (isset($u->password) ? $u->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } else { ?>
							<input type="password" name="password2" value="<?= (isset($u->password) ? $u->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } ?>
						<br /><br />
					
					<? } ?>
					
					<h3 class="userField">Address / Info</h3>
					
					<textarea name="address" class="userField" <? checkDisabled(); ?>><?= (isset($u->address) ? $u->address : null) ?></textarea>
					
					<br /><br />
					
					<h3 class="userField">User Is Active</h3>
					
					<input type="checkbox" name="isactive" <? checkDisabled(); ?> <?= (isset($u->isactive) && $u->isactive == 1 ? "CHECKED" : null) ?>>				
	
			
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
				
				<table width="25%" cellpadding="0" cellspacing="0" border="0">
				
					<tr>
						<td>
					
						
							<h3 class="userField">Invoice Logo Image <small>(450x100 pixels)</small></h3>
							
				
							<? if ($u->invoiceimage_path) { ?>

								<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/0/<?= $u->userid ?>" alt="" />
							
							<? }  ?>
					
							
							<br /><br />
							
							<input type="file" name="invoiceImage" <? checkDisabled(); ?>/>
							
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
	