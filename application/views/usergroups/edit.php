
<form name="permform" method="post" action="<?= base_url(); ?>usergroups/save/<?=$id?>">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
	
				<h3 class="userField">Usergroup Name:</h3>
								
				<input type="text" name="usergroup" value="<?= $usergroup->usergroup?>" class="userField" />
			
			</td>
		</tr>
	</table>

	<br />

	<table border="0" class="editPerms">
	
<? 
	// for alt row classes
	
	$rowClass = "permRow";
	
	$currentPermGroup = "";
	
	foreach ($usergroup->permissions->result() as $p) { 

?>
		
		
	
		
		<? if ($currentPermGroup != $p->permgroup) { ?>
			
			<tr>
				<th class="editPerms"><?= $p->permgroup ?></th>
			</tr>
		
		<? } ?>
	
		<? if ($p->has_perm_inherited) { ?>
		
			<tr class="<?=$rowClass?>">
				<td class="editPermsInherited"><?= $p->permtext ?></td>
				<td class="editPermsInherited">(inherited)</td>
		
			</tr>
		
		
		<? } else { ?>
			
			<input type="hidden" name="perms[<?=$p->permid?>]" value="true">
	
			<tr class="<?=$rowClass?>">
				<td class="editPerms"><?= $p->permtext ?></td>
				<td class="editPerms"><input type="checkbox" name="chkbox[<?=$p->permid?>]" <? if ($p->has_perm) echo "CHECKED" ;?>></td>
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
		<tr>
			<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
		</tr>

	</table>
</form>
