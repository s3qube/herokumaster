
<form name="permform" method="post" action="<?= base_url(); ?>usergroups/doAdd">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<h3 class="userField">Parent Usergroup:</h3>
				
				<select name="parentusergroupid" class="userField" <? checkDisabled(); ?>>
				
					<option value="0">Please Select...</option>
				
					<? foreach ($usergroups as $key=>$ug) { ?>
					
						<option value="<?= $key ?>"><?= $ug ?></option>
					
					<? } ?>
				
				</select>
				
				<br /><br />
	
				<h3 class="userField">Usergroup Name:</h3>
								
				<input type="text" name="usergroup" value="" class="userField" />
			
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
		</tr>
	</table>
</form>
