
<? //print_r($product) ?>

<script language="javascript">
		
	$(document).ready(function() {
	
		$(".chzn-select").chosen(function() {
 			
 			
 
		});


	});
		
			// detect html5 file reader object and redirect if not present
			
			if (typeof(FileReader) == "undefined") {
   				
   				var url = ( document.URL );
   				var newUrl = url.replace("showUpload","showJavaUpload");
   				
   			//	alert(newUrl);
   				
   				window.location = newUrl;
   			
   			}
   			
			// Convert divs to queue widgets when the DOM is ready
			
			$(function() {
				
				// Setup html5 version
				uploader = $("#html5_uploader").pluploadQueue({
				
					// General settings
					runtimes : 'html5',
					url : '<?=base_url();?>files/uploadAssets/',
					max_file_size : '250mb',
					multipart_params: {propertyid : $("#propertyid").val(), param2 : 'value2'},
					unique_names : true
			
					/*chunk_size : '1mb',*/
			
					// Resize images on clientside if we can
					/*resize : {width : 320, height : 240, quality : 90},
			
					// Specify what files to browse for
					filters : [
					
						{title : "Image files", extensions : "jpg,gif,png"},
						{title : "Zip files", extensions : "zip"}
					
					]*/
			
				});
								
				var uploader = $('#html5_uploader').pluploadQueue(); 
				
				uploader.bind('FileUploaded', function(up, file, res) {
			   
			        //alert("FILE UPLOODED");
			   
			    });
			    
			    uploader.bind('BeforeUpload', function(up, file) {
				    up.settings.multipart_params = {"propertyid": $("#propertyid").val() };
				});
			
			
			});
			
			function showUploader() {
				
				if ($('#propertyid').val() != 0) {
					
					$('#html5_uploader').fadeIn("fast");				
				
				} else {
					
					$('#html5_uploader').fadeOut("fast");
					
				}
				
			}
		

</script>

<form name="userform" action="<?= base_url(); ?>assets/doUpload" method="post" enctype="Multipart/Form-Data">

	<div class="contentWrapper">
	
		<h3 class="userField">Property</h3>
	
		<select name="propertyid" id="propertyid" class="chzn-select userField" onchange="showUploader();">
						
			<option value="0">Please Select...</option>
						
			<? foreach ($properties->result() as $p) { ?>
			
				<option value="<?= $p->propertyid ?>" ><?= $p->property ?></option>
			
			<? } ?>
			
		</select>
	
		<br /><br />
		
		<div id="html5_uploader" style="display:none;">
			
			<form enctype="multipart/form-data" method="post" action="<?=base_url();?>files/upload/">
				<input type="file" name="file" />
				<input type="submit" value="submitt" />
			</form>
		
		</div>
		
		<!--
		
		<div class="assetEditItem">
	
			<div class="assetEditImage"><img src="<?= base_url(); ?>imageclass/viewAssetThumbnail/<?=$a->assetid?>/500" style="border-width:1px;border-color:#333333;border-style:solid;"></div>
		
			<div class="assetEditInfo">
			
				<h3 class="assetField">Asset Name</h3>
			 
				<input type="text" name="assetname" value="<?= (isset($a->assetname) ? $a->assetname : null) ?>" class="assetField" />
			
				<h3 class="assetField">Author</h3>
					
					<select name="authorid" class="assetField">
						
							<option value="" <? if ((isset($a->authorid)) && $a->authorid == 0) echo "SELECTED"; ?>>Please Select</option>
						
						<? foreach ($authors->result() as $au) { ?>
						
							<option value="<?= $au->id ?>" <? if ((isset($a->authorid)) && $a->authorid == $au->id) echo "SELECTED"; ?>><?= $au->author ?></option>
						
						<? } ?>
					
					</select>
					
				<h3 class="assetField">Notes</h3>
					
				<textarea name="assetDetail" class="assetField"><?= (isset($a->assetdetail) ? $a->assetdetail : null) ?></textarea>
					
				<h3 class="assetField">Tags (comma-separated)</h3>
					
				<textarea name="tags" class="assetField"><?= (isset($a->tags) ? $a->tags : null) ?></textarea>
			
			</div>
			
			
		
		
		</div>
		
		-->
	
	</div>
	


	<!--<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="540">
					
					<h3 class="userField">Property</h3>
					
					<input type="text" name="property" value="<?= (isset($p->property) ? $p->property : null) ?>" class="userField" />
					
					
					
					<br /><br />
					
					<h3 class="userField">Author</h3>
					
					<select name="approval_methodid" class="userField">
						
						<? foreach ($authors->result() as $au) { ?>
						
							<option value="<?= $au->id ?>" <? if ((isset($a->authorid)) && $a->authorid == $au->id) echo "SELECTED"; ?>><?= $au->author ?></option>
						
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
					
					<br />
					
					<? if (checkPerms('can_hide_all_products') && $mode != 'add') { ?>
						
						<input type="hidden" name="hideAllProducts" value="" />
						
						<input type="button" onclick="confirmHideAllProducts();" value="Hide All Products From External Users" class="hideProductsBtn" />
				
					<? } ?>
				
			</td>
		</tr>
	</table>-->
	
	
	<div style="clear:both;"></div>
	

</form>

<br />	
	