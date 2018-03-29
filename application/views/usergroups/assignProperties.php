
<script language="javascript">
	
	$(document).ready(function() {
	
		$(".chzn-select").chosen(function() {
 			
 			//updateProductLines();
 
		});

	});
	
</script>


<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
				<form name="assignForm" action="<?= base_url(); ?>usergroups/savePropertyAssignments" method="post">
				
					<script language="javascript">
					
						function changeUsergroupRedirect() {
						
						
						
							usergroupID = document.getElementById('assignPropsUsergroupID').value;
							location.href = base_url + "usergroups/assignProperties/" + usergroupID;
						
						}
						
						
					
					</script>
				
					
					<div id=""></div>
				
					
					<select id="assignPropsUsergroupID" name="usergroupID" onchange="changeUsergroupRedirect();">
						
						<option value="0">Please Select...</option>
						
						<? /* foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
						
						<? } */?>
						
						<? foreach ($usergroups as $key=>$ug) { ?>
												
							<option value="<?= $key ?>" <? if ($usergroupID == $key) echo "SELECTED" ?>><?= $ug ?></option>
						
						<? } ?>
						
				
					</select>
					
					<br /><br />
					
					<? if ($usergroupID != 0) { ?>
					
					<h3 class="userField">Properties:</h3>
												
									
						<select name="propertyIDs[]" class="chzn-select" style="height:400px;" MULTIPLE>
							
							<? foreach ($properties->result() as $p) { ?>
			
									<option value="<?= $p->propertyid ?>" <? if ($p->isassigned) echo "SELECTED"; ?>><?= $p->property ?></option>
									
							<? } ?>
						
						</select>
						
						
			
						
					
					
					<br /><br />
					
					<!-- / Designers -->
					
					<div style="text-align:left;">
		
						<input type="submit" value="Save Changes">&nbsp;&nbsp;&nbsp;
						
					</div>
					
				<br /><br />
				
				<? } ?>
				
				
				
				</form>
			</td>
			
		</tr>
		
</table>