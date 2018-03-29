


<? 
	$currentID = 0;
	
	foreach ($invoice->items as $index=>$i) { 
	
?>
		
		
			<? if ($i->opm_productid != $currentID) { ?>
					
			
				
				<? if ($index != 0) { // write divider if we arent on first product ?>
				</table>
				<br /><br />
				<table>
					<tr>
						<td><hr /></td>
					</tr>
				</table>
				<br />
				<? } ?>
				
			
				
				<table cellpadding="4">	
				
					<tr>
						<td width="365" align="left" valign="top" rowspan="100"><? if ($i->default_imageid && isset($createdImages[$i->id])) { ?><img src="<?=base_url();?>resources/images/temp/invoice/<?= $i->default_imageid ?>.jpg" width="275" height="275" border="1"><? } else { ?> <img src="<?=base_url();?>resources/images/no_image.gif" width="275" height="275" border="1"><? } ?></td>
						<td width="1500" align="left" valign="top"><span style="font-size:11pt; font-weight:bold;"><?= $i->property ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?= $i->productname ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?= $i->category ?></span></td>
						<td></td>
					</tr>
			
			<? } ?>
			
			<tr>
				<td><span style="font-weight:normal;font-size:10pt;"><?= $i->chargetype ?></span></td>
				<td>$<?= $i->chargeamount ?></td>
			</tr>
			
	
		
			
<? 
		$currentID = $i->opm_productid;
	} 
	
?>

<br /><br />

<table>

	<tr>
		<td width="2010" align="right"><span style="font-weight:bold; font-size:16pt;">TOTAL: $<?= $invoice->total ?></span></td>
	</tr>

</table>


			