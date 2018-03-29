<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		
		<? if (isset($result)) { ?>
		
			<script language = "javascript">
			
				window.parent.reloadNotes();
				var timerID = setTimeout("window.parent.Shadowbox.close();", 600);
			
			</script>		
		
		<? } ?>
		
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_invoicepopup_styles.css">
	</head>
	<body>
		
		<? if (!isset($result)) { ?>
		
		
			<div id="container">
				<form name="noteForm" method="POST" action="<?= base_url(); ?>invoices/createSaveNote/<?= $invoiceid ?>">
				
					<input type="hidden" name="invoiceid" value="<?= $invoiceid ?>" />
					<h3 class="invoicePopLabel">Note Text:</h3>
				
					<textarea name="note" class="invoicePopField"></textarea>
					
					<br />
					
					<div class="invoicePopBtn"><input type="submit" class="invoicePopBtn" value="Save" /></div>
				
				</form>
			</div>
			
			
		<? } else { ?>
		
			
			<br /><br /><br />
				
			<div style="text-align:center;"><?= $resultText ?></div>
				
		
		<? } ?>
		
	
	</body>
</html>