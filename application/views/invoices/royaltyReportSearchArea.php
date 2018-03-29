

<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>invoices/submitRoyaltyReport">
		<input type="hidden" name="exportExcel" value="0">
		
		<div style="float:right; margin-bottom:6px;" id="reportSearchFieldHider"><a href="#" onclick="$('#billingSearchFields').toggle('slow'); return false;">Hide Search Fields</a></div>
		
		<br /><br />
		
		<div id="billingSearchFields">
		
			<table border="0" cellpadding="3" width="95%">
			
				<tr>
				
					<td valign="top" class="royaltyReport">Property :</td>
					<td valign="top">
						
						<select name="propertyid"  class="royaltyReport">
				
							<option value="0" <? if ($args['propertyid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($properties->result() as $p) { ?>
							
								<option value="<?= $p->propertyid ?>" <? if ($p->propertyid == $args['propertyid']) echo "SELECTED"; ?>><?= $p->property ?></option>
							
							<? } ?>
							
						</select>
						
					</td>
		
	
					
				</tr>
				
				<tr>
					
					<td valign="top" class="royaltyReport">Start Date :</td>
					<td valign="top">
						
						<input type="text" class="format-m-d-y divider-dash highlight-days-12 royaltyReport" id="dp-normal-b1" name="startdate" value="<?= ($args['startdate']) ? date("m-d-Y",$args['startdate']) : "0";?>" maxlength="10" />							

					</td>

					
				</tr>
				
				<tr>
				
					
					<td valign="top" class="royaltyReport">End Date :</td>
					<td valign="top">
						
						<input type="text" class="format-m-d-y divider-dash highlight-days-12 royaltyReport" id="dp-normal-b2" name="enddate" value="<?= ($args['enddate']) ? date("m-d-Y",$args['enddate']) : "0";?>" maxlength="10" />							

					</td>
				
				</tr>
				
				<tr>
					<td colspan="10" align="right"><br /><input type="submit" onclick="document.searchForm.exportExcel.value='0';" value="search"></td>
				</tr>
			</table>
		
		</div>
		
		<br /><br /> <br /> <br />
		
		<div style="clear:both;">&nbsp;</div>
		
</div>