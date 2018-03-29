<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>grabsheets/submit">
		
		<table border="0" cellpadding="3" width="95%">
			<tr>
				<td>Group :</td>
				<td>
					
					<select name="grabsheetgroupid" class="searchField">
			
						<option value="0" <? if ($args['grabsheetgroupid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($grabsheetGroups->result() as $g) { ?>
						
							<option value="<?= $g->grabsheetgroupid ?>" <? if ($args['grabsheetgroupid'] == $g->grabsheetgroupid ) echo "SELECTED"; ?>><?= $g->grabsheetgroup ?></option>
						
						<? } ?>
						
					</select>
					&nbsp;<a href="#" onclick="opm.showHideDiv('newGroupArea'); return false;" class="regBlueLink"><small>Create New Group</small></a>
				</td>
				
				<td>&nbsp;&nbsp;</td>
				
					<td></td>
					<td align="right">

						

					</td>
				
			</tr>
			<tr>
				<td>Includes Property :</td>
				<td>
					
					<select name="propertyid" class="searchField">
			
						<option value="0" <? if ($args['propertyid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>" <? if ($args['propertyid'] == $p->propertyid ) echo "SELECTED"; ?>><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
				</td>
				
				<td>&nbsp;&nbsp;</td>
				
					<td></td>
					<td align="right">

						

					</td>
				
			</tr>
			
			<tr>
				
				<td colspan="10">
					<div id="newGroupArea">
						<br />
						New Group Name: <input type="text" class="searchField" name="newGroupName"> <input type="submit" name="createGroup" value="Create Group">
					</div>
				</td>
				
			</tr>

			
			<tr>
				<td colspan="10" align="right"><input type="submit" value="search"></td>
			</tr>
		</table>		

	</form>

</div>