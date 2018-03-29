<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>users/submit">
		
		<table border="0" cellpadding="3" width="95%">
			<tr>
				<td>Usergroups :</td>
				<td>
					
					<select name="usergroupid" class="searchField">
			
						<option value="0" <? if ($args['usergroupid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($usergroups as $key=>$data) { ?>
						
							<option value="<?= $key ?>" <? if ($args['usergroupid'] == $key) echo "SELECTED"; ?>><?= $data ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
				
				<td>&nbsp;&nbsp;</td>
				
					<td>Approval Contact For :</td>
					<td>

						<select name="appPropertyID" class="searchField">
			
							<option value="0" <? if ($args['appPropertyID'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($properties->result() as $p) { ?>
							
								<option value="<?= $p->propertyid ?>" <? if ($args['appPropertyID'] == $p->propertyid) echo "SELECTED"; ?>><?= $p->property ?></option>
							
							<? } ?>
							
						</select>

					</td>
				
			</tr>
			
			<tr>
				<td>Username :</td>
				<td>
					
					<input type="text" name="username" class="searchField" value="<?= ($args['username'] ? $args['username'] : null) ?>" />
					
				</td>
				
				 <td>&nbsp;&nbsp;</td>
				<td>Email:</td>
				<td><input type="text" name="login" class="searchField" value="<?= ($args['login'] ? $args['login'] : null) ?>" /></td>
				
				
			</tr>
			
			<tr>
			
				<td>Permission :</td>
				<td colspan="4">
					
 					<select name="permissionid" id="permissionid" style="width:300px;">
					
					<option value="0" <? if ($args['permissionid'] == '') echo "SELECTED"; ?>>SELECT PERMISSION</option>
           			
						<? foreach ($permissions->result() as $perm) { ?>
						
							<option value="<?= $perm->permid ?>" <? if ($args['permissionid'] == $perm->permid) echo "SELECTED"; ?>><?= $perm->permtext ?></option>
            			
            			<? } ?>
          			
          			</select>					
			
				</td>

		
		  </tr>
			
			<tr>
				<td colspan="2">
				Show Deactivated : 
				<input type="checkbox" name="showDeactivated" <?= ($args['showDeactivated'] ? "CHECKED" : "UNCHECKED")?>/>
				</td>
				
			</tr>
			<tr>
				
				<td colspan="10" align="right"><input type="submit" value="search"></td>
			
			</tr>
		</table>		

	</form>

</div>