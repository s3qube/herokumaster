<script language="javascript">

	$(document).ready(function() {
	
		$(".chzn-select").chosen(function() {
 			
 			
 
		});


	});

</script>

<div id="searcharea">

	<form name="searchForm" method="post" id="searchForm" action="<?= base_url() ?>assets/submit">

		
		<table border="0" cellpadding="3" width="95%">
			
			<tr>
				<td class="userField">Property :</td>
				<td>
					
					<select name="propertyid" class="userField chzn-select" xonchange="alert('changee');">
			
						<option value="0" <? if ($args['propertyid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>" <? if ($args['propertyid'] == $p->propertyid) echo "SELECTED"; ?>><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
										
				</td>
				
				 <td>&nbsp;&nbsp;</td>
								
			</tr>
			
					
					
					
			<tr>
			
				<td class="userField">Author :</td>
				<td>

					
					<select name="authorid" class="userField chzn-select">
						
							<option value="" <? if ($args['authorid'] == 0) echo "SELECTED"; ?>>Please Select</option>
						
						<? foreach ($authors->result() as $au) { ?>
						
							<option value="<?= $au->id ?>" <? if ($args['authorid'] == $au->id) echo "SELECTED"; ?>><?= $au->author ?></option>
						
						<? } ?>
					
					</select>
					
				</td>
				
				 <td>&nbsp;&nbsp;</td>
								
			</tr>
			
			<tr>
			
				<td class="userField">Tags :</td>
				<td>

					
					<input type="text" name="tags" class="userField" value="<?= ($args['tags'] ? $args['tags'] : null) ?>" />
					
				</td>
				
				 <td>&nbsp;&nbsp;</td>
								
			</tr>

			<tr>
				
				<td colspan="10" align="right"><input type="submit" value="search"></td>
			
			</tr>
		</table>	
	
		


	</form>

</div>