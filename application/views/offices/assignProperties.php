

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
									
									
						<div class="selectField" style="height:400px;">
							
							<? foreach ($properties->result() as $p) { ?>
									
								<div id="div_property_id_<?= $p->propertyid ?>" class="selectItem <? if ($p->isassigned) echo "selectItemOn" ?>">
								
									<input class="selectItemChkbox" type="checkbox" id="property_id_<?= $p->propertyid ?>" name="propertyIDs[<?= $p->propertyid ?>]" onChange="opm.changeOptionColor('property_id',<?= $p->propertyid ?>);" <? if ($p->isassigned) echo "CHECKED"; ?> />
									<label for="property_id_<?= $p->propertyid ?>"><?= $p->property ?></label> 
								
								</div>
						
							<? } ?>
						
						</div>
						
					<? } ?>
					
					<br /><br />
					
					<!-- / Designers -->
					
					<div style="text-align:right;">
		
						<input type="submit" value="Save">&nbsp;&nbsp;&nbsp;
						
					</div>
					
				<br /><br />
				
				
				
				</form>
			</td>
			
		</tr>
		
</table>