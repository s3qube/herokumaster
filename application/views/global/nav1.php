							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									
									<? if (checkPerms('view_products_tab')) { ?>
										<? if ($this->opm->activeNav == 'products') { ?>
											<td class="nav1"><a href="<?=base_url();?>search/doSearch" class="nav1_ov">PRODUCTS</a></td>
										<? } else { ?>
											<td class="nav1"><a href="<?=base_url();?>search/doSearch" class="nav1" onmouseover="this.className='nav1_ov'" onmouseout="this.className='nav1'">PRODUCTS</a></td>
										<? } ?>
									<? } ?>
									
									<? if (checkPerms('view_properties_tab')) { ?>
										<? if ($this->opm->activeNav == 'properties') { ?>
											<td><a href="<?=base_url();?>properties"><img src="<?=base_url();?>resources/images/nav_properties_over.gif" id="nav1_properties" alt="properties" width="77" height="10" border="0" class="nav1_item" /></a></td>
										<? } else { ?>
											<td><a href="<?=base_url();?>properties" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('nav1_properties','','<?=base_url();?>resources/images/nav_properties_over.gif',1)"><img src="<?=base_url();?>resources/images/nav_properties.gif" id="nav1_properties" alt="properties" width="77" height="10" border="0" class="nav1_item" /></a></td>
										<? } ?>
									<? } ?>
									
									<? if (checkPerms('view_production_tab')) { ?>
										<? if ($this->opm->activeNav == 'production') { ?>
											<td class="nav1"><a href="<?=base_url();?>grabsheets/search" class="nav1_ov">PRODUCTION</a></td>
										<? } else { ?>
											<td class="nav1"><a href="<?=base_url();?>grabsheets/search" class="nav1" onmouseover="this.className='nav1_ov'" onmouseout="this.className='nav1'">PRODUCTION</a></td>
										<? } ?>
									<? } ?>
									
									<? if (checkPerms('view_wholesale_tab')) { ?>
										<? if ($this->opm->activeNav == 'wholesale') { ?>
											<td class="nav1"><a href="<?=base_url();?>wholesale" class="nav1_ov">WHOLESALE</a></td>
										<? } else { ?>
											<td class="nav1"><a href="<?=base_url();?>wholesale" class="nav1" onmouseover="this.className='nav1_ov'" onmouseout="this.className='nav1'">WHOLESALE</a></td>
										<? } ?>
									<? } ?>
									
									
									<? if (checkPerms('view_billing_tab')) { ?>
										<? if ($this->opm->activeNav == 'billing') { ?>
											<td class="nav1"><a href="<?=base_url();?>invoices/search" class="nav1_ov">BILLING</a></td>
										<? } else { ?>
											<td class="nav1"><a href="<?=base_url();?>invoices/search" class="nav1" onmouseover="this.className='nav1_ov'" onmouseout="this.className='nav1'">BILLING</a></td>
										<? } ?>
									<? } ?>
									
									
									<? if (checkPerms('view_assets_tab')) { ?>
										<? if ($this->opm->activeNav == 'assets') { ?>
											<td class="nav1"><a href="<?=base_url();?>assets/search" class="nav1_ov">ASSETS</a></td>
										<? } else { ?>
											<td class="nav1"><a href="<?=base_url();?>assets/search" class="nav1" onmouseover="this.className='nav1_ov'" onmouseout="this.className='nav1'">ASSETS</a></td>
										<? } ?>
									<? } ?>
									
									
									<? if (checkPerms('view_administration_tab')) { ?>
										<? if ($this->opm->activeNav == 'administration') { ?>
											<td class="nav1"><a href="<?=base_url();?>users/search/0/0/0/0/0/0/" class="nav1_ov">ADMIN</a></td>
										<? } else { ?>
											<td class="nav1"><a href="<?=base_url();?>users/search/0/0/0/0/0/0/" class="nav1" onmouseover="this.className='nav1_ov'" onmouseout="this.className='nav1'">ADMIN</a></td>
										<? } ?>
									<? } ?>
								
								</tr>
							</table>