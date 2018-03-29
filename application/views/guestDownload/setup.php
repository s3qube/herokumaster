<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		
		<script type="text/javascript" src="<?= base_url(); ?>resources/js/jquery-1.3.2.min.js"></script>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_upload.css">
		
		<script language="javascript">
		
			function sendData() {
	
			}
			
			$(document).ready(function(){

			});	
				
				
			function confirmFormData() {				
				
					
					if ($("#nameField").val() != '' && $("#emailField").val() != '') {
					
						sendFormData(); 
						
					
					} else {
						
						alert("Some information is missing");	
						
					}
					
					
					
			}
			
			
		
			function sendFormData() {
			
				email1 = $("#email1Field").val();
				name1 = $("#name1Field").val();
				
				email2 = $("#email2Field").val();
				name2 = $("#name2Field").val();
				
				email3 = $("#email3Field").val();
				name3 = $("#name3Field").val();
				
				fileType = $("#fileType").val();
				fileID = $("#fileID").val();
				
				opmProductID = $("#opmProductID").val();
				isUpload = $("#isUpload").val();
		
				$.post("<?= base_url(); ?>guestDownload/handle/", { email1: email1, name1: name1, email2: email2, name2: name2, email3: email3, name3: name3, fileType: fileType, fileID: fileID, opmProductID: opmProductID, isUpload: isUpload },
				
					function(data){
					
						if (data == 'Success') {
						
							$("#gdMessage").html("<span style='color:red'><b>Invite Sent!</b></span>");
							$("#btnSend").attr("disabled", "true"); 
						
						} else {
						
							alert("Errors were encountered when attempting to setup the download.");
						
						}
						
				
					});
			
			}

		
		</script>
		
		
	</head>
	<body>
		
		<div id="gdContent">
			<table border="0" width="300" height="" align="center">
				
					
					<input type="hidden" name="fileType" id="fileType" value="<?= $fileType ?>" />
					<input type="hidden" name="fileID" id="fileID" value="<?= $fileID ?>" />
					<input type="hidden" name="opmProductID" id="opmProductID" value="<?= $opmProductID ?>" />
				
					<input type="hidden" name="isUpload" id="isUpload" value="<?= (isset($isUpload) ? "1" : "0") ?>" />

				
					<tr>
						<td align="left" valign="middle" colspan="2">
							<h3 class="gdlLabel" style="margin-bottom:2px;">Recipient 1 Name:</h3>
							<input type="text" id="name1Field" name="name" class="gdlField" style="width:300px; margin-bottom:0px;" />
						</td>
					</tr>
					
					<tr>
						<td align="left" valign="middle" colspan="2">
							<h3 class="gdlLabel" style="margin-bottom:2px;">Recipient 1 Email:</h3>
							<input type="text" id="email1Field" name="email" class="gdlField" style="width:300px; margin-bottom:15px;" />
						</td>
					</tr>
					
					<tr>
						<td align="left" valign="middle" colspan="2">
							<h3 class="gdlLabel" style="margin-bottom:2px;">Recipient 2 Name:</h3>
							<input type="text" id="name2Field" name="name" class="gdlField" style="width:300px; margin-bottom:0px;" />
						</td>
					</tr>
					
					<tr>
						<td align="left" valign="middle" colspan="2">
							<h3 class="gdlLabel" style="margin-bottom:2px;">Recipient 2 Email:</h3>
							<input type="text" id="email2Field" name="email" class="gdlField" style="width:300px; margin-bottom:15px;" />
						</td>
					</tr>
					
					<tr>
						<td align="left" valign="middle" colspan="2">
							<h3 class="gdlLabel" style="margin-bottom:2px;">Recipient 3 Name:</h3>
							<input type="text" id="name3Field" name="name" class="gdlField" style="width:300px; margin-bottom:0px;" />
						</td>
					</tr>
					
					<tr>
						<td align="left" valign="middle" colspan="2">
							<h3 class="gdlLabel" style="margin-bottom:2px;">Recipient 3 Email:</h3>
							<input type="text" id="email3Field" name="email" class="gdlField" style="width:300px; margin-bottom:15px;" />
						</td>
					</tr>
					
					<tr>
						<td><div id="gdMessage"></div></td>
						<td align="right" valign="middle">
							<input type="submit" value="Send Invite" id="btnSend" onclick="confirmFormData(); return false;"/>
						</td>
					</tr>
				
			</table>
		</div>
		
				
		
		</div>

	</body>
</html>