

<table border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td><div style="margin-left:10px;" class="fbox_header">Master Files</div></td>
		<td align="right"><a href="javascript:openUploader(<?= $product->opm_productid ?>,'masterfile');" class="blueLink">Upload File</a>&nbsp;&nbsp;<? if (checkPerms('can_guest_upload')) { ?>|&nbsp;&nbsp;<a href="javascript:openGuestUploadWindow(<?= $product->opm_productid ?>,'mf');" class="blueLink">Setup Guest Upload</a><? } ?></td>
	</tr>
	<tr>
		<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
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
					<td class="fbox_td3" align="right">
					
						<a href="<?= base_url(); ?>files/download/mf/<?= $f->fileid ?>" class="redBoldLink">Download</a>
						<? if (checkPerms('can_delete_masterfiles')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="confirmDeleteMfSep('mf',<?= $f->fileid ?>)" class="redBoldLink">Delete</a><? } ?>
						<? if (checkPerms('can_guest_download')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:openGuestDownloadWindow(<?= $f->fileid ?>,'mf');" class="redBoldLink">Guest</a>&nbsp;&nbsp;<? } ?>
					
					</td>
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

<br /><br />

<? if (checkPerms('can_view_separations')) { ?>

	<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td><div style="margin-left:10px;" class="fbox_header">Separations</div></td>
			<td align="right"><a href="javascript:openUploader(<?= $product->opm_productid ?>,'separation');" class="blueLink">Upload File</a>&nbsp;&nbsp;<? if (checkPerms('can_guest_upload')) { ?>|&nbsp;&nbsp;<a href="javascript:openGuestUploadWindow(<?= $product->opm_productid ?>,'sep');" class="blueLink">Setup Guest Upload</a><? } ?></td>
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
						<td class="fbox_td3" align="right">
						
							<a href="<?= base_url(); ?>files/download/sep/<?= $f->fileid ?>" class="redBoldLink">Download</a>
							<? if (checkPerms('can_delete_separations')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="confirmDeleteMfSep('sep',<?= $f->fileid ?>)" class="redBoldLink">Delete</a><? } ?>
							<? if (checkPerms('can_guest_download')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:openGuestDownloadWindow(<?= $f->fileid ?>,'sep');" class="redBoldLink">Guest</a>&nbsp;&nbsp;<? } ?>
	
	
						</td>
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