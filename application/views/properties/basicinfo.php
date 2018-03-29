
<? //print_r($product) ?>

<script language="javascript">

	

</script>

<form name="userform" action="<?= base_url(); ?>properties/save" method="post" enctype="Multipart/Form-Data">
	
	<? if (isset($p)) { ?>
	
		<input type="hidden" name="propertyid" value="<?= $p->propertyid ?>">

	<? } ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="540">
					
					<h3 class="userField">Property</h3>
					
					<input type="text" name="property" value="<?= (isset($p->property) ? $p->property : null) ?>" class="userField" />
					
					
					
					<br /><br />
					
					<h3 class="userField">Approval Method</h3>
					
					<select name="approval_methodid" class="userField">
						
						<? foreach ($appMethods->result() as $am) { ?>
						
							<option value="<?= $am->approval_methodid ?>" <? if ((isset($p->approval_methodid)) && $am->approval_methodid == $p->approval_methodid) echo "SELECTED"; ?>><?= $am->approval_method ?></option>
						
						<? } ?>
					
					</select>

					<br /><br />
					
					<h3 class="userField">Copyright Line</h3>
					
					<textarea name="copyright" class="userField" ><?= (isset($p->copyright) ? $p->copyright : null) ?></textarea>


					
					
					<? if (isset($mode) && $mode != 'add' && checkPerms('can_change_product_territories')) { ?>
						
						<br /><br />
						
						<h3 class="userField">Default Territories</h3>
						
						<div id="">
				
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td></td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
								</tr>
								<tr>
									<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
										
										
										
										<?= $this->opm->displayPropertyTerritories($p->propertyid); ?>								
										
								
								
									</td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
								</tr>
							</table>
	
						</div>
					
					<? } ?>
					
					<br /><br />
					
					<h3 class="userField">Default Product Description</h3>
					
					<textarea name="default_productdesc" class="userField" ><?= (isset($p->default_productdesc) ? $p->default_productdesc : null) ?></textarea>
					
					<? if (checkPerms('can_change_product_territories')) { ?>
					
						<br /><br />
						
						<h3 class="userField">Property Is Active</h3>
						
						<input type="checkbox" name="isactive" <? checkDisabled(); ?> <?= (isset($p->isactive) && $p->isactive == 1 ? "CHECKED" : null) ?>>
					
					<? } ?>
					
					<br /><br />
						
						<h3 class="userField">Is Harley Property</h3>
						
						<input type="checkbox" name="isharley" <? checkDisabled(); ?> <?= (isset($p->isharley) && $p->isharley == 1 ? "CHECKED" : null) ?>>
			
	
			
			</td>
			<td valign="top" align="left">
				
				<!-- avatar goes here -->
				
				<table width="25%" cellpadding="0" cellspacing="0" border="0">
				
					<tr>
						<td>
						
							<h3 class="userField">Property Image <small>(300x100 px)</small></h3>
							
							<? if (isset($p)) { $this->opm->displayPropertyImage($p->propertyid); } ?>
							
							<br /><br />
							
							<input type="file" name="propertyImage" />
							
						</td>
					</tr>
				
				</table>
				
				
				<? if (checkPerms('can_edit_bandcode')) { ?>


					<br><br>
				
					<h3 class="userField">Band Code</h3>
					
					<input type="text" name="nv_propid" value="<?= (isset($p->nv_propid) ? $p->nv_propid : "9999") ?>" class="userField" />
				
				
				<? } ?>
				
				
				<? if (isset($mode) && $mode != 'add' && checkPerms('can_change_product_rights')) { ?>
						
						<br /><br />
						
						<h3 class="userField">Default Rights</h3>
						
						<div id="">
				
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td></td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
								</tr>
								<tr>
									<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
										
										
										
										<?= $this->opm->displayPropertyRights($p->propertyid); ?>								
										
								
								
									</td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
								</tr>
							</table>
	
						</div>
					
					<? } ?>
					
					<? if (isset($mode) && $mode != 'add' && checkPerms('can_change_product_channels')) { ?>
						
						<br /><br />
						
						<h3 class="userField">Artist Channels</h3>
						
						<div id="">
				
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td></td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
								</tr>
								<tr>
									<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
										
										
										
										<?= $this->opm->displayPropertyChannels($p->propertyid); ?>								
										
								
								
									</td>
								</tr>
								<tr>
									<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
								</tr>
							</table>
	
						</div>
					
					<? } ?>
					
					<br />
					
					<? if (checkPerms('can_hide_all_products') && $mode != 'add') { ?>
						
						<input type="hidden" name="hideAllProducts" value="" />
						
						<input type="button" onclick="confirmHideAllProducts();" value="Hide All Products From External Users" class="hideProductsBtn" />
				
					<? } ?>
				
			</td>
		</tr>
	</table>
	
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="right"><input type="submit" value="Save">&nbsp;&nbsp;&nbsp;</td>
		</tr>
	</table>
	
	<div style="clear:both;"></div>
	

</form>

<br />	
	