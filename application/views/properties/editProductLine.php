

<form name="userform" action="<?= base_url(); ?>properties/updateProductLine" method="post">
	
	<input type="hidden" name="productlineid" value="<?= $productLine->productlineid ?>">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Product Line</h3>
					
					<input type="text" name="productline" value="<?= $productLine->productline ?>" class="userField" />
							
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
				
				<input type="hidden" name="propertyid" value="<?= $productLine->propertyid ?>">
				
			</td>
		</tr>
	</table>
	
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="right"><input type="submit" value="Update">&nbsp;&nbsp;&nbsp;</td>
		</tr>
	</table>
	

</form>

<br />	
	