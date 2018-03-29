
<script language="javascript">

	function changeAll(objChecker) {
	
		if (objChecker.checked == true) {

			CheckAll('invoiceList[]',1);
		
		} else {

			CheckAll('invoiceList[]',0);
		
		}
	
	}
	
	function CheckAll(field, value) {
		for (var i=0;i<document.invoicesForm.elements[field].length;i++) {
			if(value == 1) {
				document.invoicesForm.elements[field][i].checked = true
			} else {
				document.invoicesForm.elements[field][i].checked = false
			}
		}
	}

</script>

<form name="invoicesForm" method="post" action="<?= base_url() ?>invoices/exportInvoices">

	<table border="0" class="searchInvoiceTable">
	
		<tr class="searchInvoiceTable">
			
			<? if (checkPerms('can_export_invoices')) { ?>
			<th class="searchInvoiceTable"><input type="checkbox" onchange="changeAll(this);"/></td>
			<? } ?>
			<th class="searchInvoiceTable"></td>
			<th class="searchInvoiceTable">ID</td>
			<th class="searchInvoiceTable">From User</td>
			<th class="searchInvoiceTable">Status</td>
			<th class="searchInvoiceTable">Title</td>
			<th class="searchInvoiceTable">Total</td>
			<th class="searchInvoiceTable">Ref #</td>
			<th class="searchInvoiceTable">Created</td>
			<th class="searchInvoiceTable">Action</td>
		
		</tr>
	
	<? 
	
		foreach ($invoices->result() as $index=>$i) { 
			
			// alt rows
			
			if ($index % 2 == 0)
				$alt = "";
			else
				$alt = "siAlt";
				
			// determine if user can edit invoice
			
			if ($i->statusid != 1 && (!checkPerms('can_edit_submitted_invoices'))) 	
				$i->canEdit = false;
			else 
				$i->canEdit = true;
	
	?>
		
		
			<tr class="searchInvoiceTable <?= $alt ?>">
				<? if (checkPerms('can_export_invoices')) { ?>
					<td class="searchInvoiceTable" align="center"><input type="checkbox" name="invoiceList[]" value="<?=$i->id?>" /></td>
				<? } ?>
				<td class="searchInvoiceTable" align="center"><a href="<?= base_url() ?>invoices/edit/<?= $i->id ?>"><img src="<?= base_url(); ?>resources/images/invoice_icon.gif" width="25" height="32" border="0" /></a></td>
				<td class="searchInvoiceTable"><strong><?= $i->id ?></strong></td>
				<td class="searchInvoiceTable"><strong><?= $i->username ?></strong></td>
				<td class="searchInvoiceTable"><?= printInvoiceStatus($i->status) ?></td>
				<td class="searchInvoiceTable"><?= $i->title ?></td>
				<td class="searchInvoiceTable">$<?= number_format($i->total,2) ?></td>
				<td class="searchInvoiceTable"><?= $i->referencenumber ?></td>
				<td class="searchInvoiceTable"><?= opmDate($i->createdate) ?></td>
				<td class="searchInvoiceTable" align="center"><a href="<?= base_url() ?>invoices/edit/<?= $i->id ?>"><?= ($i->canEdit ? "EDIT" : "VIEW") ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url() ?>invoices/showPrintable/<?= $i->id ?>">PRINT</a></td>
	
			</tr>
		
		
		
		
	<? } ?>
	</table>
	
	<? if (checkPerms('can_export_invoices')) { ?>
	
		<br />
		
			<div align="right"><input type="submit" name="submitBtn" value="Export Invoices" class="invoiceBtn" onmouseover="this.className='invoiceBtnHover'" onmouseout="this.className='invoiceBtn'"></div>
		
		<br />
	
	<? } ?>

</form>
<!--<div class="searchDiv"></div>-->