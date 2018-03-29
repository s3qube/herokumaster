
<? //print_r($user) ?>

<form name="userform" action="<?= base_url(); ?>users/savePermissions" method="post" enctype="Multipart/Form-Data">

	<input type="hidden" name="userid" value="<?= $user->userid ?>">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<h3 class="userField">Assign Permissions</h3>
					
					<br />
					
					<table border="0" class="editPerms">
	
						<? 
							// for alt row classes
							
							$rowClass = "permRow";
							
							$currentPermGroup = "";
							
							foreach ($permissions->result() as $p) { 
						
						?>
								
								<? if ($currentPermGroup != $p->permgroup) { ?>
									
									<tr>
										<th class="editPerms"><?= $p->permgroup ?></th>
									</tr>
								
								<? } ?>
							
								<? if ($p->hasperm) { ?>
								
									<tr class="<?=$rowClass?>">
										<td class="editPermsInherited"><?= $p->permtext ?></td>
										<td class="editPermsInherited">(inherited)</td>
								
									</tr>
								
								
								<? } else { ?>
									
									<input type="hidden" name="perms[<?=$p->permid?>]" value="true">
							
									<tr class="<?=$rowClass?>">
										<td class="editPerms"><?= $p->permtext ?></td>
										<td class="editPerms"><input type="checkbox" name="chkbox[<?=$p->permid?>]" <? if ($p->haspermexplicit) echo "CHECKED" ;?>></td>
									</tr>
								
								<? } ?>
						
							
						<? 	
								$currentPermGroup = $p->permgroup;
								
								// alt rows
								
								if ($rowClass == 'permRow')
									$rowClass = 'permRowAlt';
								else
									$rowClass = 'permRow';
								
							} 
						
						?>
					
									
			</td>
		</tr>
	</table>
	
	<br />
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
	
		<? if (!isset($addMode)) {  // edit mode ?>
		
			<tr>
				<td align="right"><input type="submit" value="Save" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } else { ?>
		
			<tr>
				<td align="right"><input type="submit" value="Add User (email will be sent)" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } ?>
	
	</table>
	

</form>

<br />	
	