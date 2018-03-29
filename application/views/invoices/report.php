<?

	$totalCharges = 0; // total charges, duhr

?>

<script language="javascript">

	var argsDataJson;

	$(document).ready(function() {
		
		// create json string of args data to be passed while getting invoice details!
		
		argsDataJson = <?= json_encode($args) ?>;
		
	});

	function loadChgTypeDetail(propertyid,chargetypeid) {
	
		if ($("#chgTypeDetail_" + propertyid + "_" + chargetypeid).is(":hidden")) {
						
			url = base_url + 'invoices/ChargeTypeDetail/' + propertyid + '/' + chargetypeid;
		
			$.post(url, argsDataJson,
   			function(data) {
   				
   				$('#chgTypeDetail_'+ propertyid + '_' + chargetypeid).html(data);
				$('#chgTypeDetail_'+ propertyid + '_' + chargetypeid).fadeIn('slow');
				//$(".invChgDetailRow").tooltip();
  			
  			 });
		
			/*$('#chgTypeDetail_'+ propertyid + '_' + chargetypeid).load(url, function() {
			
				$('#chgTypeDetail_'+ propertyid + '_' + chargetypeid).fadeIn('slow');
				$(".invChgDetailRow").tooltip();
			
			});*/
			
		
		} else {
		
			$('#chgTypeDetail_'+ propertyid + '_' + chargetypeid).fadeOut('slow');

		}
		
		/*
		
		$('#chgTypeDetail_'+ propertyid + '_' + chargetypeid).load(url);
		$('#chgTypeDetail_<?= $i->propertyid ?>_<?=$i->chargetypeid ?>').toggle('slow');
		*/
	
	}

</script>


<form name="invoicesForm" method="post" action="<?= base_url() ?>invoices/exportInvoices">

	<table border="0" class="billRptTable">
	
		<tr class="billRptTable">
			
			<th class="billRptTable">Charge Type</td>
			<th class="billRptTable">Total Charges</td>
		
		</tr>
	
	<? 
	
		$currentProp = "";
		$propertyTotal = 0;
	
		foreach ($reportData->result() as $index=>$i) { 
			
			// alt rows
			
			if ($index % 2 == 0)
				$alt = "";
			else
				$alt = "siAlt";
				
			// get total
			
			$totalCharges += $i->totalcharges;
			
			
	
	?>
			
			<?
			
				if ($args['groupByProperty'] && ($i->property != $currentProp)) { 
			
			?>
			
				<? if ($propertyTotal > 0) { ?>
				
				
					<tr class="billRptTable <?= $alt ?>">

						<td class="billRptTable"><strong><?= $currentProp ?> Total:</strong></td>
						<td align="right">$<?= number_format($propertyTotal, 2); ?></td>
		
					</tr>
					
					<tr><td><br /></td></tr>
				
					
					
				<? } ?>
			
				<tr class="billRptTable <?= $alt ?>">

					<td class="billRptTable billRptProperty"><strong><?= $i->property ?></strong></td>
					<td></td>
	
				</tr>
				
				<? $propertyTotal = 0; ?>
		
			<?  	
					
				}
				  
				$currentProp = $i->property;
				$propertyTotal += $i->totalcharges;
			?>
		
		
			<tr class="billRptTable <?= $alt ?>">

				<td class="billRptTable"><strong><?= $i->chargetype ?></strong> <a href="#" onclick="loadChgTypeDetail(<?= $i->propertyid ?>,<?=$i->chargetypeid ?>); return false;"class="invReportXpander">+</a><div id="chgTypeDetail_<?= $i->propertyid ?>_<?=$i->chargetypeid ?>" class="invRptChgTypeDetail"></div></td>
				<td class="billRptTable" align="right">$<?= number_format($i->totalcharges,2); ?></td>

			</tr>
		
		
		
		
	<? } ?>
	
		<tr>
			
			<td class="billRptTable billRptTotal">TOTAL:</td>
			<td class="billRptTable billRptTotal" align="right">$<?= number_format($totalCharges,2) ?></td>

		
		</tr>
	</table>

</form>
<!--<div class="searchDiv"></div>-->