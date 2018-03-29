<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>invoices/submit">
		<input type="hidden" name="exportExcel" value="0">
	
		<table border="0" cellpadding="3" width="95%">
		
			<tr>
			
				<td>Bravado User :</td>
				<td>
					
					<select name="userid" class="searchField">
				
						<option value="0" <? if ($args['userid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($productManagers->result() as $u) { ?>
						
							<option value="<?= $u->userid ?>" <? if ($args['userid'] == $u->userid) echo "SELECTED" ; ?>><?= $u->username ?></option>
						
						<? } ?>
					
					</select>
					
				</td>
			
				<td>&nbsp;&nbsp;</td>
				
				<td valign="top" rowspan="10">Categories:</td>
				<td rowspan="10">
					
					<input type="checkbox" /><span class="calCat calCat1">Art Approval Deadline</span><br />
					<input type="checkbox" /><span class="calCat calCat2">Cut &amp; Sew Approval</span><br />
					<input type="checkbox" /><span class="calCat calCat3">Shirt Art Approval</span><br />
					<input type="checkbox" /><span class="calCat calCat4">Start Date, Territory</span><br />
					<input type="checkbox" /><span class="calCat calCat5">Trinket Approval</span><br />
				</td>				
				
			
				
			</tr>
		

			<tr>
			
				
				<!--<td>Invoices Per Page:</td>
				
				<td>
						
					<input name="perPage" class="searchField" value="<?= ($args['perPage'] ? $args['perPage'] : $this->config->item('searchPerPage')) ?>">

				
				</td>
				
				<td></td>-->
				
							
				
			</tr>
			
	
			<tr>
				<td colspan="10" align="right"><br /><input type="submit" onclick="document.searchForm.exportExcel.value='0';" value="search"></td>
			</tr>
		</table>

</div>