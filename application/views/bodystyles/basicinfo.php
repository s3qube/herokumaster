
<form name="userform" action="<?= base_url(); ?>bodystyles/save" method="post" enctype="Multipart/Form-Data">
	<? if (isset($b)) { ?>
		<input type="hidden" name="bodystyleid" value="<?= $b->id ?>">
	<? } ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Body Style</h3>
					
					<input type="text" name="bodystyle" value="<?= (isset($b->bodystyle) ? $b->bodystyle : null) ?>" class="userField" />
					
					<br /><br />
					
					<h3 class="userField">Category</h3>

					<select name="categoryid" class="userField">
			
						<option value="0" <? if (isset($b) && $b->categoryid == 0) echo "SELECTED"; ?>>Please Select...</option>
						
						<? foreach ($categories->result() as $c) { ?>
						
							<option value="<?= $c->categoryid ?>" <? if (isset($b) && ($b->categoryid == $c->categoryid)) echo "SELECTED"; ?>><?= $c->category ?></option>
						
						<? } ?>
						
					</select>
					
					<br /><br />
					
					<h3 class="userField">Code</h3>

					<input type="text" name="code" value="<?= (isset($b) ? $b->code : null) ?>" class="userField" />
		
					
			
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
	