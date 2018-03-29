<!DOCTYPE html>

<html>
	<head>
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_upload.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/jquery.plupload.queue.css">

		<!--[if lt IE 9]>
 			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
				
		<script type="text/javascript" src="<?=base_url();?>resources/js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="<?=base_url();?>resources/js/plupload.js"></script>
		<script type="text/javascript" src="<?=base_url();?>resources/js/jquery.plupload.queue.js"></script>
		<script type="text/javascript" src="<?=base_url();?>resources/js/plupload.html5.js"></script>

		<script language="javascript">
		
			// detect html5 file reader object and redirect if not present
			
			if (typeof(FileReader) == "undefined") {
   				
   				var url = ( document.URL );
   				var newUrl = url.replace("showUpload","showJavaUpload");
   				
   			//	alert(newUrl);
   				
   				window.location = newUrl;
   			
   			}
   			
			// Convert divs to queue widgets when the DOM is ready
			
			$(function() {
				
				// Setup html5 version
				uploader = $("#html5_uploader").pluploadQueue({
				
					// General settings
					runtimes : 'html5',
					url : '<?=base_url();?>files/upload/<?=$opm_productid?>/<?=$fileType?>',
					max_file_size : '2000mb',
					
					unique_names : true
			
					/*chunk_size : '1mb',*/
			
					// Resize images on clientside if we can
					/*resize : {width : 320, height : 240, quality : 90},
			
					// Specify what files to browse for
					filters : [
					
						{title : "Image files", extensions : "jpg,gif,png"},
						{title : "Zip files", extensions : "zip"}
					
					]*/
			
				});
								
				var uploader = $('#html5_uploader').pluploadQueue(); 
				uploader.bind('FileUploaded', function(up, file, res) {
			   
			        //alert("FILE UPLOODED");
			   
			    });
			
			
			});
		
		</script>
		
		
	</head>
	
	<body>
		
		<div id="html5_uploader">
			
			<form enctype="multipart/form-data" method="post" action="<?=base_url();?>files/upload/<?=$opm_productid?>/<?=$fileType?>">
				<input type="file" name="file" />
				<input type="submit" value="submitt" />
			</form>
		
		</div>
		
	</body>

</html>