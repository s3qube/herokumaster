<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
	
		<script language = "javascript">
		
			<? if (isset($success)) { ?>
				
					window.parent.location.href = "<?=base_url();?>products/view/<?= $opm_productid ?>/";
					
			<? } ?>
			
		</script>

		
	</head>
	<body bgcolor="#ffffff">
	
		<? if (!isset($success)) { ?>
	
			<form name="pickSizesForm" method="post" action="<?= base_url();?>sizes/saveSizes">
		
				<input type="hidden" name="opm_productid" value="<?= $opm_productid ?>" />
				
				<? foreach ($sizes->result() as $s) { ?>
		
					<? //print_r($s); ?>
					
					<div class="sizeRow">			
				
						<input type="checkbox" name="sizes[<?= $s->id ?>]" <? if ($s->ischecked) echo "CHECKED";?> />
						<?= $s->size ?>
				
					</div>
		
					
				<? } ?>
					
					<br />
					
					<input type="submit" name="saveSizes" value="Save" />
					
			</form>
			
		<? } ?>
		
	</body>
</html>