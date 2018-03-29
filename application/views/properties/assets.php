
<? if (checkPerms('can_upload_assets')) { ?>

	<div style="width:693px;margin-left:auto;margin-right:auto;">
	
		<div style="text-align:right"><a href="<?= base_url(); ?>upload/showAssetUpload/<?=$p->propertyid?>" class="blueLink">Upload New Asset</a></div>
		
		<br />
			
	</div>
	

<? } ?>

<? //print_r($product) ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					
				
						
						<? 
						
						$count = 1;
						$rowCount = 1;
						$curAssetType = "";
						
						foreach ($assets->result() as $a) { 
						
						?>
						
							<? 
							
								if ($a->assettype != $curAssetType) { 
									$count = 1;
							?>
									
								<? if ($rowCount > 1) { ?>
								
										</tr>
									</table>
									<br /><br />
								<? } ?>
							
								<h2><?= $a->assettype ?></h3>
								<hr class="assetDivider" /><br />
								<table border="0">
									<tr>
							
							<? } ?>


										<td valign="top">
											<div class="assetImage"><img src="<?= base_url(); ?>imageclass/viewAssetThumbnail/<?=$a->assetid?>" style="border-width:1px;border-color:#333333;border-style:solid;"></div>
											<div class="assetInfoContainer">
												<span class="assetHeader"><?= $a->assetname ?></span><br />
												<span class="assetDetail"><?= $a->assetdetail ?></span><br /><br />
												<!--<span class="assetFileName"><?= $a->filename ?></span>-->
												<a href="<?= base_url(); ?>files/download/asset/<?= $a->assetid ?>" class="regBlueLink" title="Download <?= $a->filename ?>">Download</a>&nbsp;&nbsp|&nbsp;&nbsp;<a href="<?= base_url(); ?>files/delete/asset/<?= $a->assetid ?>" class="regBlueLink">Delete</a>
											</div>
										</td>
							
						
						<? 
							$count++;
							
							if ($count > 3) {
								echo "</tr><tr><td><br></td></tr><tr>";
								$count = 1;
							}
							
							$curAssetType = $a->assettype;
							$rowCount++;	
						} 
						
						?>
					
					
					
			</td>
		</tr>
	</table>
	

	