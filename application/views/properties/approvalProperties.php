
		
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
	
	<h3 class="userField">Current Approval Properties for <?= $user->username ?>:</h3>
	
	<form name="approvalPropertiesForm" method="post" action="<?= base_url(); ?>users/saveApprovalProperties">
	
		<input type="hidden" name="userid" value="<?= $user->userid ?>" />
		
		<? if ($userProperties->num_rows() > 0) { ?>
		
			<table border="0" cellpadding="3" cellspacing="1" class="tbl_user_properties">
			
				<tr>
					<th class="tbl_user_properties">Property</th>
					<th class="tbl_user_properties">Approval Required?</th>
					<th class="tbl_user_properties">Begin Date</th>
					<th class="tbl_user_properties">End Date</th>
				</tr>
			
			<? foreach ($userProperties->result() as $up) { ?>
			
				<input type="hidden" name="arrLineIDs[<?= $up->lineid ?>]" value="1">
				
				<tr class="tbl_user_properties">
				
					<td class="tbl_user_properties"><?= $up->property ?></td>
					<td class="tbl_user_properties"><input type="checkbox" name="approvalrequired[<?= $up->lineid ?>]" <? if ($up->approvalrequired) echo "CHECKED";?>></td>
					<td class="tbl_user_properties"><input type="text" class="w8em format-m-d-y divider-dash highlight-days-12" id="dp-normal-b<?= $up->propertyid ?>" name="begindate[<?= $up->lineid ?>]" value="<?= ($up->begindate) ? date("m-d-Y",$up->begindate) : "0";?>" maxlength="10" style="width:80px;" /></td>
					<td class="tbl_user_properties"><input type="text" class="w8em format-m-d-y divider-dash highlight-days-12" id="dp-normal-e<?= $up->propertyid ?>" name="enddate[<?= $up->lineid ?>]" value="<?= ($up->enddate) ? date("m-d-Y",$up->enddate) : "0";?>" maxlength="10" style="width:80px;" /></td>
				</tr>
			<? } ?>
			
				<tr>
					<td class="tbl_user_properties" colspan="10" align="right"><input type="submit" name="save" value="Save"></td>
				</tr>
			
			</table>
		
		<? } else { ?>
		
			
			<h4>No Approval Properties.</h4>
			
		
		<? } ?>
	
	</form>
	
	<br /><br />
	
	<h3 class="userField">Add Approval Property:</h3>
	
	<form name="addPropertyForm" method="post" action="<?= base_url(); ?>users/addApprovalProperty">
		<input type="hidden" name="userid" value="<?= $user->userid ?>" />
	
		Property : 
		
		<select name="propertyid">
			
			<option value="0">Please Select...</option>
			
			<? foreach ($properties->result() as $p) { ?>
			
				<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
			
			<? } ?>
			
		</select>
		
		<input type="submit" name="addproperty" value="Add" />
	
	</form>