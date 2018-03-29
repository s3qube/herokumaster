<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_upload.css">
		
	</head>
	<body>
		
		<div id="uploadDiv" style="width:550px;margin-left:auto;margin-right:auto;">
		
			
			* The up arrow starts the upload!.
			
					
			<applet name="jumpLoaderApplet"
					code="jmaster.jumploader.app.JumpLoaderApplet.class"
					archive="<?=base_url();?>resources/jumploader_z.jar"
					width="550"
					height="320" 
					mayscript>
				<param name="uc_uploadUrl" value="<?=base_url();?>files/upload/<?=$opm_productid?>/<?=$fileType?>">
				<param name="uc_fileParameterName" value="file"/>
				<? /* <param name="uc_partitionLength" value="1048576"/> */ ?>
				<param name="vc_mainViewFileListViewVisible" value="false"/>
				<param name="vc_mainViewFileTreeViewVisible" value="false"/>
				
			</applet>
		
		</div>

	</body>
</html>