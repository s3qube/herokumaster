
<? //print_r($product) ?>

<script language="javascript">
	
	$(document).ready(function() {
	
		$("#productEditForm").validate();
		
		<? if ($mode == 'edit' && checkPerms('can_delete_products')) { ?>
			
			document.getElementById("delete_product").value = 0;
		
		<? } ?>
		
		function updateProductLines() {
		
			var propertyid = $('#propertySelect').val();  
			
			if (propertyid != "") {
			
				//alert("bow wow");
			
				var url = '<?= base_url();?>ajax/fetchProductLineJson/'+propertyid+'/<?= $product->opm_productid?>';
				
				//$('#productLineSelect').load(url);
				
				
				$.getJSON(url, function(data){
			
					//alert(data);
			
					var html = '';
					var len = data.length;
				
				    for (var i = 0; i< len; i++) {
				   		
						html += '<option value="' + data[i].productlineid + '">' + data[i].productline + '</option>';
				   
				    }
			
					//alert(html);
					$('#productLineSelectEl').find('option').remove().end().append(html).trigger("liszt:updated");
				
				});
			
				// also fetch default description
				
				<? if ($mode == 'add') { ?>
				
					var url = '<?= base_url();?>ajax/fetchDefaultProductDescription/';
					
					$.post(url, { propertyid: propertyid}, function(data) {
	  				
	  					if (data != '') {
	  						$("textarea#productdesc").val(data);
						}
						
					});
				
				<? } ?>

				
			}
			
			
			//$("#productLineSelectEl");
				
		
		}
		
		//updateProductLines(); // update product lines onload, in case back button was pushed
	
		$(".chzn-select").chosen(function() {
 			
 			//updateProductLines();
 
		});

	
		$('#propertySelect').change(function() {
 			
 			updateProductLines();
 
		});
	
	});
	
</script>

<form name="productform" action="<?= base_url(); ?>products/save" method="post" enctype="Multipart/Form-Data" id="productEditForm">
	<input type="hidden" name="opm_productid" value="<?= $product->opm_productid ?>">

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Property <span class="productEditRequired">(required)</span></h3>
					
					<select name="propertyid" class="userField required chzn-select" id="propertySelect">
						
						<option value="">Please Select...</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>" <? if ($product->propertyid == $p->propertyid) echo "SELECTED" ?>><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
					
					<br /><br />
					
					<h3 class="userField">Product Name <span class="productEditRequired">(required)</span></h3>
					
					<input type="text" name="productname" value="<?= $product->productname?>" class="userField required" />
					
					<br /><br />
					
					<? if (checkPerms('prodEdit_can_edit_shortname')) { ?>
					
						<h3 class="userField">Short Name</h3>
						
						<input type="text" name="shortname" value="<?= $product->shortname?>" class="userField" />
						
						<br /><br />
					
					<? } ?>
					
					<? if (checkPerms('prodEdit_can_edit_productcode')) { ?>
					
						<h3 class="userField">Product Code</h3>
					
						<input type="text" name="productcode" value="<?= $product->productcode?>" class="userField" />
						
						<br /><br />
						
						<!--<input type="hidden" name="productcode" value="" />-->
					
					<? } ?>
					
					<? if (checkPerms('prodEdit_can_edit_licenseecode')) { ?>
					
						<h3 class="userField">Licensee Code</h3>
					
						<input type="text" name="licenseecode" value="<?= $product->licenseecode?>" class="userField" />
						
						<br /><br />
						
						<!--<input type="hidden" name="productcode" value="" />-->
					
					<? } ?>
					
					
					<? if (checkPerms('can_edit_designcode')) { ?>
					
						
					
						<? if (!$product->designcode_islocked) { ?>
						
							<h3 class="userField">Navision Design Code<span style="font-size:8pt;"> (Enter for legacy product only)</span></h3>
							
							<input type="text" name="designcode" value="<?= $product->designcode?>" class="userField" />
						
						<? } else { ?>
					
							<input type="hidden" name="designcode" value="<?= $product->designcode?>" class="userField" />
							<h3 class="userField">Navision Design Code: <?= $product->designcode?></h3>						
					
						<? } ?>
						
						<br /><br />
					
					<? } ?>
					
					<h3 class="userField">Product Description</h3>
					
						<textarea name="productdesc" class="userField" id="productdesc"><?= $product->productdesc ?></textarea>
					
					<br /><br />
					
					<? if (checkPerms('prodEdit_can_choose_category')) { ?>
					
						<h3 class="userField">Category <span class="productEditRequired">(required)</span></h3>
						
						<select name="categoryid" class="userField required">
				
							<option value="0">Please Select...</option>
						
							<? foreach ($categories as $key=>$cat) { ?>
							
								<option value="<?= $key ?>" <?= ($product->categoryid == $key) ? "SELECTED" : null ?> ><?= $cat ?></option>
							
							<? } ?>
						
						</select>
						
						<br /><br />
						
					<? } ?>
					
					<? //if (checkPerms('prodEdit_can_choose_bodystyle')) { ?>
					
						<!--<h3 class="userField">Body Style <span class="productEditRequired">(required)</span></h3>
						
						<select name="bodystyleid" class="userField required">
							
							<option value="">Please Select...</option>
							
							<? //foreach ($bodystyles->result() as $b) { ?>
							
								<option value="<?= $b->id ?>" <? if ($product->bodystyleid == $b->id) echo "SELECTED" ?>><?= $b->bodystyle ?></option>
							
							<? //} ?>
							
						</select>-->
						
						<br /><br />
						
					<? //} ?>
						
					<? if (checkPerms('prodEdit_can_enter_numprints')) { ?>
					
						<h3 class="userField"># of Prints <!--<span class="productEditRequired">(required)</span>--></h3>
					
						<input type="text" name="numprints" value="<?= $product->numprints ?>" class="userField" />
					
						<br /><br />
						
					<? } ?>
						
					<? if (checkPerms('prodEdit_can_enter_filmnumber')) { ?>
					
						<h3 class="userField">Film Number</h3>
						
						<input type="text" name="filmnumber" value="<?= $product->filmnumber ?>" class="userField" />
						
						<br /><br />
						
					<? } ?>
					
					<? if (checkPerms('prodEdit_can_enter_pginfo')) { ?>
					
						<h3 class="userField">Print + Garment Info <span class="productEditRequired">(required)</span></h3>
					 
						<textarea name="filmlocations" class="userField required"><?= $product->filmlocations ?></textarea>
					
						<br /><br />
						
					<? } ?>
					
					<? if (checkPerms('prodEdit_can_enter_cr_addendums')) { ?>
					
						<h3 class="userField">Copyright Addendums</h3>
						
						<textarea name="copyrightaddendums" class="userField"><?= $product->copyrightaddendums ?></textarea>
						
						<br /><br />
					
					<? } ?>
					
					<? if (checkPerms('prodEdit_can_enter_artwork_charges')) { ?>
					
						<h3 class="userField">Artwork Charges</h3>
						
						<textarea name="artworkcharges" class="userField"><?= $product->artworkcharges ?></textarea>
						
						<br /><br />
					
					<? } ?>
					
					<? if (checkPerms('prodEdit_can_enter_presentation_styles')) { ?>
					
						<h3 class="userField">Presentation Styles</h3>
						
						<textarea name="presentationstyles" class="userField"><?= $product->presentationstyles ?></textarea>
						
						<br /><br />
					
					<? } ?>
			
					
	
			
			</td>
			<td valign="top" align="left">
				
				<? if (checkPerms('prodEdit_can_choose_product_line')) { ?>
				
					<!-- Product Lines! -->
				
					<h3 class="userField">Product Lines <span class="productEditRequired">(required)</span></h3>
					
					<div id="productLineSelect">
					
						<?// if ($productLines) { ?>
					
						<select name="productLineIDs[]" id="productLineSelectEl" class="userField required chzn-select" MULTIPLE>
						
							<? foreach ($productLines->result() as $pl) { ?>

								<option value="<?= $pl->productlineid ?>" <? if ($pl->isassigned) echo "SELECTED" ?>><?= $pl->productline ?></option>
							
							<? } ?>
						
						</select>
						
						<?// } ?>
					
					</div>
					
					<!-- / Product Lines! -->
					
					<br />
				
				<? } ?>
				
				
				<? if (checkPerms('prodEdit_can_choose_designer')) { ?>	
						
						<? if (checkPerms('can_view_all_designers')) { ?>
						
							<h3 class="userField">Designer(s):</h3>
						
							<select name="designerIDs[]" class="userField chzn-select" multiple>
				
								<option value="0">Please Select...</option>
							
								<? foreach ($designers->result() as $d) { ?>
								
									<option value="<?= $d->userid ?>" <?= ($d->isassigned) ? "SELECTED" : null ?> ><?= $d->username ?></option>
								
								<? } ?>
							
							</select>
						
						
						<? } else { ?>
						
							
							<h3 class="userField">Designer: <?= $this->userinfo->username ?></h3>
							<input type="hidden" name="designerIDs[]" value="<?= $this->userinfo->userid ?>" />
						
						
						<? } ?>
					
					<br /><br />
					
					<!-- / Designers -->
					
					
				
				<? } ?>
				
				<? if (checkPerms('prodEdit_can_choose_licensees')) { ?>	
						
						<? if (checkPerms('can_view_all_licensees')) { ?>
						
							<h3 class="userField">Licensee(s):</h3>
						
							<select name="licenseeIDs[]" class="userField chzn-select" multiple>
				
								<option value="0">Please Select...</option>
							
								<? foreach ($licensees->result() as $l) { ?>
								
									<option value="<?= $l->usergroupid ?>" <?= ($l->isassigned) ? "SELECTED" : null ?> ><?= $l->usergroup ?></option>
								
								<? } ?>
							
							</select>
						
						
						<? } ?>
					
					<br /><br />
					
					<!-- / Licensees -->
					
					<br /><br />
					
				<? } ?>
				
				<? if (checkPerms('prodEdit_can_enter_due_date')) { ?>
					
					<h3 class="userField">Due Date</h3>
						
					<input type="text" class="w8em format-m-d-y divider-dash highlight-days-12" id="dp-normal-b1" name="duedate" value="<?= ($product->duedate) ? date("m-d-Y",$product->duedate) : "0";?>" maxlength="10" style="font-size:13pt;width:300px;" />							
					
					<br /><br />
				
				<? } ?>
				
				<? if (checkPerms('prodEdit_can_choose_article')) { ?>
					
					<h3 class="userField">Article <span class="productEditRequired">(required)</span></h3>
						
					<select name="articleid" class="userField ">
						
						<option value="">Please Select...</option>
						
						<? foreach ($articles->result() as $a) { ?>
						
							<option value="<?= $a->id ?>" <? if ($product->articleid == $a->id) echo "SELECTED" ?>><?= $a->article ?></option>
						
						<? } ?>
						
					</select>
					
					<br /><br />
					
				<? } ?>
				
				<?/* if (checkPerms('prodEdit_can_choose_substage')) { 
					
					<h3 class="userField">Submission Stage <span class="productEditRequired">(required)</span></h3>
						
					<select name="substageid" class="userField required">
						
						<option value="">Please Select...</option>
						
						<? foreach ($substages->result() as $s) { ?>
						
							<option value="<?= $s->id ?>" <? if ($product->substageid == $s->id) echo "SELECTED" ?>><?= $s->stage ?></option>
						
						<? } ?>
						
					</select>
					
					<br /><br />
					
			 	} */?>
									
			</td>
		</tr>
	</table>
	
	<br /><br />
	
	<div style="text-align:right;">
		
		<input type="submit" value="Save">&nbsp;&nbsp;&nbsp;
		
		<? if ($mode == 'add') { ?>
		
			<br /><br />
	
			<input type="submit" name="save_add_another" value="Save and Add Another">&nbsp;&nbsp;&nbsp;
		
		<? } ?>
		
		<? if ($mode == 'edit' && checkPerms('can_delete_products')) { ?>
			
			<script language="javascript">
			
				function confirmDeleteProduct() {
				
					if (confirm("Are you sure you want to delete this product?")) {
					
						document.getElementById("delete_product").value = 1;
						document.productform.submit();
					
					}
					
				
				}
			
			</script>
			
			<br /><br />
			
			<input type="hidden" name="delete_product" id="delete_product" value="">
			<input type="button" onclick="confirmDeleteProduct();" value="Delete This Product">&nbsp;&nbsp;&nbsp;
		
		
		<? } ?>
		
	</div>


</form>

<br /><br /><br />


<script language="javascript">

	window.onload = function(){
	
		
	


	
	};
	
	/*
		
	Window.onDomReady(function() {
		
		
		
		
		$('propertySelect').addEvent('change',function(){
		
			 // update product lines
		
			 var propertyid = this.getValue();
			 var url = '<?= base_url();?>ajax/fetchProductLineSelect/'+propertyid+'/<?= $product->opm_productid?>';
			 new Ajax(url, {
				  method: 'get',
				  update: $('productLineSelect')
			 }).request();
			 
			 // update product description
			 
			 var url = '<?= base_url();?>ajax/fetchDefaultProductDescription/'+propertyid;
			 new Ajax(url, {
				  method: 'get',
				  update: $('productdesc')
			 }).request();
			 
			 
		});
	
	});
	
	*/
	

</script>
	