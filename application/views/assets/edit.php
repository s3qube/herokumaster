
<? //print_r($product) ?>

<script language="javascript">
		
		

</script>

<form name="userform" action="<?= base_url(); ?>assets/save" method="post" enctype="Multipart/Form-Data">

	<input type="hidden" name="assetid" value="<?= $a->assetid ?>">

	<div class="contentWrapper">
		
		<div class="assetEditItem">
	
			<div class="assetEditImage"><img src="<?= base_url(); ?>imageclass/viewAssetThumbnail/<?=$a->assetid?>/400" style="border-width:1px;border-color:#333333;border-style:solid;"></div>
		
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
	
	</div>
	

	
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="right"><input type="submit" value="Save">&nbsp;&nbsp;&nbsp;</td>
		</tr>
	</table>
	
	<div style="clear:both;"></div>
	

</form>

<br />	
	