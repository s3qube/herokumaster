
		
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
	
	
	<form name="productLinesForm" method="post" action="<?= base_url(); ?>properties/saveProductLines">
	
		<input type="hidden" name="propertyid" value="<?= $p->propertyid ?>" />
		
		<? if ($productLines->num_rows() > 0) { ?>
		
			<table border="0" cellpadding="3" cellspacing="1" class="tblProductLines">
			
				<tr>
					<th class="tblProductLines">Product Line</th>
					<th class="tblProductLines">Is Active ?</th>
					<!--<th class="tblProductLines">Delete</th>-->
                    <!-- Globosoft -->
                    <th class="tblProductLines">Edit</th>
                   	<? if (checkPerms('can_delete_productlines')) { ?>
                    <th class="tblProductLines">Delete</th>
                    <? } ?>
                    <!-- -->
				</tr>
			
			<? foreach ($productLines->result() as $pl) { ?>
			
				<input type="hidden" name="arrProductLineIDs[<?= $pl->productlineid ?>]" value="1">
				
				<tr class="tbl_user_properties">
				
					<td class="tblProductLines"><?= $pl->productline ?></td>
					<td class="tblProductLines"><input type="checkbox" name="isactive[<?= $pl->productlineid ?>]" <? if ($pl->isactive) echo "CHECKED";?>></td>

                    <td class="tblProductLines"><a href="<?= base_url(); ?>properties/editProductLine/<?= $pl->productlineid ?>/<?= $p->propertyid ?>">Edit</a></td>
                  
					<? if (checkPerms('can_delete_productlines')) { ?>
            
						<td class="tblProductLines"><a href="<?= base_url(); ?>properties/deleteProductLine/<?= $pl->productlineid ?>/<?= $p->propertyid ?>" onclick="return confirm('Do you want to delete this product line?')">Delete</a></td>
			
					<? } ?>
			
				</tr>
			
			<? } ?>
			
				<tr>
					<td class="tblProductLines" colspan="10" align="right"><input type="submit" name="save" value="Save"></td>
				</tr>
			
			</table>
		
		<? } else { ?>
		
			
			<h4>No Product Lines.</h4>
			
		
		<? } ?>
	
	</form>
	
	<br /><br />
	
	<? if (checkPerms('can_add_product_lines')) { ?>
	
	
		<h3 class="userField">Add Product Line:</h3>
		
		<form name="addProductLineForm" method="post" action="<?= base_url(); ?>properties/addProductLine">
			<input type="hidden" name="propertyid" value="<?= $p->propertyid ?>" />
		
			<input type="text" class="userField" name="productline">
			
			<input type="submit" name="addproperty" value="Add" />
		
		</form>
		
	
	<? } ?>