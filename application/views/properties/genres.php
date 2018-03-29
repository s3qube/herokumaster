
<? if (checkPerms('can_manage_property_genres')) { ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<div class="userField">Add Genre: </div>
				
				<select class="userField">
					
					<option value="0">Choose...</option>
					
				</select>
			
			</td>
		</tr>
	</table>

	
	<br /><br />


	<? foreach ($genres->result() as $g) { ?>

	<table border="0" class="searchProductTable">
		<tr>
			<td valign="middle">
				
				<span class="propertyName"><?= $g->genre?>&nbsp;&nbsp;<a href="#" class="red">x</a></span> 
					<br />
				
				<span class="searchProductInfo"></span>
			
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	
<? } ?>
	

<? } ?>

