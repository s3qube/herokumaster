
{"meta":{"totalResults":"<?= $totalThumbnails ?>", "numPages":"<?= $numPages ?>", "perPage":"<?= $perPage ?>","pageNum":"<?= $pageNum ?>"},
"items":[
	
	
	<? foreach ($thumbnails->result() as $p) { ?>
		
			{"property":"<?=htmlentities($p->property)?>", "product":"<?=htmlentities($p->productname)?>", "imageid":"<?=$p->imageid?>", "approvalStatus":"<?= $p->approvalstatus ?>", "lastpurchase_account":"<?= $p->lastpurchase_account ?>", "lastpurchase_timestamp":"<?= $p->lastpurchase_timestamp ?>"},				
	
	<? } ?>

]}

