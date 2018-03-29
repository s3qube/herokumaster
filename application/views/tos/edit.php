
<script language="javascript">
	
	$(document).ready(function() {
		
		$("#tosForm").validate();
	
	});
	
</script>

<? //print_r($tos); ?>

<form name="tosForm" id="tosForm" method="post" action="<?= base_url(); ?>tos/save">

	<? if (isset($tos)) { ?>
	
		<input type="hidden" name="id" value="<?= $tos->id ?>" />
	
	<? } ?>
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<h3 class="userField">Usergroup:</h3>
				
				<select name="usergroupid" class="userField">
				
					<option value="0" <?= (isset($tos) && $tos->usergroupid == 0) ? "SELECTED" : null ?>>ALL USERS</option>
				
					<? foreach ($usergroups as $key=>$ug) { ?>
					
						<option value="<?= $key ?>" <?= (isset($tos) && $tos->usergroupid == $key) ? "SELECTED" : null ?>><?= $ug ?></option>
					
					<? } ?>
				
				</select>
				
				
				<br /><br />
				
				<h3 class="userField">Effective Date:</h3>
								
				<input type="text" class="w8em format-m-d-y divider-dash highlight-days-12" id="dp-normal-b1" name="effectivedate" value="<?= (isset($tos) && $tos) ? date("m-d-Y",$tos->effectivedate) : "0";?>" maxlength="10" style="font-size:13pt;width:300px;" />							

				
				<br /><br />
				
				<h3 class="userField">TOS Name (for reference):</h3>
								
				<input type="text" class="userField" name="tosname" value="<?= (isset($tos->tosname)) ? $tos->tosname : null; ?>"  style="font-size:13pt;width:300px;" />
				
				
				<br /><br />
				
				<h3 class="userField">Is Active:</h3>

				<input type="checkbox" name="isactive" <? checkDisabled(); ?> <?= (isset($tos->isactive) && $tos->isactive == 1 ? "CHECKED" : null) ?>>				

				
				<br /><br />
	
				<h3 class="userField">Terms Of Service Text:</h3>
								
				<textarea name="tostext" class="userField required" style="width:500px;height:350px;"><?= (isset($tos) ? $tos->tostext : null) ?></textarea>
			
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
		</tr>
	</table>
</form>
