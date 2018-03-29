<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>users/submit">
		
		<table border="0" cellpadding="3" width="95%">
			<tr>
				<td>Usergroup :</td>
				<td>
					
					<select name="usergroupid" class="searchField">
			
						<option value="0" <? if ($args['usergroupid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($usergroups as $key=>$data) { ?>
						
							<option value="<?= $key ?>" <? if ($args['usergroupid'] == $key) echo "SELECTED"; ?>><?= $data ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
				
				<td>&nbsp;&nbsp;</td>
				
			</tr>
			
					<tr>
						<td colspan="2">
							Include Parents : 
							<input type="checkbox" name="includeparents" <?= ($args['includeparents'] ? "CHECKED" : "UNCHECKED")?>/>
						</td>
						
						<td>&nbsp;&nbsp;</td>
						
						<td colspan="2">
							Show Only Active: 
							<input type="checkbox" name="showonlyactive" <?= ($args['showonlyactive'] ? "CHECKED" : "UNCHECKED")?>/>
						</td>
				
					</tr>
					
			<tr>
				
				<td colspan="10" align="right"><input type="submit" value="search"></td>
			
			</tr>
		</table>		

	</form>

</div>