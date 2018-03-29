
<form name="permform" method="post" action="<?= base_url(); ?>offices/doAdd">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<h3 class="userField">Parent Usergroup:</h3>
				
				<select name="parentOfficeId" class="userField" <? checkDisabled(); ?>>
				
					<option value="0">Please Select...</option>
				
					<? foreach ($offices as $key=>$of) { ?>
					
						<option value="<?= $key ?>"><?= $of ?></option>
					
					<? } ?>
				
				</select>
				
				<br /><br />
	
				<h3 class="userField">Office Name:</h3>
								
				<input type="text" name="office" value="" class="userField" />
			
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
		</tr>
	</table>
</form>
