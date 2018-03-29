
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>Edit Invoice # <?= (isset($invoice->id)) ? $invoice->id : "TBD" ?></td>
		<td align="right">
		
			<a href="#" onclick="viewHistory(); return false;" style="color:#666666;">View History</a>
		
			<? if ((checkPerms('can_refresh_billing_info')) && isset($invoice->statusid) && $invoice->statusid != $this->config->item('invStatusSentToNavision') && $invoice->statusid != $this->config->item('invStatusDeleted') && $invoice->statusid != $this->config->item('invStatusPaid')  && $mode != 'print') { ?>
			
				&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>invoices/refreshBillingInfo/<?= $invoice->id ?>" style="color:#666666;">Refresh Billing Info</a>
			
			<? } ?>
			
			<? if ((!checkPerms('can_change_invoice_user')) && isset($invoice->statusid) && ($invoice->userid == $this->userinfo->userid)) { ?>
			
				&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>/invoices/editInfo/<?= $this->userinfo->userid ?>/invoicing" style="color:#666666;">Edit Billing Info</a>
			
			<? } ?>
		
		</td>
	</tr>
</table>
