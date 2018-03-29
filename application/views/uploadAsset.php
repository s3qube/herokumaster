
	
	
	
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
				url : '<?=base_url();?>files/assetUpload/<?=$p->propertyid?>/asset/<?= $random_str ?>',
				max_file_size : '250mb',
				
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
		

		
	<div id="html5_uploader">
		
		<form enctype="multipart/form-data" method="post" action="<?=base_url();?>files/assetUpload/<?=$p->propertyid?>/asset/<?= $random_str ?>">
			<input type="file" name="file" />
			<input type="submit" value="submitt" />
		</form>
	
	</div>
