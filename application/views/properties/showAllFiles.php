
<form name="dlFilesForm" action="<?= base_url() ?>properties/createZipFile" method="post">

<input type="hidden" name="propertyid" value="<?= $p->propertyid ?>" />


<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	
	<tr>
		<td valign="top" align="right"><input type="submit" value="Download Files"></td>
	</tr>
	
	<tr>
		<td valign="top">
			
			<? 
				
				$curProductName = "";
			
				foreach ($files as $f) { 
				
			?>
			
				<? if ($curProductName != $f['productname']) { ?>
				
					<h3 class="showFilesProductName"><?= $f['productname'] ?></h3>
					
				<? } ?>
				
				<!-- Globosoft -->
				   <!-- <div class="prodSummaryImageTN"></div>-->
                <!-- Globosoft -->
				
					<div class="showFilesFilename"><a href="<?=base_url();?>products/view/<?=$f['opm_productid']?>" <? if ($f['default_imageid']) { ?>  <? } ?>><img src="<?=base_url();?>imageclass/viewThumbnail/<?=$f['default_imageid']?>" width="60" height="60" border="0" align="absmiddle" class="showAllFilesTN"></a><input type="checkbox" name="<?= ($f['dbtype'] == 'Masterfile') ? "MFsToDL" : "SEPsToDL"; ?>[<?=$f['fileid']?>]">&nbsp;&nbsp;<a href="<?= base_url(); ?>files/download/mf/<?= $f['fileid'] ?>" class="redBoldLink"><?= $f['filename'] ?></a>&nbsp;&nbsp;//&nbsp;&nbsp;<?= $f['dbtype'] ?>&nbsp;&nbsp;//&nbsp;&nbsp;<?= byteSize($f['filesize']) ?></div>
			
			<? 
			
					$curProductName = $f['productname'];
				} 
			
			
			?>

		</td>

	</tr>
	
	<tr>
		<td valign="top" align="right"><input type="submit" value="Download Files"></td>
	</tr>

</table>

</form>