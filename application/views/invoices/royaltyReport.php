

<? 

$timestamp2 = mktime(); 
$currentChargeType = "";

?>


<div id="royaltyReportContainer">

	<div id="royaltyReportHeader"><span style="color:#343434;">Royalty Report For:</span> <?= $prop->property ?><br /><?= $startDate ?> - <?= $endDate ?></div>
	

	
	<? 
	
		foreach ($reportData as $opm_productid => $p) { 
			
			$timestamp = mktime(); // used to gen an id for the charge row.
	?>
	
			<div class="royaltyReportProductItem">
				
				<table border="0" cellpadding="0" cellspacing="0" width="670">
				
					<tr>
						<td width="100"><div id="invChgDetailRow_<?= $timestamp ?>_<?= $opm_productid ?>" class="invChgDetailRow"><img src="<?=base_url();?>imageclass/viewThumbnail/<?=$p['default_imageid']?>/150" width="150" height="150" border="1" style="border-width:1px;border-style:solid;border-color:#cccccc;" class="tt_<?= $timestamp2 ?>" id="invChgDetailImg_<?= $timestamp ?>_<?= $opm_productid ?>" align="middle"></div></td>
						<td valign="top">
							<div class="royaltyReportChargeDetail">
								<span class="royaltyReportProduct"><?= $p['productname'] ?> - <?= $p['category'] ?></span><br />
								
							
							
								<!--<table border="0" width="550">
								
									<? foreach ($p['charges'] as $c) { ?>
										
										<? //print_r($c); ?>
								
										<tr>
										
											<td><?= $c['chargetype'] ?> (<?= $c['channelcode'] ?> - <?= $c['channel'] ?>)</td>
											<td align="right">$<?= number_format($c['chargeamount'],2) ?></td>
											
										
										</tr>
								
								
									<? } ?>
								
								</table>-->
								
								
								<table border="0" width="550">
								
									<tr>
										<th class="royaltyCharge">Charge</th>
										<th class="royaltyCharge">Vendor</th>
										<th class="royaltyCharge">Invoice #</th>
										<th class="royaltyCharge">Check #</th>
										<th class="royaltyCharge">Post Date</th>
										<th class="royaltyCharge">Channel</th>
										<th class="royaltyCharge">Amount</th>
		
									</tr>
								
									<? foreach ($p['charges'] as $c) { ?>
										
										<? //print_r($c); ?>
								
										<tr>
										
											<td><?= $c['chargetype'] ?></td>
											<td><?= $c['vendorname'] ?></td>
											<td><?= $c['invoiceid'] ?></td>
											<td><?= $c['checknumber'] ?></td>
											<td><?= opmDate($c['approvedate']) ?></td>
											<td><?= $c['channelcode'] ?></td>
											<td>$<?= number_format($c['chargeamount'],2) ?></td>
											
										
										</tr>
								
								
									<? } ?>
								
								</table>
							
							</div>
						
						</td>
					
					</tr>
				
				</table>
				
			</div>
				
				
		<? } ?>
		
		
		<div id="royaltyReportTotals">
			
			<table border="0" align="right" width="600">
				
			<? foreach ($reportTotals['channels'] as $ccode=>$c) { ?>
				
					<? if (($ccode != '700') || ($prop->isharley)) { ?>
				
						<tr>
					
							<td align="left"><?= $c['channel'] ?></td>
							<td align="left">($<?= number_format($c['subTotal'],2) ?> x <?= number_format($c['recoupRate'],2)?>%)</td>
							<td>=</td>
							<td>$<?= number_format($c['total'],2) ?></td>
				
						</tr>
					
					<? } ?>
			
			<? } ?>
			
			</table>
			
			<div style="clear:both;">&nbsp;</div>
			
			<br />
			
			<span class="royaltyReportGrandTotal">Art Expenses For Time Period: $<?= number_format($reportTotals['subTotal'],2) ?></span>
			
			<br /><br />
			
			<span class="royaltyReportGrandTotal">Recoupable Amount: $<?= number_format($reportTotals['total'],2) ?></span>
		
			<? //print_r($reportTotals) ?>
		
		</div>
		

</div>

<!--
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
-->
