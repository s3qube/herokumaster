<script language="javascript">




	jQuery(document).ready(function() {
		
		//alert("eh");
	
		var myTimestamp = randomString();
		
		var url = '<?=base_url()?>products/loadContent/<?=$product->opm_productid?>/<?=$tabname?>/' + myTimestamp;
	 
		curTab = '<?=$tabname ?>';
	 
		$( "#content" ).load( url, function() {
		
			/*if ($.isFunction(createImgUploader)) {
			
				createImgUploader();
			
			}*/
			
			if($('#sampleNotesArea').length){
			
				buildSampleDatePickers();
			
			}
			
			
		
		});
	 
	})	
	
	
function buildSampleDatePickers() {
	
	//alert("yaya");
	
	$( "#sampleNotesSubmit" ).click(function( event ) {
			
				var strNotes = $("#sampleNotes").val();
							
				$.post(base_url + "products/saveSampleNotes" , { opm_productid: id, notes: strNotes }, function( data ) {
					  
					//alert(data);
					$("#sampleNotesDiv").html(data);
					$("#sampleNotesArea").fadeOut("fast");
					
				
				});
				
				event.preventDefault();
			
			});
			
			
			$('#samplesentdisplay').DatePicker({
				
				format:'m/d/Y',
				date: $('#samplesentdate').val(),
				current: $('#samplesentdate').val(),
				starts: 1,
				position: 'r',
				
				onBeforeShow: function(){
					//alert("yo B");
					//$('#inputDate').DatePickerSetDate($('#samplesentdate').val(), true);
				},
				
				onChange: function(formated, dates) {
				
					//alert("yeah");
				
					$.post(base_url + "products/saveSampleDates" , { opm_productid: id, samplesentdate: formated }, function( data ) {
					  
					  if (data != 'ERROR') {
						 // alert(data);
						 // $('#samplesentdisplay').val(data);
						  $('#samplesentdisplay').text(data);
						  $('#samplesentdisplay').DatePickerHide();
						  
					  } else {
						  
						  alert("Sample Sent Date could not be saved.");
						  $('#samplesentdisplay').DatePickerHide();
						  
					  }
					  //alert(data);
					
					});
					
					
						
				}
			
			});
			
			$('#samplerecdisplay').DatePicker({
				
				format:'m/d/Y',
				date: $('#samplerecdate').val(),
				current: $('#samplerecdate').val(),
				starts: 1,
				position: 'r',
				
				onBeforeShow: function(){
					//alert("yo B");
					//$('#inputDate').DatePickerSetDate($('#samplesentdate').val(), true);
				},
				
				onChange: function(formated, dates){
				
					//alert("yeah");
				
					$.post(base_url + "products/saveSampleDates" , { opm_productid: id, samplerecdate: formated }, function( data ) {
					  
					  if (data != 'ERROR') {
						  //alert(data);
						 // $('#samplesentdisplay').val(data);
						  $('#samplerecdisplay').text(data);
						  $('#samplerecdisplay').DatePickerHide();
						  
					  } else {
						  
						  alert("Sample Sent Date could not be saved.");
						  $('#samplerecdisplay').DatePickerHide();
						  
					  }
					  //alert(data);
					
					});
					
					
						
				}
			
			});
	
	
}

/*window.addEvent('domready', function() {
	
	timestamp = randomString();
	
	var url = '<?=base_url()?>products/loadContent/<?=$product->opm_productid?>/<?=$tabname?>/' + timestamp;
 
 	curTab = '<?=$tabname ?>';

	new Ajax(url, {
		method: 'get',
		update: $('content')
	}).request();
	
	
});*/


</script>