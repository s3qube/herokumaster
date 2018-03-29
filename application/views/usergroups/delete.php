

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
				<form name="deleteForm" action="<?= base_url(); ?>usergroups/delete" method="post">
		
					
					<select id="deleteUsergroupID" name="usergroupID" class="delUGSelect">
						
						<option value="0">Please Select...</option>
						
						<? /* foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
						
						<? } */?>
						
						<? foreach ($usergroups as $key=>$ug) { ?>
												
							<option value="<?= $key ?>" <? if ($usergroupID == $key) echo "SELECTED" ?>><?= $ug ?></option>
						
						<? } ?>
						
				
					</select>

					&nbsp;&nbsp;
										
					<input type="submit" value="DELETE" class="invoiceBtn">&nbsp;&nbsp;&nbsp;
	
					<br /><br />
					
					<? if (isset($errors)) { ?>
					
						<h3 class="delUGError">Usergroup could not be deleted. The following errors were encountered:</h3>
						
						<? foreach ($errors as $e) { ?>
						
							
							<?= $e ?><br />
						
						
						<? } ?>
					
					
					<? } ?>
				
				
				</form>
			</td>
			
		</tr>
		
</table>