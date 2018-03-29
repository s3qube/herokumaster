<div id="searcharea">
	<form name="searchForm" method="post" action="<?=base_url();?>users/submit">
		
		<table border="0" cellpadding="3" width="95%">
			
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