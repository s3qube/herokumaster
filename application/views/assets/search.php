
 <script type="text/javascript">

	 CloudZoom.quickStart();
 
 </script>    

<div class="assetItems">
		
	<? foreach ($assets->result() as $a) { ?>
	
		<div class="assetItem">
	
			<div class="assetImage"><? if ($a->hasthumbnail) { ?><a href="<?= base_url(); ?>assets/edit/<?= $a->assetid?>"><img class="cloudzoom" src="<?= base_url(); ?>imageclass/viewAssetThumbnail/<?=$a->assetid?>/200" style="border-width:1px;border-color:#333333;border-style:solid;" data-cloudzoom = "zoomImage: '<?= base_url(); ?>imageclass/viewAssetThumbnail/<?=$a->assetid?>/1200'"></a> <? } ?> </div>
			<div class="assetInfoContainer">
				<div class="assetButtons"><a href="<?= base_url(); ?>files/download/asset/<?= $a->assetid ?>" title="Download <?= $a->filename ?>"><img src="<?= base_url(); ?>resources/images/assetBtnDL.png" id="assetBtnDL" width="18" height="22" /></a><img src="<?= base_url(); ?>resources/images/assetBtnDiv.png" id="assetBtnDiv" width="1" height="23" /><a href="<?= base_url(); ?>files/delete/asset/<?= $a->assetid ?>"><img src="<?= base_url(); ?>resources/images/assetBtnDEL.png" id="assetBtnDEL" width="20" height="22" /></a></div>
				<div class="assetHeader"><a href="<?= base_url(); ?>assets/edit/<?= $a->assetid?>"><?= $a->property ?><br /><?= ($a->assetname ? $a->assetname : $a->filename) ?></div></a><br />
				<div class="assetAuthor">Author: <?= $a->author ?></div>
				<div class="assetAuthor">Type: <?= $a->filetype ?></div>
				<div class="assetAuthor">Dimensions: <?= $a->dimensions ?></div>
				<div class="assetAuthor">Tags: <?= $a->tags ?></div>
				<!--<span class="assetFileName"><?= $a->filename ?></span>-->
				<!--<a href="<?= base_url(); ?>files/download/asset/<?= $a->assetid ?>" class="regBlueLink" title="Download <?= $a->filename ?>">Download</a>&nbsp;&nbsp|&nbsp;&nbsp;<a href="<?= base_url(); ?>files/delete/asset/<?= $a->assetid ?>" class="regBlueLink">Delete</a>-->
			</div>
		
		</div>
	
	<? } ?>

</div>


		
		
		

	
	