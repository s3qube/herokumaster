<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<script language="javascript">
			
			var base_url = '<?=base_url();?>';
			
			<? if (isset($product)) { ?>
			var id = '<?= $product->opm_productid ?>';
			<? } ?>
			
			<? if (isset($user)) { ?>
			var id = '<?= $user->userid ?>';
			<? } ?>
			
			<? if (isset($p)) { ?>
			var id = '<?= $p->propertyid ?>';
			<? } ?>
			
		</script>
			
		<? if (isset($javascripts)) echo $javascripts; ?>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/shadowbox_new.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/shadowbox.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/datepicker.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/autocompleteStyles.css">
		
		<!--<style type="text/css">
		
			BODY {
						
				background-image: url('<?=base_url();?>resources/images/nav_bg.gif');
				background-repeat: repeat-x;
				background-position: 0 83px
			
			}
		
		</style>-->
		
		<? if (isset($headInclude)) echo $headInclude; ?>
		
	</head>
	<body onload="MM_preloadImages('<?=base_url();?>resources/images/nav_products_over.gif','<?=base_url();?>resources/images/nav_properties_over.gif','<?=base_url();?>resources/images/nav_production_over.gif','<?=base_url();?>resources/images/nav_administration_over.gif');">
		
		<div id="container">

			<div id="maincontent">
				
				<? if (isset($contentNav)) echo $contentNav; ?>
				
				<br /><br />
				
				<div id="content"><? if (isset($content)) echo $content; ?></div>
				
				<? if (isset($contentNav2)) echo "<br><br>".$contentNav2; ?>
				
			</div>
			
		</div>
		
	
		<div style="page-break-after: always;"></div>

		
	</body>
</html>