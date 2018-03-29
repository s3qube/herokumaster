<script language="javascript">

	function changeAll(objChecker) {
	
		if (objChecker.checked == true) {

			CheckAll('arcList[]',1);
		
		} else {

			CheckAll('arcList[]',0);
		
		}
	
	}
	
	function CheckAll(field, value) {
		for (var i=0;i<document.archiveListForm.elements[field].length;i++) {
			if(value == 1) {
				document.archiveListForm.elements[field][i].checked = true
			} else {
				document.archiveListForm.elements[field][i].checked = false
			}
		}
	}

		
	function confirmSubmit() {
		
		
		$message = "Are you sure you want to archive these products? This process is very difficult to undo. If you are sure, click OK, otherwise click CANCEL!!";
	
		if (confirm($message))
			return true;
		else
			return false;
	
	}
	
	

</script>

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	
	<tr>
	
		<td valign="top">
		
			<p>Archiving products will soft delete them from the OPM system, and move their master files and separations into the archival area. Please do not archive products without being fully sure of what you are doing.</p>

			<form name="archiveForm" method="post" action="<?= base_url(); ?>archive/submit">
			
				<input type="hidden" name="clearProductLine" value="" /> <!-- This is used to clear the PL field when a new property is selected. -->

				<select name="propertyid" onchange="document.archiveForm.clearProductLine.value = 'true'; document.archiveForm.submit();" class="userField">
				
					<option value="0">Please Select Property To Archive...</option>
					
					<? foreach ($properties->result() as $p) { ?>
							
						<option value="<?= $p->propertyid ?>" <? if ($propertyid == $p->propertyid) echo "SELECTED" ?>><?= $p->property ?></option>
					
					<? } ?>
				
				</select>
				
				<? if (isset($propertyid)) { ?>
				
					<select name="productlineid" onchange="document.archiveForm.submit();" class="userField">
					
						<option value="0">All Product Lines</option>
						
						<? if ($productLines) { ?>
					
						
							<? foreach ($productLines->result() as $pl) { ?>
								
								<option value="<?= $pl->productlineid ?>" <? if ($pl->productlineid == $productlineid) echo "SELECTED" ?>><?= $pl->productline ?></option>
							
							<? } ?>
						
					
						
						<? } ?>
					
					</select>
				
				<? } ?>
			
			</form>
		


			
			<? if (isset($products)) { ?>
			
				<br /> <br />
			
				
				<table border="0" cellpadding="2" width="750">
				
					<form name="archiveListForm" id="archiveListForm" method="post" action="<?= base_url() ?>archive/submitProducts" onsubmit="return confirmSubmit();">
				
						<tr>
							
							<td><input type="checkbox" onchange="changeAll(this);"/></td>
							<td>ID</td>
							<td>Product Name</td>
							<td>Category</td>
							<td>Created</td>
							<td>Last Modified</td>
							<td>Master Files</td>
							<td>Seps</td>
					
						</tr>
				
						<? foreach ($products->result() as $p) { ?>
					
							<tr>
								<td class="searchProductInfo"><input type="checkbox" name="arcList[]" value="<?=$p->opm_productid?>" /></td>
								<td class="searchProductInfo"><?=$p->opm_productid?></td>
								<td class="searchProductName"><a href="<?= base_url()?>products/view/<?=$p->opm_productid?>" target="_blank"><?=$p->productname?></a></td>
								<td class="searchProductInfo"><?=$p->category?></td>
								<td class="searchProductInfo"><?= opmDate($p->timestamp) ?></td>
								<td class="searchProductInfo"><?= opmDate($p->lastmodified) ?></td>
								<td class="searchProductInfo"><?=$p->numMasterFiles?></td>
								<td class="searchProductInfo"><?=$p->numSeparations?></td>
								
								
							</tr>
						
						<? } ?>
						
						<tr>
						
							<td colspan="20" align="right"><br /><br /><input type="submit" value="Archive Products" /></td>
						
						</tr>
				
					</form>
					
				</table>
			
			<? } ?>


		</td>
	
	</tr>
	
</table>