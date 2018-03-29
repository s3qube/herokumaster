<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
	
		<script language = "javascript">
		
			<? if (isset($success) && $success == true) { ?>
				
					window.parent.location.href = "<?=base_url();?>products/view/<?= $opm_productid ?>/";
					
			<? } ?>
			
		</script>

		
	</head>
	<body bgcolor="#ffffff">
	
		<? if (!isset($success)) { ?>
	
			<div style="margin-left:10px;">
	
				<form name="addLinkForm" method="post" action="<?= base_url();?>products/saveLinkedProduct">
			
					<input type="hidden" name="opm_productid" value="<?= $opm_productid ?>" />
						
					Enter OPM Product ID to link:<br /><br />
					<input type="text" name="opmIDToLink" />
					
						
						<input type="submit" name="saveSizes" value="Add" />
						
				</form>
			
			</div>
			
		<? } else { ?>
		
		
			<div style="margin-left:10px;">
		
				<? if (isset($error)) { ?>
				
					<?= $error ?>
				
				<? } ?>
			
			</div>
			
		
		<? } ?>
		
	</body>
</html>