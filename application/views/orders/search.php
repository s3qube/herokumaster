
<script language="javascript">

	function changeAll(objChecker) {
	
		if (objChecker.checked == true) {

			CheckAll('orderList[]',1);
		
		} else {

			CheckAll('orderList[]',0);
		
		}
	
	}
	
	function CheckAll(field, value) {
		for (var i=0;i<document.ordersForm.elements[field].length;i++) {
			if(value == 1) {
				document.ordersForm.elements[field][i].checked = true
			} else {
				document.ordersForm.elements[field][i].checked = false
			}
		}
	}

</script>
</form>
<form name="ordersForm" method="post" action="<?= base_url() ?>orders/exportorders">

	<table border="0" class="searchorderTable">
	
		<tr class="searchorderTable">
			
			<th class="searchorderTable"><input type="checkbox" onchange="changeAll(this);"/></td>
			<th class="searchorderTable"></td>
			<th class="searchorderTable">ID</td>
			<th class="searchorderTable">From User</td>
			<th class="searchorderTable">Status</td>
			<th class="searchorderTable">Total</td>
			<th class="searchorderTable">Ref #</td>
			<th class="searchorderTable">Created</td>
			<? if (checkPerms('can_view_order_export_date')) { ?>
				<th class="searchorderTable">Exported</td>
			<? } ?>
			<th class="searchorderTable">Bill To</td>
			<th class="searchorderTable">Action</td>
		
		</tr>
	
	<? 
	
		foreach ($orders->result() as $index=>$o) { 
			
			// alt rows
			
			if ($index % 2 == 0)
				$alt = "";
			else
				$alt = "siAlt";
				
			// determine if user can edit order
			
			if ($o->statusid != 1 && (!checkPerms('can_edit_submitted_orders'))) 	
				$o->canEdit = false;
			else 
				$o->canEdit = true;
	
	?>
		
		
			<tr class="searchorderTable <?= $alt ?>">
					<td class="searchorderTable" align="center"><input type="checkbox" name="orderList[]" value="<?=$o->id?>" /></td>
		
				<td class="searchorderTable" align="center"><a href="<?= base_url() ?>orders/edit/<?= $o->id ?>"><img src="<?= base_url(); ?>resources/images/order_icon.gif" width="25" height="32" border="0" /></a></td>
				<td class="searchorderTable"><strong><?= $o->id ?></strong></td>
				<td class="searchorderTable"><strong><?= $o->customername ?></strong></td>
				<td class="searchorderTable"></td>
				<td class="searchorderTable"><?= $o->currencysymbol ?><?= number_format($o->total,2) ?></td>
				<td class="searchorderTable"><?= $o->customerpo ?></td>
				<td class="searchorderTable"><?= opmDate($o->date) ?></td>
				<? if (checkPerms('can_view_order_export_date')) { ?>
					<td class="searchorderTable"><?= ($o->exportdate ? opmDate($o->exportdate) : null) ?></td>
				<? } ?>
				<td class="searchorderTable"><strong><?= $o->companyname ?></strong></td>
				<td class="searchorderTable" align="center"><a href="<?= base_url() ?>orders/edit/<?= $o->id ?>"><?= ($o->canEdit ? "EDIT" : "VIEW") ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url() ?>orders/showPrintable/<?= $o->id ?>">PRINT</a></td>
	
			</tr>
		
	<? } ?>
	
	</table>
	
	<? if (checkPerms('can_export_orders') && $args['statusid'] == $this->config->item('invStatusApproved')) { ?>
	
		<br />
		
			<div align="right" style="margin-right:10px;"><input type="submit" name="submitBtn" value="Export Selected orders" class="orderBtn" onmouseover="this.className='orderBtnHover'" onmouseout="this.className='orderBtn'"></div>
		
		<br />
	
	<? } ?>
	
	<? if (checkPerms('can_print_multiple_orders')) { ?>
	
		<br />
		
			<div align="right" style="margin-right:10px;"><input type="submit" name="printMultipleBtn" value="Print Selected orders" class="orderBtn" onmouseover="this.className='orderBtnHover'" onmouseout="this.className='orderBtn'"></div>
		
		<br />
	
	<? } ?>

</form>
<!--<div class="searchDiv"></div>-->