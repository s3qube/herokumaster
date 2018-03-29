<script language="javascript">

	$(document).ready(function() {
		
		$("#chgPassForm").validate();
		
	});

	function moveProperty() {
	
		var selProperties = document.getElementById("propertyIDs");
		var selUserProperties = document.getElementById("userPropertyIDs");
		
		//selectedID = document.getElementById("propertyIDs").selectedIndex;

		
		for (i=0;i<selProperties.options.length;i++) {
		
			if (selProperties.options[i].selected) {
			
				strText = selProperties.options[i].text; //accesses value attribute of 1st option
				intValue = selProperties.options[i].value; 
		
				if (AddSelectOption(selUserProperties, strText, intValue, true)) {
				
				} else {
					return false;
				}
					

			
			}		
							
		}
		
		selectAll();
	
	}
	
	function deleteProperty() {
	
		var selUserProperties = document.getElementById("userPropertyIDs");
		
		arrItemsToRemove = new Array();
		
		for (i=0;i<selUserProperties.options.length;i++) {
		
			if (selUserProperties.options[i].selected) {
				
				arrItemsToRemove.push(selUserProperties.options[i].value);
			
			}		
							
		}
		
		for (i=0;i<arrItemsToRemove.length;i++) {
		
			//alert(arrItemsToRemove[i]);
		
			for (y=0;y<selUserProperties.options.length;y++) {
		
				if (selUserProperties.options[y].value == arrItemsToRemove[i]) {
					
					selUserProperties.remove(y);
				
				}		
							
			}
				
		}
	
		selectAll();
		
	}
	
	
	 //$('theBigFatSelectList');

	//AddSelectOption(theSelectList, "My Option", "123", true);
	
	function AddSelectOption(selectObj, text, value, isSelected) {
		
		// check and make sure id is not already there.
		
		for (x=0;x<selectObj.options.length;x++) {
		
			if (selectObj.options[x].value == value) {
				
				alert("That property is already in the list!");
				return false;
			
			}
				
		
		}

		
		if (selectObj != null && selectObj.options != null) {
		
			selectObj.options[selectObj.options.length] = new Option(text, value, false, isSelected);
			return true;
		
		} else {
		
			//alert("null issue");
			
		}
	
	}
	
	function selectAll() {
	
		var selectObj = document.getElementById("userPropertyIDs");
		
		// check and make sure id is not already there.
		
		for (i=0;i<selectObj.options.length;i++) {
		
			selectObj.options[i].selected = true;
		
		}
		
		return true;
			
	}
	
	function checkForm() {
	
		
	
	}

</script>


<? //if (checkPerms('can_change_password')) { ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	
	<form name="chgPassForm" id="chgPassForm" method="post" action="<?= base_url(); ?>mypreferences/changePass/">

	
	<tr>
		<td>
		
			<? if (!$this->userinfo->password_changed) { ?>
			
				<h2 style="color:red">All OPM users are required to change their password on or after 11/15/2013!</h1>
			
			<? } ?>
			
			<? if ($this->userinfo->password_reset) { ?>
			
				<h2 style="color:red">You must set a new OPM password to continue!</h1>
			
			<? } ?>
		
			<h3 class="userField">Change Password:</h3>
			
			Note: OPM now requires a password of at least 8 characters, with at least one capital letter and at least one number.
			
			<br />
			
			<h3>Enter Current Password:</h3>
						
			<input type="password" name="currentPassword" class="userField required" <? checkDisabled(); ?>/>
					
			<h3>Enter New Password:</h3>
						
			<input type="password" name="newPassword" class="userField required" <? checkDisabled(); ?>/>	
			
			<h3>Confirm New Password:</h3>
						
			<input type="password" name="newPasswordConf" class="userField required" <? checkDisabled(); ?>/>	
			
			
			
		</td>
	</tr>
	
	<tr>
		<td colspan="4" align="right"><br /><br /><input type="submit" value="Change Password" style="margin-right:200px;"></td>
	</tr>
	
	</form>
	</table>

<?// } ?>

<? if (checkPerms('can_edit_preferences')) { ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	
		<tr>
			<td>
				<h3>General Preferences:</h3>
				
			</td>
		</tr>
	
	</table>

	<form name="permform" method="post" action="<?= base_url(); ?>mypreferences/save/" onsubmit="return selectAll();">
		
		<input type="hidden" name="userid" value="<?= $this->userinfo->userid ?>">
	
		<table border="0" class="editPerms">
		
	<? 
		$currentPrefGroup = "";
		foreach ($preferences->result() as $p) { 
	
	?>
			
			<? if ($currentPrefGroup != $p->prefgroup) { ?>
				
				<tr>
					<th class="editPerms"><?= $p->prefgroup ?></th>
				</tr>
			
			<? } ?>
		
				
			<input type="hidden" name="prefs[<?=$p->prefid?>]" value="true">
	
			<tr>
				<td class="editPerms"><?= $p->preftext ?></td>
				<td class="editPerms"><input type="checkbox" name="chkbox[<?=$p->prefid?>]" <? if ($p->has_pref) echo "CHECKED" ;?>></td>
			</tr>
	
	
		
	<? 	
			$currentPrefGroup = $p->prefgroup;
		} 
	
	?>
			<tr>
				<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
			</tr>
	
		</table>
	
<? } ?>




<table border="0" class="editPerms">
	
	<tr>
		<th class="editPerms" colspan="4">Limit above emails to the following properties:</td>
	</tr>
	
	<tr>
		<td colspan="4">(if list is left empty, you will receive emails for all properties)</td>
	</tr>
	
	<tr>
	
		<td>
			<table border="0">
				<tr>
					<td>
						<h3>All Properties:</h3>
						<select name="propertyIDs[]" id="propertyIDs" class="prefPropSelect" MULTIPLE>
							
							<? foreach ($properties->result() as $p) { ?>
							
								<option value="<?= $p->propertyid ?>" <? if (1==0) echo "SELECTED"; ?>><?= $p->property ?></option>
							
							<? } ?>
							
						</select>
				
					</td>
					<td>
						
						<input type="button" value="ADD &gt;&gt;" onclick="moveProperty();"><br /><br />
						<input type="button" value="REMOVE" onclick="deleteProperty();">
												
					</td>
					<td>
						<h3>My Properties:</h3>
						<select name="userPropertyIDs[]" id="userPropertyIDs" class="prefPropSelect" MULTIPLE>
							
							<? foreach ($userProperties->result() as $p) { ?>
							
								<option value="<?= $p->propertyid ?>" SELECTED><?= $p->property ?></option>
							
							<? } ?>
							
						</select>
						
					</td>
				</tr>
			</table>
		</td>
		
	</tr>
	
	<tr>
		<td colspan="4" align="right"><br /><br /><input type="submit" value="save"></td>
	</tr>
	
</table>
</form>
					
					
