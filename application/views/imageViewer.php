<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html>
	<head>
	
		<script type="text/javascript" src="<?=base_url();?>resources/js/jquery-1.3.2.min.js"></script>
	
		<script type="text/javascript" src="<?=base_url();?>resources/js/opm_scripts.js"></script>


	    <!--<script type="text/javascript" src="<?=base_url();?>resources/js/mootools-release-1.11.js"></script>
		<script type="text/javascript" src="<?=base_url();?>resources/js/shadowbox-mootools.js"></script>
		<script type="text/javascript" src="<?=base_url();?>resources/js/shadowbox.js"></script>-->
		
		
		<script language="javascript">
		
			window.onload = function(){
				
				$('#imageDimDiv').load('<?= base_url(); ?>imageclass/imageViewerDims/<?= $imageid ?>/500');
			
			};
		
			currentSize = 500; // the width of the image we are currently viewing.
		
			function switchImage(imageid) {
			
				//opm.swapDetailImg('<?=base_url();?>imageclass/viewThumbnail/'+imageid+'/500');
				
				$('#imageDimDiv').load('<?= base_url(); ?>imageclass/imageViewerDims/'+imageid+'/500');
				
				$('#detailImage').fadeOut(function(){
					
					$('#detailImage').attr("src", '<?=base_url();?>imageclass/viewThumbnail/'+imageid+'/500');//.fadeIn();
  			
  				});
				
				
			}

		
		</script>
	
			
	    <link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/shadowbox_new.css">
	
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/shadowbox.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/datepicker.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/autocompleteStyles.css">
	
	    
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
	
	</head>
	
	<body bgcolor="#000000">

		<div id="imageDimDiv" align="right" style="color:white;"></div>
		
		

		<div id="detailImageDiv" align='center' style='padding-top:2px;'>
			
			<div id="detailImgContainerDiv" style="min-height:500px;">
			
				<img src='<?= base_url() ?>imageclass/viewThumbnail/<?= $imageid ?>/<?= $width ?>' id="detailImage" style="display:none;" onload="$(this).fadeIn('slow');">
        	
        	</div>
			
			<br>

			<div style="background:#333333; padding:3px 0px 3px 0px; margin:3px 3px 3px 3px">
       
				<? foreach ($otherImages->result() as $i) { ?>
			       
					<a href="javascript:switchImage(<?=$i->imageid?>)"><img src="<?=base_url();?>/imageclass/viewThumbnail/<?=$i->imageid?>/60" border="0" class="prodImagesImg"  /></a>&nbsp;&nbsp;
				
				<? } ?>
       
			</div>
		
		</div>
	
	
	
	</body>

</html>