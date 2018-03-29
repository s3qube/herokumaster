<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
	
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery-1.3.2.min.js"></script>
			
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery.autocomplete.js"></script>

	
		<script language = "javascript">
		
			<? if (isset($result)) { ?>
				
					
					window.parent.reloadInvoice();
					
					<? if ($result == 1) { ?>
					
						<? if ($url == 'DONE') { ?>
						
							var timerID = setTimeout("window.parent.Shadowbox.close();", 600);
						
						<? } else { ?>

							var timerID = setTimeout("document.redirForm.submit();", 600);
						
						<? } ?>
					
					
					<? } ?>
					
			<? } ?>
		
			/*var options, a;
	
			jQuery(function(){
		  	
		  		options = { serviceUrl:'<?= base_url(); ?>ajax/invoiceQuery',minChars:2,deferRequestBy: 300 };
		  		a = $('#query').autocomplete(options);
			
			});*/
			
			function getChargeDetails(chargetypeid) {
				
				$('#productSelect').load('<?= base_url(); ?>invoices/ajaxGetChargeDetails/<?= $invoiceid ?>/' + chargetypeid);
				$("#selBtn").fadeIn("slow");
			}
			
			
			function checkForm() {
			
				return true;
			
			}
			
		</script>
		
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_invoicepopup_styles.css">
	</head>
	<body>
	
		<? if (isset($result)) { ?>
		
			<!-- This form get submitted when we "add another charge" or product -->
			
			<form name="redirForm" method="post" action="<?= base_url();?><?= $url ?>/<?= $invoiceid ?>">
			
				<input type="hidden" name="opm_productid" value="<?= $opm_productid ?>" />
				
			</form>
		
		<? } ?>

		
		<? if (!isset($result)) { ?>
		
			<div id="container">
			
				<form name="prodForm" method="POST" action="<?= base_url(); ?>invoices/addEditCharge/<?= $invoiceid ?>" onsubmit="return checkForm();">
				
					<input type="hidden" name="invoiceid" value="<?= $invoiceid ?>" />
					<input type="hidden" name="opm_productid" value="<?= $opm_productid ?>" />
					
					<? if (isset($c->id)) { ?>
					
						<input type="hidden" name="chargeid" value="<?= $c->id ?>" />
					
					<? } ?>
					
					<h3 class="invoicePopLabel">Add Charge:</h3>
					
					<select id="chargetypeid" name="chargetypeid" class="invoicePopField" onchange="getChargeDetails(this.value);">
						
						<option value="0">Please Select...</option>
							
						<? foreach ($chargeTypes as $id=>$ct) { ?>
						
							<option value="<?= $id ?>" <? if (isset($c->chargetypeid) && $c->chargetypeid == $id) echo "SELECTED"; ?>><?= $ct ?></option>
						
						<? } ?>
						
												
					</select>
					
					<div id="productSelect"><? if (isset($chargeDetail)) echo $chargeDetail ?></div>
					
					<div id="imageArea"></div>
					
					
					<? if (isset($c->id)) { ?>
						
						<div class="invoicePopBtn" id="selBtn">
						
							<input type="submit" name="saveCharge" class="invoicePopBtn" value="Save Charge" /><br />
							<input type="submit" name="removeCharge" class="invoicePopBtn" value="Remove Charge" /><br />

						</div>
					
					
					<? } else { ?>
					
						<div class="invoicePopBtn" id="selBtn" style="display:none;">
						
							<input type="submit" name="addThisCharge" class="invoicePopBtn" value="Add This Charge" /><br />
							<input type="submit" name="addThisChargeAddAnother" class="invoicePopBtn" value="Add This Charge and Add Another For This Product" />
							<input type="submit" name="addAnotherProduct" class="invoicePopBtn" value="Add This Charge and Add A Different Product" />
						
						</div>
					
					<? } ?>					
					
				
				</form>
			</div>
			
			
		<? } else { ?>
		
			
			<br /><br /><br />
				
			<div style="text-align:center; width:90%;"><?= $resultText ?>
			
			
				<? if ($result == 0) { ?>
				
					<br /> <br /> <br /> <br />
					
					<div style="text-align:right;" align="center">
						<a href="#" onclick="window.parent.Shadowbox.close(); return false;">Close Window</a> 
					</div>
					
				<? } ?>
				
			
			</div>	
		
		<? } ?>
		
	
	</body>
</html>