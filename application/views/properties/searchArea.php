<div id="searcharea">

	<form name="searchForm" method="post" id="searchForm" action="<?= base_url() ?>properties/submit">
	
		
		<table border="0" cellpadding="3" width="95%">
			
			<tr>
				<td>Property Name :</td>
				<td>
					
					<input type="text" name="searchText" class="searchField" value="<?= ($args['searchText'] ? $args['searchText'] : null) ?>" />
					
				</td>
				
				 <td>&nbsp;&nbsp;</td>
								
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