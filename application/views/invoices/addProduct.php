<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
	
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery-1.3.2.min.js"></script>
			
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery.autocomplete.js"></script>

	
		<script language = "javascript">
		
			<? if (isset($result)) { ?>
				
					window.parent.reloadNotes();
					var timerID = setTimeout("window.parent.Shadowbox.close();", 600);
				
			<? } ?>
		
			/*var options, a;
	
			jQuery(function(){
		  	
		  		options = { serviceUrl:'<?= base_url(); ?>ajax/invoiceQuery',minChars:2,deferRequestBy: 300 };
		  		a = $('#query').autocomplete(options);
			
			});*/
			
			function getProducts(propertyid) {
				
				$('#productSelect').load('<?= base_url(); ?>invoices/ajaxGetProducts/' + propertyid);
		
			}
			
			function getImage(opm_productid) {
				
				$('#imageArea').load('<?= base_url(); ?>invoices/ajaxGetImage/' + opm_productid);
				$("#selBtn").fadeIn("slow");
			
			}
			
			function checkForm() {
			
				document.prodForm.opm_productid.value = $("#opm_prodid").val();
				return true;
			
			}
			
		</script>
		
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_invoicepopup_styles.css">
	</head>
	<body>
		
		<? if (!isset($result)) { ?>
		
		
			<div id="container">
				<form name="prodForm" method="POST" action="<?= base_url(); ?>invoices/addEditCharge/<?= $invoiceid ?>" onsubmit="return checkForm();">
				
					<input type="hidden" name="invoiceid" value="<?= $invoiceid ?>" />
					<input type="hidden" name="opm_productid" value="" />
					
					
					<h3 class="invoicePopLabel">Select Property:</h3>
					
					<select id="opm_prodid" name="opm_prodid" class="invoicePopField" onchange="getProducts(this.value);">
							
						<option value="0">Please Select...</option>		
							
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
					<div id="productSelect"></div>
					
					<div id="imageArea"></div>
					
					<div class="invoicePopBtn" id="selBtn" style="display:none;"><input type="submit" class="invoicePopBtn" value="Select This Product" /></div>
				
				</form>
			</div>
			
			
		<? } else { ?>
		
			
			<br /><br /><br />
				
			<div style="text-align:center;"><?= $resultText ?></div>
				
		
		<? } ?>
		
	
	</body>
</html>