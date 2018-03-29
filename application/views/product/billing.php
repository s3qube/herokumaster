


<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px;">
	
	
		
		<tr>
			<td valign="top" colspan="2">
			
				<? if (checkPerms("can_view_product_skus")) { ?>
			
					<div id="productSkuArea">
					
						<span class="boldHeader">SKUs:</span>
										
						<form name="skuForm" method="post" action="<?=base_url();?>products/regenerateSkus">
						
						<table border="0" class="skuTable">
						
								<input type="hidden" name="opm_productid" value="<?= $id ?>" />
								
								<tr class="skuTable">
								
									<th class="skuTable">Color</th>
									<th class="skuTable">Size</th>
									<th class="skuTable">SKU</th>
								
								</tr>
								
								<? foreach ($p->skus as $s) { ?>
								
									<tr class="skuTable">
									
										<td class="skuTable"><?= $s->color ?></td>
										<td class="skuTable"><?= $s->sizecode ?></td>
										<td class="skuTable"><?= $s->sku ?></td>
									
									</tr>
								
								<? } ?>
								
								<tr class="skuTable">
									<td colspan="8" align="right"><input type="submit" value="Regenerate SKUs" /></td>
								</tr>
						
											
						</table>
					</form>
		
					</div>
				
				<? } ?>
				
				<br /><br />
			
			</td>
		</tr>

	
	
	
	<tr>
		<td valign="top">
			
			<? if (checkPerms("can_view_product_invoices")) { ?>
			
				<div id="">
				
					<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="390" height="30">
						<tr>
							<td class="boldHeader">Invoices Containing This Product:</td>
							<td align="right"></td>
						</tr>
						<tr>
						
							<td colspan="2">
							
									<br />
															
									<table border="0">
		
									<? 
										$curInvoiceid = 0;
										$totalCharges = 0.00;
									
										foreach ($invoiceItems as $i) {
										
									?>
											
											
											<? if ($curInvoiceid != $i->invoiceid) {  // this is a distinct invoice, show invoice info ?>
												
												
												<? if ($curInvoiceid != 0) {  // don't do a <br> if this is the first invoice item....?>
												
													<tr>
														<td colspan="2"><br /></td>
													</tr>
												
												<? } ?>
												
												
												<tr>
													<td colspan="2"><a href="<?= base_url(); ?>invoices/edit/<?= $i->invoiceid ?>" class="invLiProdText">#<?= $i->invoiceid ?> - <?= $i->username ?></a></td>
												</tr>	
												
											
											<? } ?>			
									
									
												
												<tr>
												
													<td><div class="billpage_invDetail"><?= $i->chargetype ?></div></td>
													<td align="right"><div class="billpage_invDetail">$<?= number_format($i->chargeamount,2) ?></div></td>
													
												</tr>
									
									<? 
	
											$totalCharges += $i->chargeamount;
											$curInvoiceid = $i->invoiceid;
											
										
										} 
										
										
										
									?>
									
										<? if ($totalCharges) { ?>
										
											<tr>
												<td colspan="2" class="invLiProdText"><br /><br />TOTAL CHARGES: $<?= number_format($totalCharges,2) ?></td>
											</tr>
											
										<? } else { ?>
										
											<tr>
												<td colspan="2" class=""><br /><br />No Invoices.</td>
											</tr>
											
										
										<? } ?>
									
									</table>
	
							
							</td>
							
						</tr>
					</table>
					
				</div>	
			
			<? } ?>
			
					
		</td>
		
		<td valign="top" align="right">
		
			<? if (checkPerms("can_add_product_to_invoices")) { ?>
		
				<div id="">
				
					<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
						<tr>
							<td class="boldHeader">Add This Product To:</td>
							<td align="right"><!--<a href=""><img src="<?=base_url();?>/resources/images/inv_groupchk.gif" width="26" height="26" border="0"></a><a href="#"><img src="<?=base_url();?>/resources/images/inv_groupx.gif" width="26" height="26" border="0"></a>--></td>
						</tr>
						<tr>
						
							<td colspan="2">
								
								<form name="addProductToInvoiceForm" method="post" action="<?= base_url() ?>invoices/edit/addProduct">
								
									<br /><br />
									
									<input type="hidden" name="opm_productid" value="<?= $id ?>" />
								
									<select name="invoiceid">
						
										<option value="">Select Invoice...</option>
										
										<? foreach ($userInvoices->result() as $i) { ?>
									
											<option value="<?= $i->id ?>">Invoice #<?=$i->id ?></option>					
										
										<? } ?>
									
									</select>
									<input type="image" align="absmiddle" src="<?= base_url(); ?>resources/images/btn_add.gif" border="0" style="border:none;" />
									<!--<input type="submit" value="Add" name="addProduct" />-->
								
								</form>
								
							</td>
							
						</tr>
					</table>
	
				</div>
			
			<? } ?>
						
		</td>
	</tr>
</table>

<br />
<br />



	
	