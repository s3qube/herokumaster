<script language="javascript">

	
	jQuery(document).ready(function() {
	
		timestamp = randomString();
		
		var url = '<?=base_url()?>users/loadContent/<?=$user->userid?>/<?=$tabname?>/' + timestamp;
	 
		curTab = '<?=$tabname ?>';
	 
		$('#content').load(url);
	 
	})
	
/*window.addEvent('domready', function() {
	
 

	new Ajax(url, {
		method: 'get',
		update: $('content'),
		
		onComplete: function(response) {
					
					if (window.datePickerController)
						datePickerController.create();
				
				}
				
	}).request();
	
	
});*/


</script>