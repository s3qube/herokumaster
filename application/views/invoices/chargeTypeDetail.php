
<? $timestamp2 = mktime(); ?>

<div class="chgDetails_<?= $timestamp2 ?>">

<? 

	foreach ($charges->result() as $index=>$c) { 
		
		$timestamp = mktime(); // used to gen an id for the charge row.
?>

			
			<div id="invChgDetailRow_<?= $timestamp ?>_<?= $index ?>" class="invChgDetailRow"><img src="<?=base_url();?>imageclass/viewThumbnail/<?=$c->default_imageid?>/25" width="25" height="25" border="0" class="tt_<?= $timestamp2 ?>" id="invChgDetailImg_<?= $timestamp ?>_<?= $index ?>" align="middle">&nbsp;&nbsp;Invoice # <?= $c->invoiceid ?> - $<?= number_format($c->chargeamount,2) ?></div>
			
			<div class="invChgDetailTooltip" id="invChgDetailTooltip_<?= $timestamp ?>_<?= $index ?>">
			
				<img src="<?=base_url();?>imageclass/viewThumbnail/<?=$c->default_imageid?>/150" width="150" height="150" border="0">
			
				<div class="invoiceReportTooltipText">
			
					<?= $c->property ?><br />
					<?= $c->productname ?><br />
					
				
				</div>
			
			</div>



<? 

	} 

?>

</div>

<script language="javascript">

	$("#chgDetails_<?= $timestamp2 ?>").ready(function()
    { 
    
    	$('.tt_<?= $timestamp2 ?>').each(function(index) {
    	
    		elementID = $(this).attr('id');
    		tooltipID = elementID.replace("invChgDetailImg", "invChgDetailTooltip");
    		
    		//alert(elementID);
    		//alert(tooltipID);
    		
    		$("#" + elementID).tooltip({

				// use div.tooltip as our tooltip
				tip: '#'+tooltipID,
	
				// use the fade effect instead of the default
				effect: 'fade'
	
	
		
			});
 		
 		});
    	
    	/*$(".tt_<?= $timestamp2 ?>").tooltip({

			// use div.tooltip as our tooltip
			tip: '.invChgDetailTooltip',
	
			// use the fade effect instead of the default
			effect: 'fade'
	
	
		
		});*/
   
    });

</script>

