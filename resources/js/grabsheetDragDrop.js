
var dropbox = []; // keep track of products added to the grabsheet

// window.addEvent('domready', function() {

window.addEvent('domready', loadThumbs() );

function loadThumbs() {

	//var theTips = new TipsX3 ($$('.tipper'), {showDelay: 0})
	
	if (window.TipsX3)
	opm.initializeTooltips();

	var drop = $('cart');
	var dropFx = drop.effect('background-color', {wait: false}); // wait is needed so that to toggle the effect,
	 
	$$('.item').each(function(item){
	 
		item.addEvent('mousedown', function(e) {
			
			//alert("thisran");
			
			e = new Event(e).stop();
	 
			var clone = this.clone()
				.setStyles(this.getCoordinates()) // this returns an object with left/top/bottom/right, so its perfect
				.setStyles({'opacity': 0.7, 'position': 'absolute'})
				.addEvent('emptydrop', function() {
					this.remove();
					drop.removeEvents();
				}).inject(document.body);
	 
			drop.addEvents({
				'drop': function() {
				
					var id = item.getProperty('id');
					
					if(dropbox.contains(id)) { // check for dupes
					
						drop.removeEvents();
						clone.remove();
						dropFx.start('7389AE').chain(dropFx.start.pass('ffffff', dropFx));
						// alert("Sorry, this is a duplicate item!");
					
					} else {
					
						drop.removeEvents();
						clone.remove();
						item.clone().inject(drop);
						dropFx.start('7389AE').chain(dropFx.start.pass('ffffff', dropFx));
						dropbox.push(id); // add product to the list
						
						document.getElementById("itemids").value = dropbox.toString();
						//alert(document.getElementById("itemids").value);
					
					}
					
				},
				'over': function() {
					dropFx.start('98B5C1');
				},
				'leave': function() {
					dropFx.start('ffffff');
				}
			});
	 
			var drag = clone.makeDraggable({
				droppables: [drop]
			}); // this returns the dragged element
	 
			drag.start(e); // start the event manual
		});
	 
	});
	
}

function updateThumbs() {
	
	productlineid = $('productLineSelect').getProperty('value');
	
	if (productlineid != 0) {

		var items = $('items').empty().addClass('ajax-loading');
		 
		url = base_url + 'grabsheets/getThumbs/' + productlineid;
		
		//alert(url);
		
		new Ajax(url, {
			method: 'get',
			update: $('items'),
			onComplete: function() {
				items.removeClass('ajax-loading');
				loadThumbs();
			}
		}).request();
		
	}

}

function changeSelect() {

	if (document.getElementById('propertyid').value != 0) {
	
		timestamp = randomString();
	
		var url = base_url + "grabsheets/getProductlines/" + document.getElementById('propertyid').value + "/" + timestamp;
	 
		new Ajax(url, {
			method: 'post',
			update: $('sDiv')
		}).request();
	
	}
}

function resetGrabsheet() {

	dropbox = [];
	document.getElementById("cart").innerHTML = "";

}

function checkGrabsheetForm() {
	
	grabsheetgroupid = document.grabsheet.grabsheetgroupid.value;
	title = document.grabsheet.title.value;
	grabsheettemplateid = document.grabsheet.grabsheettemplateid.value;
	itemids = document.grabsheet.itemids.value;
	
	if (itemids == '') {
	
		alert("There are no items on the grabsheet!");
		return false;
		
	} else if (title == '') {
	
		alert("Please enter a title!");
		return false;
	
	} else if (grabsheettemplateid == 0) {
	
		alert("Please select a template!");
		return false;
	
	} else if (grabsheetgroupid == 0) {
	
		alert("Please select a grabsheet group!");
		return false;
	
	} else {
	
		return true;
	
	}


}
	
// });