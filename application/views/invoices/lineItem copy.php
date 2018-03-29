<input type="hidden" name="opm_productid[<?= $i->id ?>]" value="<?= $i->opm_productid ?>" />

<? if ($altRow) { ?>
	<div class="invoiceRowAlt">
<? } else { ?>
	<div class="invoiceRow">
<? } ?>

	<table border="0" class="invoiceLineItemTable">
		<tr>
			<td width="100"><div class="prodSummaryImageTN"><a href="<?=base_url();?>products/view/<?= $i->opm_productid ?>" <? if ($i->default_imageid) { ?> class="tipper" title="AJAX:<?= base_url();?>tooltips/searchTip/<?=$i->default_imageid?>" <? } ?>><img src="<?=base_url();?>imageclass/viewThumbnail/<?= $i->default_imageid ?>" width="60" height="60" border="0"></a></div></td>
			<td valign="top">
				<span class="invoiceProductName"><?= $i->property ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?= $i->productname ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?= $i->category ?></span>
				
				<div class="invoiceChargeRow">
				
					<select name="chargeType[<?=$i->id?>]" class="invoiceChargeType" <? if ($locked) echo "DISABLED"; ?>>
					
						<option value="0">Please Select...</option>
						
						
						
						<? foreach ($chargeTypes as $id=>$chargeType) { ?>
						
							<option value="<?= $id ?>" <? if ($i->chargetypeid == $id) echo "SELECTED"; ?>><?=$chargeType?></option>
						
						<? } ?>
					
					</select>
					
					<span id="chargeAmount_<?= $i->id ?>" style="display:none;">$<input type="text" name="chargeAmount[<?= $i->id ?>]" class="invoicePrice" value="<?= $i->chargeamount ?>" onBlur="getTotals();" <? if ($locked) echo "DISABLED"; ?> /></span>
					<span id="notes_<?= $i->id ?>" style="display:none;">Notes:<input type="text" name="notes[<?= $i->id ?>]" class="invoiceNotes" value="<?= $i->notes ?>" <? if ($locked) echo "DISABLED"; ?> />
				
				</div>
			</td>
			<td align="right"><? if (!$locked) { ?><input type="submit" name="remove[<?= $i->id ?>]" value="remove" class="invRemoveBtn" <? if ($locked) echo "DISABLED"; ?> /><? } ?></td>
		</tr>
	</table>
</div>

<div class="invoiceDiv"></div>
