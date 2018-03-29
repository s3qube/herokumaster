

<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>invoices/submitReport">
		<input type="hidden" name="exportExcel" value="0">
		
		<div style="float:right; margin-bottom:6px;" id="reportSearchFieldHider"><a href="#" onclick="$('#billingSearchFields').toggle('slow'); return false;">Hide Search Fields</a></div>
		
		<div id="billingSearchFields">
		
			<table border="0" cellpadding="3" width="95%">
			
				<tr>
				
					<td valign="top">Bravado User :</td>
					<td valign="top">
						
						<select name="ownerids[]" class="searchField" MULTIPLE>
					
							<option value="0" <? if (sizeof($args['ownerids']) == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($productManagers->result() as $u) { ?>
							
								<option value="<?= $u->userid ?>" <? if (in_array($u->userid, $args['ownerids'])) echo "SELECTED" ; ?>><?= $u->username ?></option>
							
							<? } ?>
						
						</select>
						
					</td>
				
					<td>&nbsp;&nbsp;</td>
					
					<td valign="top">Property:</td>
					<td valign="top">
						
						<select name="propertyids[]" class="searchField" MULTIPLE>
				
							<option value="0" <? if (sizeof($args['propertyids']) == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($properties->result() as $p) { ?>
							
								<option value="<?= $p->propertyid ?>" <? if (in_array($p->propertyid, $args['propertyids'])) echo "SELECTED"; ?>><?= $p->property ?></option>
							
							<? } ?>
							
						</select>
						
					</td>				
					
				
					
				</tr>
				
				<tr>
	
					<td valign="top">From User :</td>
					<td valign="top">
						
						<select name="userids[]" class="searchField" MULTIPLE>
						
							<option value="0" <? if (sizeof($args['userids']) == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($users->result() as $u) { ?>
							
								<option value="<?= $u->userid ?>" <? if (in_array($u->userid, $args['userids'])) echo "SELECTED" ; ?>><?= $u->username ?> (<?= $u->usergroup ?>)</option>
							
							<? } ?>
						
						</select>
						
					</td>
					
				
					<td>&nbsp;</td>
				
					<td valign="top">Attention :</td>
						<td valign="top">
							
							<select name="attentionids[]" class="searchField" MULTIPLE>
						
								<option value="0" <? if (sizeof($args['attentionids']) == 0) echo "SELECTED"; ?>>SHOW ALL</option>
								
								<? foreach ($productManagers->result() as $u) { ?>
								
									<option value="<?= $u->userid ?>" <? if (in_array($u->userid, $args['attentionids'])) echo "SELECTED" ; ?>><?= $u->username ?></option>
								
								<? } ?>
							
							</select>
							
						</td>
					
				
				</tr>
				<tr>
				
					<td valign="top">Status :</td>
					<td valign="top">
						
						<select name="statusids[]" class="searchField" MULTIPLE>
						
							<option value="0" <? if (sizeof($args['statusids']) == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($statuses as $key=>$status) { ?>
							
								<option value="<?= $key ?>" <? if (in_array($key,$args['statusids'])) echo "SELECTED"; ?>><?= $status ?></option>
							
							<? } ?>
						
						</select>
						
					</td>
				
					<td>&nbsp;&nbsp;</td>
					
					<td valign="top">Start Date :</td>
					<td valign="top">
						
						<input type="text" class="format-m-d-y divider-dash highlight-days-12 searchField" id="dp-normal-b1" name="startdate" value="<?= ($args['startdate']) ? date("m-d-Y",$args['startdate']) : "0";?>" maxlength="10" />							

					</td>

					
				</tr>
				
				<tr>
				
				<td colspan="2">
					<br /><br />
					Group By Property : 
					<input type="checkbox" name="groupByProperty" <?= ($args['groupByProperty'] ? "CHECKED" : "UNCHECKED")?>/>
				
				</td>
				
				<td>&nbsp;&nbsp;</td>
					
					<td valign="top">End Date :</td>
					<td valign="top">
						
						<input type="text" class="format-m-d-y divider-dash highlight-days-12 searchField" id="dp-normal-b2" name="enddate" value="<?= ($args['enddate']) ? date("m-d-Y",$args['enddate']) : "0";?>" maxlength="10" />							

					</td>
				
			</tr>
				
				<tr>
					<td colspan="10" align="right"><br /><input type="submit" onclick="document.searchForm.exportExcel.value='0';" value="search"></td>
				</tr>
			</table>
		
		</div>
		
		<br />
		
</div>