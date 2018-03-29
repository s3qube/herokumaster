

<html>
	<head>
		<title>Upload Test</title>
		
		<style type="text/css">@import url(jquery.plupload.queue.css);</style>
		
		<script type="text/javascript" src="../js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="plupload.js"></script>
		<script type="text/javascript" src="jquery.plupload.queue.js"></script>
		<script type="text/javascript" src="plupload.html5.js"></script>
		
		<script type="text/javascript">
		
			// Convert divs to queue widgets when the DOM is ready
			
			$(function() {
				
				// Setup html5 version
				$("#html5_uploader").pluploadQueue({
				
					// General settings
					runtimes : 'html5',
					url : 'upload.php',
					max_file_size : '250mb',
					chunk_size : '1mb',
					unique_names : true
			
					// Resize images on clientside if we can
					/*resize : {width : 320, height : 240, quality : 90},
			
					// Specify what files to browse for
					filters : [
					
						{title : "Image files", extensions : "jpg,gif,png"},
						{title : "Zip files", extensions : "zip"}
					
					]*/
			
				});
			
			
			});
		
		</script>

		
		
	</head>

	<body>
	
		<h4>HTML 5 runtime</h4>
		<div id="html5_uploader">You browser doesn't support native upload. Try Firefox 3 or Safari 4.</div>

	</body>



</html>

