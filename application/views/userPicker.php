<html>

	<head>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
		<script type="text/javascript" src="<?=base_url();?>resources/js/mootools-release-1.11.js"></script>
		<script type="text/javascript" src="<?=base_url();?>resources/js/opm_scripts.js"></script>

		<script language="javascript">
		
			function populateAndClose() {
			
				<? if ($mode == 'screenPrinters' || $mode == 'separators') { ?>
			
					strUGIDs = "";
					
					// iterate through all checkboxes on page
	
					var objCheckBoxes = document.usergroupsForm.elements['ugIDs'];
					
					var countCheckBoxes = objCheckBoxes.length;
	
					if(!countCheckBoxes) {
				
						strUGIDs = document.usergroupsForm.ugIDs.value;
				
					} else {
					
						for(var i = 0; i < countCheckBoxes; i++) {
										
							if(objCheckBoxes[i].checked)
								strUGIDs += objCheckBoxes[i].value + ",";
						
						}
						
						// remove last comma
						
						strUGIDs = strUGIDs.substring(0,strUGIDs.length-1);
					
					}
					
					//alert(strUGIDs);
					parent.document.sendEmailForm.recipientUGs.value = strUGIDs;
					
				<? } ?>
				
				parent.document.sendEmailForm.notificationComment.value = document.usergroupsForm.notificationComment.value;
				parent.document.sendEmailForm.submit();
				parent.Shadowbox.close();
				
									
			
			}
			
		
		</script>

	</head>
	
	<body bgcolor="#ffffff" style="margin-left:10px;">
		
		<form name="usergroupsForm">
		
		<? if ($mode == 'screenPrinters' || $mode == 'separators') { // we need user pickers for these email types.?>
		
			<h3 class="userField">Send Email To:</h3>

				<div class="selectField" style="height:150px;">
					
					<? foreach ($usergroups->result() as $u) { ?>
						
						<div id="div_ug_id_<?= $u->usergroupid ?>" class="selectItem <? echo "selectItemOn" ?>">
						
							<input class="selectItemChkbox" type="checkbox" id="ug_id_<?= $u->usergroupid ?>" value="<?= $u->usergroupid ?>" name="ugIDs" onChange="opm.changeOptionColor('ug_id',<?= $u->usergroupid ?>);" <? echo "CHECKED"; ?> />
							<label for="ug_id_<?= $u->usergroupid ?>"><?= $u->usergroup ?></label>
						
						</div>
				
					<? } ?>
				
				</div>
				<br /><br />
			
		<? } else { ?>
		
		
		<? } ?>
			
			<h3 class="userField">Add Comment:</h3>
			
			<textarea name="notificationComment" style="width:300px;height:100px;"></textarea>
			
			<br />
			
			<div align="right"><input type="button" name="submitBtn" value="Send Email" onclick="populateAndClose();" style="margin-right:10px;" /></div>
			
		</form>
	</body>

</html>