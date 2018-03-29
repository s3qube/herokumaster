

<div id="uploadDiv" style="width:640px;margin-left:auto;margin-right:auto;">
		

			
	* The up arrow starts the upload.
	
	
	<applet name="jumpLoaderApplet"
			code="jmaster.jumploader.app.JumpLoaderApplet.class"
			archive="<?=base_url();?>resources/jumploader_z.jar"
			width="640"
			height="480" 
			mayscript>
		<param name="uc_uploadUrl" value="<?=base_url();?>files/guestUpload/<?=$randomString?>">
		<param name="uc_fileParameterName" value="Filedata"/>
		<? /* <param name="uc_partitionLength" value="1048576"/> */ ?>
		<param name="vc_mainViewFileListViewVisible" value="false"/>
		<param name="vc_mainViewFileTreeViewVisible" value="false"/>
		
	</applet>		

</div>