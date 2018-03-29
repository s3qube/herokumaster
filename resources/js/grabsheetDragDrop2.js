
var dropbox = []; // keep track of products added to the grabsheet

var mySortables;

var tween;

var cancelTooltip = false;

var gsItems = new Array();

var curPage;
var numPages;


// window.addEvent('domready', function() {

//window.addEvent('domready', makeSort() );

//alert("boo");

function gsMoveAll() {
	
	if ($('dropInstruction'))
    	$('dropInstruction').destroy();

	mySortables.removeItems(gsItems);

	gsItems.each(function(item) {
	//Globosoft					  
		if(item!=undefined) {
			item.inject($('sort2'));
		}
	///		
	});
	
	mySortables.addItems(gsItems);

}

function deleteGsItem(id) {
	
	//alert("about to delete id:" + id);
	delete gsItems[id];

}

function clearGsItems() {

	//alert(gsItems.length);

	gsItems.each(function(item) {
		
		deleteGsItem(item.id);
			
	});
	
	//alert(gsItems.length);

}



window.addEvent('domready', function() {

	tween = $('gsTooltipDiv').get('tween', {property: 'opacity'});

	mySortables = new Sortables('#sort,#sort2,#trash', {
    	clone: function(event,element,list){
			var scroll = {x:0 ,y: 0};
			element.getParents().each(function(el){
				if(['auto','scroll'].contains(el.getStyle('overflow'))){
					scroll = {
						x: scroll.x + el.getScroll().x,
						y: scroll.y + el.getScroll().y
					}					
				}
			});
			var position = element.getPosition();
			
			return element.clone().setStyles({
				margin: '0px',
				position: 'absolute',
				visibility: 'hidden',
				'width': element.getStyle('width'),
				top: position.y + scroll.y,
				left: position.x + scroll.x
			}).inject(this.list);
		}, revert: { duration: 500,  transition: 'elastic:out' },
    	
    	onComplete: function(el) {
    	
    		//alert("you just dragged something" + el);
    		
    		parentID = el.getParent().id;
    		
    		if(parentID == 'sort2') { // we dragged into the grabsheet list
    		
    			// figure out if "drag items here" element is in list. if so, remove.
    			
    			if ($('dropInstruction'))
    				$('dropInstruction').destroy();
    				
    			if(el.getElement('.gsRedX')!=undefined)	
    				el.getElement('.gsRedX').setProperty('class','gsRedXOn');
    		
    		
    		} else if (parentID == 'trash' && (el.id != 'trashImage')) { // it was dropped in the trash!
    		
    			el.destroy();
    			tempID = el.id.substring(7,40);
    			delete gsItems[tempID];
    		
    		} else if (el.id == 'trashImage') { // we clicked on the trash - trash ALL!! 
    			    			
    			arrPickedItems = $('sort2').getChildren();
				
	
				arrPickedItems.each(function(el) {
					
					tempID = el.id.substring(7,40);
    				//alert(tempID);
    				deleteGsItem(tempID);
					
				
				});
				
				mySortables.removeItems(arrPickedItems).destroy();
	
    		
    		}
    		
    		
    		
    	
    	}
	});
	

});


function addItem() {
	
	var item1 = new Element('li', {'class': 'gsItem','alt':'29','id':'x_234'}).set('html','Five').inject($('sort'));
	var item2 = new Element('li').set('html','Doo').inject($('sort'));	
	var item3 = new Element('li').set('html','BingBong').inject($('sort2'));				
	
	mySortables.addItems([item1, item2, item3]);

	item1.setStyle('background-image', "url('/imageclass/viewThumbnail/11')");


}



function saveSheet() {

	var sort_order = '';
    $('sort2').getChildren().each(function(li) { sort_order = sort_order +  li.get('alt')  + '|'; });
	$('strGS').value = sort_order;
	
	//alert(sort_order);

	return sort_order;


}

function injectItem(element) {

	var item1 = new Element('li').set('html',"joe");
	item1.inject($('sort'));
	mySortables.addItems([item1]);

}

	var updateGSItems = function(items) {
	
		// update pagination
	
		items.each(function(item) {
							
		//Globosoft					
		if(item!=undefined) {
			var itemName = item.property + "&nbsp;&nbsp;//&nbsp;&nbsp;" + item.product;
			
			if (item.approvalStatus == 'Approved' || item.approvalStatus == 'Approved W/ Revisions')
				itemName = itemName + "&nbsp;&nbsp;<span style='color:green;font-weight:bold;font-size:12px;'>&#10004;</span>";

			itemName = itemName + "&nbsp;&nbsp;<a href='#' onclick=\"setGSCommentID("+item.imageid+"); return false;\" class=\"blueLink\">&#9998;</a>";

			itemName = itemName + "<span class='gsRedX' onclick=\"this.getParent().destroy(); deleteGsItem("+item.imageid+")\">&nbsp;&nbsp;X</font>";
		
		
			gsItems[item.imageid] = new Element('li', {'onmousedown':'killGsTooltip();','onmouseover':'showGsTooltip(this,'+item.imageid+');','onmouseout':'hideGsTooltip();','class': 'gsItem','alt':item.imageid,'id':'gsItem_'+item.imageid,'background-image':'/imageclass/viewThumbnail/'+item.imageid}).set('html', itemName).inject($('sort'));
			$('gsItem_'+item.imageid).setStyle('background-image',"url('"+base_url+"imageclass/viewThumbnail/"+item.imageid+"/30')");
			
			
				//$('gsItem_'+item.imageid).addClass("gsItemApproved");
				
			if (item.approvalStatus == 'Rejected')
				$('gsItem_'+item.imageid).addClass("gsItemRejected");

			
			
			
			mySortables.addItems([gsItems[item.imageid]]);
		}
		///////////////////////////////////////////////////////////////
		});
		
		TB_init();
		
	};
	

	
function setGSCommentID(imageid) {

	//alert(imageid);
	
	TB_show('Edit Comment','#TB_inline?height=150&width=300&inlineId=gsCommentWin',false);

	$('gsCommentID').setProperty('value',imageid);
	
	
	
	try {
		
		// set the comment box of the popup to the text in the hidden input, if the hidd exists!
		
		strTemp = $('gsItemComment_'+imageid).getProperty('value');
		//alert(strTemp);
		
		$('TB_ajaxContent').childNodes[1].elements[0].value = strTemp;
		
	
	} catch (e) { // 
		
		//alert("tag not found, creating...");
		//var myEL = new Element('input', {'type': 'hidden','id':'gsItemComment_'+imageid,'name':'gsItemComment_'+imageid}).injectAfter($('grabsheetForm'));
		
		$("grabsheetForm").adopt(new Element("input", {
			"type": "hidden",
			'id':'gsItemComment_'+imageid,
			"name": 'gsItemComment_'+imageid
		})); 
		
		//varformObj = ;
	//	alert("this ran");
		//myEL.injectAfter($('gsForm'));

	} 


}

function gsCommentSaveClose(submitButtonObj) {
	
	
	commentID = $('gsCommentID').getProperty('value');
	
	// save comment text to hidden tag
	
	strTemp = submitButtonObj.parentNode.elements[0].value; // dom traversing is nessa, as smoothbox duplicates the whole form (and it's IDs...) thanks smoothbox.

	//alert(strTemp);

	$('gsItemComment_'+commentID).setProperty('value',strTemp);
	//$('gsCommentID').setProperty('value',imageid);
	
	TB_remove();

}


function updateThumbs(pageNumber) {

	//clearGsItems();
	
	arrPickedItems = $('sort').getChildren();
				
	arrPickedItems.each(function(el) {
		
		tempID = el.id.substring(7,40);
		//alert(tempID);
		deleteGsItem(tempID);
		
	
	});
	
	productlineid = $('productLineSelect').getProperty('value');
	
	propertyid = $('propertyid').getProperty('value');

	searchText = encodeURIComponent($('searchText').getProperty('value'));
		
	approvalStatusID = $('approvalStatusID').getProperty('value');
	
	opmproductid = Number($('opmproductid').getProperty('value'));
	
	productcode = Number($('productcode').getProperty('value'));
	
	designerid = $('designerid').getProperty('value');
	
	categoryid = $('categoryid').getProperty('value');
	
	usergroupid = $('usergroupid').getProperty('value');
	
	if (searchText == '')
		searchText = 0;
	
	//if (productlineid != 0) {

	//	var items = $('items').empty().addClass('ajax-loading');
	
		$('sort').empty();
		 
		myUrl = base_url + 'grabsheets/getThumbsJSON/' + propertyid + '/' + productlineid + '/' + searchText + '/' + approvalStatusID + '/' + opmproductid + '/' + productcode + '/' + designerid + '/' + categoryid + '/' + usergroupid + '/' + pageNumber;
		
		//alert(url);
		//alert(myUrl);
		var request = new Request.JSON({
			url: myUrl,
			onComplete: function(jsonObj,currentProductLineID) {
				updateGSItems(jsonObj.items);
		        
		        curPage = parseInt(jsonObj.meta.pageNum);
		        numPages = parseInt(jsonObj.meta.numPages);
		        
		        displayPagInfo(jsonObj);
		        
				if (productlineid != currentProductLineID) {
					
					
					
				/*	
					//alert(dump(jsonObj.meta));
				// we have selected a new product line, reset all pagination vars.
					$('pagTotalProds').set('html', jsonObj.meta.totalResults);
					$('pagTotalPages').set('html', jsonObj.meta.numPages);
					$('pagTotalPages').set('title', jsonObj.meta.numPages);
					$('pagPageNum').set('html', jsonObj.meta.pageNum);
					$('pagPageNum').set('title', jsonObj.meta.pageNum);

	*/
					
				}
				
				
				
				if ($('grabPag').getStyle('visibility') == 'hidden')
					$('grabPag').setStyle('visibility','visible');
					
				if ($('moveAllBtn').getStyle('visibility') == 'hidden')
					$('moveAllBtn').setStyle('visibility','visible');
					
				
					
				// display json for testing
				var display = JSON.stringify(jsonObj, undefined, 2); // indentation level = 2	
				document.getElementById('jsonDisplay').innerHTML = display;
			}
		
		}).send();
		
	//}

}

function updatePage() {
	
	updateThumbs(document.getElementById("selPage").value);
}

function displayPagInfo(jsonObj) {
		
	strHtml = jsonObj.meta.totalResults + " Products &nbsp;&nbsp;&nbsp;";
	
	// display back
	
	if (curPage > 1) {
	
		strHtml += "<a onclick='updateThumbs(1)'>&lt;&lt;</a>&nbsp;&nbsp;<a onclick='updateThumbs(" + (curPage - 1) + ")'>&lt;</a>&nbsp;&nbsp;";
	
	}
	
	strHtml += "<select id='selPage' onchange='updatePage()' >";
	
	// display pages
	
	numPages = parseInt(jsonObj.meta.numPages);
	
	for (x=1; x<=numPages; x++) {
		
		/*if (x == curPage) {
			
			strHtml += " " + x + "";
			
		} else {
			
			strHtml += " <a onclick='updateThumbs(" + x + ")'>" + x + "</a>";
		
		}*/
		
		if (x == curPage) {
		
			selStr = "SELECTED";
		
		} else {
			
			selStr = "";
			
		}
		
		strHtml += "<option value='" + x +  "' " + selStr + ">Page " +  x + "</option>";
		
		
	}
	
	strHtml += "</select>";
	
	// display forward
	
	if (curPage < numPages) {
	
		//strHtml += " <a onclick='updateThumbs(" + (curPage + 1) + ")'>&gt;</a>";
	
		strHtml += "&nbsp;&nbsp;<a onclick='updateThumbs(" + (curPage + 1) + ")'>&gt;</a>&nbsp;&nbsp;<a onclick='updateThumbs(" + (numPages) + ")'>&gt;&gt;</a>";

	
	}
	
	//strHtml += " (curPage:" + curPage + ")";
	
	$('grabPag').set('html', strHtml);
	
	if ($('grabPag').getStyle('visibility') == 'hidden')
		$('grabPag').setStyle('visibility','visible');
	
}

function pagPageForward() {

	pageNum = parseInt($('pagPageNum').getProperty('title'));
	totalPages = parseInt($('pagTotalPages').getProperty('title'));
	
	if (pageNum < totalPages) {
	
		pageNum++;
		updateThumbs(pageNum);
	
	}

}

function pagPageBack() {

	pageNum = parseInt($('pagPageNum').getProperty('title'));
	totalPages = parseInt($('pagTotalPages').getProperty('title'));
	
	if (pageNum > 1) {
	
		pageNum--;
		updateThumbs(pageNum);
	
	}

}

function changeSelect() {

	if (document.getElementById('propertyid').value != 0) {
	
		timestamp = randomString();
	
		var myUrl = base_url + "grabsheets/getProductlines/" + document.getElementById('propertyid').value + "/" + timestamp;
	 
	 	var req = new Request({
		
			method: 'get',
			url: myUrl,
			
			onComplete: function(response) { $('sDiv').set('html', response); }
		
		}).send();

	 	//onRequest: function() { alert('Request made. Please wait...'); },
	
		/*new Ajax(url, {
			method: 'post',
			update: $('sDiv')
		}).request();*/
	
	}
}


function showGsTooltip(objLi,imageid) {

	windowDims = getWindowSize();
	
	var ttX = objLi.getPosition().x;
	var ttY = objLi.getPosition().y;
	
	// if we are too close to window bottom, show the tooltip on top!
	
	//alert(windowDims.height - ttY);
	
	$('gsTooltipDiv').setStyle('top',ttY+20);
	$('gsTooltipDiv').setStyle('left',ttX);
	
	// now lets set the image src
	
	$('gsTooltipDivImg').setProperty('src',base_url+'imageclass/view/'+imageid);
	
	$('gsTooltipDiv').setStyle('opacity',0);
	$('gsTooltipDiv').setStyle('display', 'block');
	
	//setup tween
	
	//fade it away
	tween.start(0).chain(function(){
		//get value from elsewhere and inject it in the dom
		//$('gsTooltipDiv').empty().grab(newcontent);
		//show again
		tween.start(1);
	});
	
	

}

function hideGsTooltip() {
	//alert("gi");
	//setup tween
	//var tween = $('gsTooltipDiv').get('tween', {property: 'opacity'});
	//fade it away
	tween.start(1).chain(function(){
		//get value from elsewhere and inject it in the dom
		//$('gsTooltipDiv').empty().grab(newcontent);
		//show again
		tween.start(0);
	});
	
}

function killGsTooltip() {
	
	tween.cancel();

	//$('gsTooltipDiv').setStyle('display', 'none');


}


function resetGrabsheet() {

	dropbox = [];
	document.getElementById("cart").innerHTML = "";

}

function checkGrabsheetForm() {

	document.grabsheet.itemids.value = saveSheet();
	
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

function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
	
// });