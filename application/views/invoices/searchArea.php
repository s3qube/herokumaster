<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>invoices/submit">
		<input type="hidden" name="exportExcel" value="0">
	
		<table border="0" cellpadding="3" width="95%">
		
			<tr>
			
				<td>Bravado User :</td>
				<td>
					
					<select name="ownerid" class="searchField">
				
						<option value="0" <? if ($args['ownerid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($productManagers->result() as $u) { ?>
						
							<option value="<?= $u->userid ?>" <? if ($args['ownerid'] == $u->userid) echo "SELECTED" ; ?>><?= $u->username ?></option>
						
						<? } ?>
					
					</select>
					
				</td>
			
				<td>&nbsp;&nbsp;</td>
				
				<td>Includes Prop. :</td>
				<td>
					
					<select name="propertyid" class="searchField">
			
						<option value="0" <? if ($args['propertyid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>" <? if ($args['propertyid'] == $p->propertyid) echo "SELECTED"; ?>><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
				</td>				
				
			
				
			</tr>
			
			<tr>
				
				<? if (checkPerms('can_view_all_invoices')) { ?>
				
					<td>From User :</td>
					<td>
						
						<select name="userid" class="searchField">
						
							<option value="0" <? if ($args['userid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($users->result() as $u) { ?>
							
								<option value="<?= $u->userid ?>" <? if ($args['userid'] == $u->userid) echo "SELECTED" ; ?>><?= $u->username ?> (<?= $u->usergroup ?>)</option>
							
							<? } ?>
						
						</select>
						
					</td>
				
				<? } else { ?>
				
					<td></td>
					<td></td>
					<input type="hidden" name="userid" value="<?= $this->userinfo->userid ?>" />
				
				<? } ?>
			
				<td>&nbsp;</td>
			
				<td>Attention :</td>
					<td>
						
						<select name="attentionid" class="searchField">
					
							<option value="0" <? if ($args['attentionid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($productManagers->result() as $u) { ?>
							
								<option value="<?= $u->userid ?>" <? if ($args['attentionid'] == $u->userid) echo "SELECTED" ; ?>><?= $u->username ?></option>
							
							<? } ?>
						
						</select>
						
					</td>
				
					<td>&nbsp;&nbsp;</td>
			
			</tr>
			<tr>
			
				<td>Status :</td>
				<td>
					
					<select name="statusid" class="searchField">
					
						<option value="0" <? if ($args['statusid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($statuses as $key=>$status) { ?>
						
							<option value="<?= $key ?>" <? if ($args['statusid'] == $key) echo "SELECTED"; ?>><?= $status ?></option>
						
						<? } ?>
					
					</select>
					
				</td>
			
				<td>&nbsp;&nbsp;</td>
			
				
				
				<td>Show Deleted :</td>
				<td><input type="checkbox" name="showdeleted" <?= ($args['showdeleted'] ? "CHECKED" : "UNCHECKED")?>/></td>
				
			
			</tr>
			
			<tr>
			
				
				
				
				
				
				
			
				
			</tr>
			
			<tr>
			
				
				<td>Company:</td>
				
				<td>
					
					<select name="companyid" class="searchField">
					
						<option value="0" <? if ($args['companyid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($companies->result() as $c) { ?>
						
							<option value="<?= $c->id ?>" <? if ($args['companyid'] == $c->id) echo "SELECTED"; ?>><?= $c->name ?></option>
						
						<? } ?>
					
					</select>
					
				</td>
				
				<td></td>
				
				
			</tr>

			<tr>
			
				
				<td>Invoices Per Page:</td>
				
				<td>
						
					<input name="perPage" class="searchField" value="<?= ($args['perPage'] ? $args['perPage'] : $this->config->item('searchPerPage')) ?>">

				
				</td>
				
				<td></td>
				
				<td>Invoice #:</td>
				
				<td>
						
					<input name="invoiceid" class="searchField" value="" />

				
				</td>
				
				
			</tr>
			
	
			<tr>
				<td colspan="10" align="right"><br /><input type="submit" onclick="document.searchForm.exportExcel.value='0';" value="search"></td>
			</tr>
		</table>

</div>