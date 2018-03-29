

<form name="userform" action="<?= base_url(); ?>accounts/save" method="post" enctype="Multipart/Form-Data">
	
	<? if (isset($a->accountid)) { ?>
		<input type="hidden" name="accountid" value="<?= $a->accountid ?>">
	<? } ?>
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
				<h3 class="userField">Account Name</h3>
				
				<input type="text" name="account" value="<?= (isset($a->account) ? $a->account : null) ?>" class="userField" <? checkDisabled(); ?>/>
					
			</td>
		</tr>
	</table>
	
	<br /><br />
	
	<table width="600" cellpadding="0" cellspacing="0" border="0">
	
		<? if (!isset($addMode)) {  // edit mode ?>
		
			<tr>
				<td align="right"><input type="submit" class="invoiceBtn" value="Save" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } else { ?>
		
			<tr>
				<td align="right"><input type="submit" value="Add User (email will be sent)" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } ?>
	
	</table>
	

</form>

<br />	
	