
<?

	$tabs = array(
	
		"basicinfo" => 1,
		"productlines" => 2,
		"assets" => 3,
		"billing" => 4
	
	);


?>

<script language="javascript">


	jQuery(document).ready(function() {
	
		//timestamp = randomString();
		
		//var url = '<?=base_url()?>properties/loadContent/<?=$p->propertyid?>/<?=$tabname?>';
		//curTab = '<?=$tabname ?>';
	 
		//$('#content').load(url);
		
		opm.changeContent(<?= $tabs[$tabname] ?>,'<?= $tabname ?>','properties');
	 
	})	
	
/*window.addEvent('domready', function() {
	
	var url = '<?=base_url()?>properties/loadContent/<?=$p->propertyid?>/<?=$tabname?>';
 
	new Ajax(url, {
		method: 'get',
		update: $('content'),
		
		onComplete: function(response) {
					
				//	if (window.datePickerController)
				//		datePickerController.create();
				
				}
				
	}).request();
	
	
});*/


</script>