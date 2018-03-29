
<form name="permform" method="post" action="<?= base_url(); ?>categories/save">

	<? if (isset($category)) { ?>
	
		<input type="hidden" name="categoryid" value="<?= $category->categoryid ?>" />
	
	<? } ?>
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
			
				<h3 class="userField">Parent Category:</h3>
				
				<select name="parentcategoryid" class="userField" <? checkDisabled(); ?>>
				
					<option value="0">Please Select...</option>
				
					<? foreach ($categories as $key=>$cat) { ?>
					
						<option value="<?= $key ?>" <?= (isset($category) && $category->parentid == $key) ? "SELECTED" : null ?> ><?= $cat ?></option>
					
					<? } ?>
				
				</select>
				
				<br /><br />
	
				<h3 class="userField">Category Name:</h3>
								
				<input type="text" name="category" class="userField" value="<?= (isset($category)) ? $category->category : null ?>" />
			
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
		</tr>
	</table>
</form>
