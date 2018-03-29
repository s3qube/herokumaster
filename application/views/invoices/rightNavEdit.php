
<? if (is_object($invoice)) { // $invoice->canEdit?>

	<div style="margin-top:-8px; text-align:right;" <? if (1==2) { ?> class="invModule" onclick="editInvoice();" <? } else { ?> class="invModuleNoEdit" <? } ?>>

<? } ?>

<table border="0" cellpadding="0" cellspacing="0">

	<tr>
	
		<td>
		
			<? if (isset($invoice->status)) { ?> Status: <?=  printInvoiceStatus($invoice->status,true); ?><br /><? } ?>
			Submit To: <?= (isset($invoice->owner)) ? $invoice->owner : "TBD" ?>
		
		</td>
		
		<? if ($mode != 'print') { ?>
		
			<td style="padding-left:10px;">
			
				<table border="0" class="tblInvoiceIcons">
					<tr>
						<? if (isset($invoice->id) && $mode != 'print') { ?>
							<td class="tblInvoiceIcons"><a href="<?= base_url() ?>invoices/showPrintable/<?=  $invoice->id ?>" title="Print This Invoice"><img src="<?= base_url() ?>resources/images/print_icon_off.gif" alt="Print This Invoice" width="37" height="37" /></a></td>
						<? } ?>
							
						<? if (isset($invoice->id) && $mode != 'print' && checkPerms('can_copy_invoices')) { ?>
							<td class="tblInvoiceIcons"><a href="<?= base_url() ?>invoices/copyInvoice/<?=$invoice->id?>" title="Copy This Invoice"><img src="<?= base_url() ?>resources/images/copy_icon_off.gif" alt="Copy This Invoice" width="37" height="37" /></a></td>
						<? } ?>
						
						<? if (isset($invoice->statusid) && $invoice->statusid != $this->config->item('invStatusSentToNavision') && $invoice->statusid != $this->config->item('invStatusDeleted') && $invoice->statusid != $this->config->item('invStatusPaid') && $mode != 'print' && checkPerms('can_delete_invoices')) { ?>
							<td class="tblInvoiceIcons"><a href="#" onclick="return confirmDeleteInvoice();" title="Delete This Invoice"><img src="<?= base_url() ?>resources/images/trash_icon_off.gif" alt="Delete This Invoice" width="37" height="37" /></a></td>
						<? } ?>
					</tr>
				</table>
			
			</td>
		
		<? } ?>
		
	</tr>
	
</table>


</div>
