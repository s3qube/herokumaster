
<? if ($path) { ?>
	
	<table border="0" class="searchProductTable">
			<tr>
				<td valign="middle"><a href="<?= $goUpURL ?>"><img src="<?= base_url() ?>resources/images/folder.gif" alt="folder" width="78" height="59" border="0" align="middle" />&nbsp;&nbsp;&uarr;</a></td>
			</tr>
	</table>
	
	<br /><br />


<? } ?>

<? if (isset($arrFiles)) { ?>
		
	<? foreach ($arrFiles as $f) { ?>
	
		<table border="0" class="searchProductTable">
			<tr>
				<td valign="middle">
					
					<? if (!isset($f['is_dir'])) { ?>
					
						<? if (isset($f['thumb_url'])) { ?>
					
							<img src="<?= base_url() ?>templates/fetchThumb/<?= urlencode($f['thumb_url']) ?>"  align="middle" />&nbsp;&nbsp;
					
						<? } else { ?>
					
							<img src="<?= base_url() ?>resources/images/no_image.gif" align="middle" />&nbsp;&nbsp;

						
						<? } ?>
						
						<span class="propertyName">
						
							<a href="<?= $f['download_url'] ?>"><?= $f['filename']?></a>
					
						</span>
					
					<? } elseif (isset($f['is_dir'])) { ?>
					
						<a href="<?= $f['folder_url'] ?>"><img src="<?= base_url() ?>resources/images/folder.gif" alt="folder" width="78" height="59" border="0" align="middle" /></a>&nbsp;&nbsp;
					
						<span class="propertyName">
						
							<a href="<?= $f['folder_url'] ?>"><?= $f['filename']?></a>
					
						</span>
						
					
					<? } ?>
					
					
					
					<span class="searchProductInfo"></span>
				
				</td>
			</tr>
		</table>
		
		<br />
		
		
		
	<? } ?>
	
<? } else { ?>

	<table border="0" class="searchProductTable">
			<tr>
				<td valign="middle">
					
										
					<span class="propertyName">Folder is empty.</span>
				
				</td>
			</tr>
		</table>
		
		<div class="searchDiv"></div>


<? } ?>

<pre>

	<? //print_r($arrFiles) ?>

</pre>


		
		
		

	
	