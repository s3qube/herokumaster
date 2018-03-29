

<form name="wholesaleInfoForm" action="<?= base_url() ?>products/saveWholesaleInfo" method="post">
	
	<input type="hidden" name="opm_productid" value="<?= $p->opm_productid ?>" />
	
	<div id="prodWsContent">
	
		<div class="reqCol1">
			
			<h3 class="userField">Is Available:</h3>
		
			<input type="checkbox" name="isactive" <?= ($p->wholesaleInfo->isactive ? "CHECKED" : null) ?>>
			
			<br /><br />
			
			<h3 class="userField">Is Featured:</h3>
		
			<input type="checkbox" name="isfeatured" <?= ($p->wholesaleInfo->isfeatured ? "CHECKED" : null) ?>>
			
			<br /><br />
			
			<h3 class="userField">Site Brand:</h3>
								
			<select name="sitebrandid" class="userField chzn-select">
		
				<option value="0">Please Select...</option>
			
				<? foreach ($sb->result() as $sb) { ?>
				
					<option value="<?= $sb->id ?>" <?= ($sb->id == $p->wholesaleInfo->sitebrandid) ? "SELECTED" : null ?> ><?= $sb->sitebrand ?></option>
				
				<? } ?>
			
			</select>
			
			<br /><br />
			
			<h3 class="userField">Base Price:</h3>
								
			<input type="text" name="baseprice" class="userField" value="<?= $p->wholesaleInfo->baseprice ?>" />
		
			
		</div>
		
		<div class="reqCol2">
			
			<h3 class="userField">Available Sizes:</h3>
			
			<table class="wsAvailSizes">
				
					<tr>
						<th class="wsAvailSizes">Size</th>
						<th class="wsAvailSizes">Sku</th>
						<th class="wsAvailSizes">Available</th>
					</tr>
				
				<? foreach ($p->wholesaleInfo->sizes as $s) { ?>
				
					<tr>
						<td><?= $s['size'] ?></td>
						<td><input type="text" class="wsSizeSku" name="sku[<?= $s['id'] ?>]" value="<?= $s['sku'] ?>" /></td>
						<td><input type="checkbox" name="isavail[<?= $s['id']?>]" <?= ($s['isactive'] ? "CHECKED" : null) ?> /></td>
					</tr>
				
				<? } ?>
				
				<tr>
					<td></td>
				<tr>
				
				<tr>
					
						<td>
		
							<select name="add_sizeid">
							
								<option value="">Add Size...</option>
							
								<? foreach ($sizes->result() as $s) { ?>
		
									<option value="<?= $s->id ?>"><?= $s->size ?></option>
									
								<? } ?>
							
							</select>
		
						</td>
						<td><input type="text" name="add_sku" /></td>
						<td><input type="submit" value="Add" /></td>
					
					
					
				</tr>
				
			</table>
			
			<br /><br /><br /><br /><br /><br />
			
			<div style="text-align:right;">
				<input type="submit" class="invoiceBtn" value="Save" />
			</div>
			
		</div>
						
		
		
		<pre>
				<? //print_r($p->wholesaleInfo) ?>
		</pre>
		
		
	</div>

</form>