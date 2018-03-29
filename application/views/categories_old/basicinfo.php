
<? //print_r($product) ?>

<form name="userform" action="<?= base_url(); ?>categories/save" method="post" enctype="Multipart/Form-Data">
	<? if (isset($p)) { ?>
		<input type="hidden" name="categoryid" value="<?= $p->categoryid ?>">
	<? } ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Category</h3>
					
					<input type="text" name="category" value="<?= (isset($p->category) ? $p->category : null) ?>" class="userField" />
					
			<br /><br />
						
					
			
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
				
				<table width="25%" cellpadding="0" cellspacing="0" border="0">
				
					<tr>
						<td>
						
						
							
						</td>
					</tr>
				
				</table>
				
			</td>
		</tr>
	</table>
	
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="right"><input type="submit" value="Save">&nbsp;&nbsp;&nbsp;</td>
		</tr>
	</table>
	

</form>

<br />	
	