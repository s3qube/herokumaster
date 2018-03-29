
<script language="javascript">


	function confirmDeleteVisual($imageid) {
	
		<? if ($product->islocked && !checkPerms('can_edit_locked_products')) { ?>
		
			alert("Product is locked. Image cannot be deleted.");
			return false;
		
		<? } ?>

	
		if (confirm("Are You Sure You Want To Delete This Visual?")) {
		
			window.location = "<?= base_url();?>imageclass/delete/" + $imageid;
			//alert("About To Redirect!");
		
		} else {
		
			return false;
		
		}
	
	}
	
		
	// Convert divs to queue widgets when the DOM is ready
	
	/*$(function() {
		
		// Setup html5 version
		$("#mfUploader").pluploadQueue({
		
			// General settings
			runtimes : 'html5',
			url : 'upload.php',
			max_file_size : '250mb',
			chunk_size : '1mb',
			unique_names : true
	
	
	
		});
	
	
	});*/
		


</script>

<? if (checkPerms('can_upload_images') && (!$product->islocked || checkPerms('can_edit_locked_products'))) { ?>

	<div style="width:693px;margin-left:auto;margin-right:auto;">
	
		<div style="float:left;margin-left:10px;" class="fbox_header">Images</div>
		
		<div style="text-align:right"><a href="#" onclick="opm.showHideDiv('newPostArea'); return false;" class="blueLink">Upload New Image</a></div>
		
		<br />
		
		
		
	</div>
	
	<div id="newPostArea" style="display:none;">
	
		<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
				</tr>
				<tr>
					<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
						<div class="fb_content2">
						
							<table border="0" style="margin-left:20px; margin-right:20px;">
							
							<tr>
								<td>
						
									<form name="newpost" action="<?= base_url();?>products/saveImage" method="POST" enctype="Multipart/Form-Data">
					
										<input type="hidden" name="opm_productid" value="<?= $product->opm_productid ?>">
										
										<h3 class="forumHeader">Label</h3>
										
										<input type="text" class="forumPost" name="label" />
										
										<br /><br />
										
										<input type="file" name="imageFile">
										
										<br /><br />
										
										<div style="text-align:right">
										<input type="submit" name="submit" value="Upload">
										</div>
									
									</form>
									
								</td>
							</tr>
						</table>
						
						
						
						</div>
						
					</td>
				</tr>
				<tr>
					<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
				</tr>
			</table>
		
		</div>

<? } ?>


<? foreach ($images->result() as $key=>$i) { ?>

		<? //$imageDims = getJPEGImageXY($i->image); ?>
		
		<table border="0" cellpadding="0" cellspacing="0" align="center" width="693">
			<tr>
				<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
			</tr>
			<tr>
				<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
					<div class="fb_content2">
					<table border="0" style="margin-left:20px; margin-right:20px;">
						
						<tr>
							<td><a href="#" onclick="shadowboxImage(<?= $i->imageid ?>,500);return false;"><img src="<?=base_url();?>/imageclass/viewThumbnail/<?=$i->imageid?>/80" border="0" class="prodImagesImg" /></a></td>
							
							<td valign="middle">
								
								<table border="0" style="margin-left:25px;" width="500">
									<tr>
										<td class="prodImageFilename">Filename: <?= $i->imageid ?>.jpg <? if ($i->isdefault) { ?>&nbsp;&nbsp;<span class="redLink">DEFAULT</span><? } ?></td>
										<td class="prodImageFilename">Label: <?= $i->image_label?></td>
									</tr>
									<tr>
										<td><br /><br /></td>
									</tr>
									<tr>
										<td><a href="<?= base_url();?>imageclass/view/<?=$i->imageid?>/true" class="redLink">Download Image</a><? if (checkPerms('can_delete_images')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="confirmDeleteVisual(<?=$i->imageid?>); return false;" class="redLink">Delete Image</a><? } ?><? if (checkPerms('can_make_default') && !$i->isdefault) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url();?>imageclass/makeDefault/<?= $product->opm_productid ?>/<?=$i->imageid?>" onclick="" class="redLink">Make Default</a><? } ?></td>
									</tr>
								</table>
								
							</td>
						</tr>
						
					</table>
					</div>
					
				</td>
			</tr>
			<tr>
				<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
			</tr>
		</table>


	<? if ($key != ($images->num_rows - 1)) {  // DONT SHOW DIV IF THIS IS THE LAST IMAGE! ?>

		<div class="searchDiv"></div>

	<? } ?>
		

<? } ?>

<? if (checkPerms("can_view_masterfiles")) { ?>

<br /><br />

<table border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td><div style="margin-left:10px;" class="fbox_header">Master Files</div></td>
	
		<? if (!$product->islocked || checkPerms('can_edit_locked_products') || checkPerms('can_upload_hires_locked')) { ?>
		
			<td align="right"><a href="javascript:openUploader(<?= $product->opm_productid ?>,'masterfile');" class="blueLink">Upload File</a>&nbsp;&nbsp;<? if (checkPerms('can_guest_upload')) { ?>|&nbsp;&nbsp;<a href="javascript:openGuestUploadWindow(<?= $product->opm_productid ?>,'mf');" class="blueLink">Setup Guest Upload</a><? } ?></td>
	
		<? } ?>	
	</tr>
	<tr>
		<td colspan="2">
			<div id="mfUploadArea">
				<div id="mfUploader"></div>
			</div>
		<img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
	</tr>
	<tr>
		<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
			<div class="fb_content">
			<table border="0" width="665" align="center">
				
				<? foreach ($masterfiles->result() as $f) { ?>
				
				<tr>
					<td><img src="<?= base_url(); ?>resources/images/clip.gif" width="17" height="15" border="0" /></td>
					<td class="fbox_td1"><?= $f->filename ?></td>
					<td class="fbox_td2"><?= byteSize($f->filesize); ?></td>
					
					<? if (!$f->archivedate) { ?>
					
						<td class="fbox_td3" align="right">
						
							<a href="<?= base_url(); ?>files/download/mf/<?= $f->fileid ?>" class="redBoldLink">Download</a>
							<? if (checkPerms('can_delete_masterfiles') && (!$product->islocked || checkPerms('can_edit_locked_products'))) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="confirmDeleteMfSep('mf',<?= $f->fileid ?>)" class="redBoldLink">Delete</a><? } ?>
							<? if (checkPerms('can_guest_download')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:openGuestDownloadWindow(<?= $f->fileid ?>,'mf');" class="redBoldLink">Guest</a>&nbsp;&nbsp;<? } ?>
						
						</td>
						
					<? } else { ?>
					
						<td class="fbox_td3" align="right"><span class="archivedFile">Archived <?= opmDate($f->archivedate) ?></span></td>
						
					
					<? } ?>
						
						
				</tr>
				
				<? } ?>
				
			</table>
			</div>
			
		</td>
	</tr>
	<tr>
		<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
	</tr>
</table>

<? } ?>


<? if (checkPerms("can_view_separations")) { ?>

<br /><br />


<table border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td><div style="margin-left:10px;" class="fbox_header">Separations</div></td>
		
		<? if (!$product->islocked || checkPerms('can_edit_locked_products') || checkPerms('can_upload_hires_locked')) { ?>

			<td align="right"><a href="javascript:openUploader(<?= $product->opm_productid ?>,'separation');" class="blueLink">Upload File</a>&nbsp;&nbsp;<? if (checkPerms('can_guest_upload')) { ?>|&nbsp;&nbsp;<a href="javascript:openGuestUploadWindow(<?= $product->opm_productid ?>,'sep');" class="blueLink">Setup Guest Upload</a><? } ?></td>
	
		<? } ?>

	</tr>
	<tr>
		<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
	</tr>
	<tr>
		<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
			<div class="fb_content">
			<table border="0" width="665" align="center">
				
				<? foreach ($separations->result() as $f) { ?>
				
				<tr>
					<td><img src="<?= base_url(); ?>resources/images/clip.gif" width="17" height="15" border="0" /></td>
					<td class="fbox_td1"><?= $f->filename ?></td>
					<td class="fbox_td2"><?= byteSize($f->filesize); ?></td>
					
					<? if (!$f->archivedate) { ?>
						
						<td class="fbox_td3" align="right">
						
							<a href="<?= base_url(); ?>files/download/sep/<?= $f->fileid ?>" class="redBoldLink">Download</a>
							<? if (checkPerms('can_delete_separations') && (!$product->islocked || checkPerms('can_edit_locked_products'))) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="confirmDeleteMfSep('sep',<?= $f->fileid ?>)" class="redBoldLink">Delete</a><? } ?>
							<? if (checkPerms('can_guest_download')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:openGuestDownloadWindow(<?= $f->fileid ?>,'sep');" class="redBoldLink">Guest</a>&nbsp;&nbsp;<? } ?>
	
	
						</td>
					
					<? } else { ?>
					
						<td class="fbox_td3" align="right"><span class="archivedFile">Archived <?= opmDate($f->archivedate) ?></span></td>
						
					
					<? } ?>
				</tr>
				
				<? } ?>
				
			</table>
			</div>
			
		</td>
	</tr>
	<tr>
		<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
	</tr>
</table>

<? } ?>