

var opm = {

		changeOptionColor: function(fieldname,id){
			
			//alert($(fieldname+'_'+id).checked);
			
			if ($(fieldname+'_'+id).checked == true) {
			
				
				//alert("you checked it");
				
				new Fx.Style($('div_'+fieldname+'_'+id), "background-color", {duration:350}).start('#aeb2dc');
				new Fx.Style($('div_'+fieldname+'_'+id), "color", {duration:350}).start('#121212');
			
			
			} else {
		
				new Fx.Style($('div_'+fieldname+'_'+id), "background-color", {duration:350}).start('#ffffff');
				new Fx.Style($('div_'+fieldname+'_'+id), "color", {duration:350}).start('#363636');
			
			}
				
		
		},

		changeOptionColorJQ: function(fieldname,id) {
						
			
			if ( $("#"+fieldname+'_'+id+':checked').val()) {
								
				
				$("#div_"+fieldname+'_'+id).animate({backgroundColor: '#aeb2dc'});
				$("#div_"+fieldname+'_'+id).animate({color: '#121212'});

				//$('#div_'+fieldname+'_'+id).stop().animate({backgroundColor:'#aeb2dc'}, 350);
				
				//new Fx.Style($('div_'+fieldname+'_'+id), "background-color", {duration:350}).start('#aeb2dc');
				//new Fx.Style($('div_'+fieldname+'_'+id), "color", {duration:350}).start('#121212');
			
			
			} else {
			
				$("#div_"+fieldname+'_'+id).animate({backgroundColor: '#ffffff'});
				$("#div_"+fieldname+'_'+id).animate({color: '#363636'});
			
				//new Fx.Style($('div_'+fieldname+'_'+id), "background-color", {duration:350}).start('#ffffff');
				//new Fx.Style($('div_'+fieldname+'_'+id), "color", {duration:350}).start('#363636');
			
			}
		
		},

		showHideDiv: function(target) {	
			
			$('#'+target).toggle('slow', function() {
   			
   				 // Animation complete.
 			 
 			 });
		
		},

		showDiv: function(target) {
		
						
			$('#'+target).fadeIn();
			
		
		},
		
		hideDiv: function(target) {
		
			
			
			$('#'+target).fadeOut;
			
		
		},
		
		swapDetailImg: function(strSrc){
		

			
			 $('#detailImage').fadeOut('fast', function() {
   			
   				$('#detailImage').attr("src",strSrc).load(function(){
               		$('#detailImage').fadeIn('fast');
       			});
 			
 			 });
			
		
		},
		
		initializeTooltips: function() {
				
			var theTips = new TipsX3($$('.tipper'), {
				
				initialize:function(){
					this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
				},
				onShow: function(toolTip) {
					this.fx.start(1);
				},
				onHide: function(toolTip) {
					this.fx.start(0);
				}
			});
		
		
		},
		
		
		
		changeContent: function(numTab,strContent,strSection) {
			
			
			
			
			if (numTab) {
				
				// set all on tds to off.
				
				$("td.tabNavOn").addClass("tabNavOff");
				$("td.tabNavOn").removeClass("tabNavOn");
				
				// set all on links to off.
				
				$("a.tabNavOn").addClass("tabNavOff");
				$("a.tabNavOn").removeClass("tabNavOn");
				
				// turn on appropriate link and td.
				
				$("#contentTab"+numTab).removeClass("tabNavOff");
				$("#contentTab"+numTab).addClass("tabNavOn");
				
				$("#contentTabLink"+numTab).removeClass("tabNavOff");
				$("#contentTabLink"+numTab).addClass("tabNavOn");

			
			}
			
			curTab = strContent;
			
			// passing a random string, cause otherwise IE caches the ajax
			timestamp = randomString();
			
			// now load the content
			
			var url = base_url + strSection + '/loadContent/' + id + '/' + strContent + '/' + timestamp;	
			
			$( "#content" ).load( url, function() {
			
				
				// For Sample Date Pickers
				
				if ($('#sampleNotesArea').length){
			
					buildSampleDatePickers();
				
				}
				
				
				
				// FOR PRODUCT IMAGE UPLOADER
			
				/*if ($("#imgUploader").length > 0) {
				
					createImgUploader();
					
				}	*/
				
				
				if ($.isFunction($(".chzn-select").chosen)) {
				
					$(".chzn-select").chosen(function() {
	 			
	 
	 				});
 				
 				}
				
			});		
		
		}
			
}



if (typeof jQuery != 'undefined') {

	// activate all helptips
	
	$(document).ready(function() {
		
		//opm.initializeTooltips();
		
		if (!!$.prototype.tooltip) { // test if tooltip method is avail
		
			//alert("we have tooltip!");
		
			$(".helpTip").tooltip({ 
				
				tip:'.searchTooltip',
				position: "center right", 
				effect: "fade",
				
				onBeforeShow: function() {
	  				
	  				obj = this.getTrigger();
					imageid = obj.attr("id").substring(7);
	  				  				
	  				src = base_url + "imageclass/viewThumbnail/" + imageid + "/290";
	  				$('#searchTooltipImg').hide().attr("src", src).fadeIn('fast');
	  				
	  			
	  			}
			}); 
			
		}
	
	});

	$(window).load(function() {
			
		if (typeof Shadowbox != 'undefined') {
	 	
			Shadowbox.init({
		
				skipSetup: true,
				onClose: reloadContent
			
			});
		
		}
		
		
		$("#productLock").click(function() {
		
		
			$.post(base_url + "products/changeLockStatus", { opm_productid: id })
			
				.done(function(data) {
				
					if (data == 'locked') {
						
						var src = $("#productLock").attr("src").replace("lockIconOpen", "lockIcon");
						$("#productLock").attr("src", src);
						
						
					} else if (data == 'unlocked') {
						
						var src = $("#productLock").attr("src").replace("lockIcon", "lockIconOpen");
						$("#productLock").attr("src", src);
						
					} else {
						
						alert("Could not change product lock status, please try again.");
						
					}
				
				}
			
			);
		
		});
	      
	});
	
	

}


function confirmDeleteVisual($imageid) {

	if (confirm("Are You Sure You Want To Delete This Visual?")) {
	
		window.location = base_url + "imageclass/delete/" + $imageid;
		//alert("About To Redirect!");
	
	} else {
	
		return false;
	
	}

}

function confirmDeleteMfSep(strMode,intID) {

	if (strMode == 'mf') {

		if (confirm("Are You Sure You Want To Delete This Master File?")) {
		
			window.location = base_url + "files/delete/mf/" + intID;
			//alert("About To Redirect!");
		
		} else {
		
			return false;
		
		}
	
	} else if (strMode == 'sep') {
	
		if (confirm("Are You Sure You Want To Delete This Separation?")) {
		
			window.location = base_url + "files/delete/sep/" + intID;
			//alert("About To Redirect!");
		
		} else {
		
			return false;
		
		}
	
	}

}

function reloadContent() {
	
	if (undefined != window.curTab) { // check if curTab is defined
	
		if (curTab == 'images')
			opm.changeContent(3,'images','products');
	
	}

}

function openUploader(opm_productid,fileType) {

	if (fileType == 'asset')
		contentUrl = base_url + 'upload/showAssetUpload/' + opm_productid; // in this case opm_product id is actually the propertyid
	else
		contentUrl = base_url + 'upload/showUpload/' + opm_productid + "/" + fileType;
	
	
	Shadowbox.open({
	
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     350,
		width:      600
	
	});
	

}



function openSizesWindow(opm_productid) {

	contentUrl = base_url + 'sizes/pickSizes/' + opm_productid;


	Shadowbox.open({
	
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     450,
		width:      290
	
	});
	

}

function openLinkProductWindow(opm_productid) {

	contentUrl = base_url + 'products/addLinkedProduct/' + opm_productid;

	Shadowbox.open({
	
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     110,
		width:      290
	
	});
	

}

function openGuestDownloadWindow(opmProductID,fileType) {


	contentUrl = base_url + 'guestDownload/setup/' + opmProductID + '/' + fileType;	
	
	Shadowbox.open({
	
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     200,
		width:      400
	
	});
	

}

function openGuestUploadWindow(opmProductID,fileType) {


	contentUrl = base_url + 'guestDownload/setup/' + opmProductID + '/' + fileType + '/' + '1'; // 1 indicates that this is an upload!
	

	Shadowbox.open({
	
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     200,
		width:      400
	
	});
	

}


function openEmailNotificationWindow(fileID,fileType) {


	contentUrl = base_url + 'email/sendNotificationDialog/' + fileID + '/' + fileType;


	Shadowbox.open({
	
		title:      '',
		type:       'iframe',
		content:    contentUrl,
		height:     500,
		width:      400
	
	});
	

}


function openUserPicker(opm_productid,type) {
	
	contentUrl = base_url + 'email/userPicker/' + opm_productid + "/" + type;

	Shadowbox.open({
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     400,
		width:      330
	});

}


function recipientsPicker(opm_productid,emailType) {

	contentUrl = base_url + 'recipients/getRecipients/' + opm_productid + "/" + email_type + "/";
	
	Shadowbox.open({
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     350,
		width:      600
	});
	

}
// 20111217 mark 
function openAccountsWindow(opm_productid, accountid) {

	var contentUrl = "/products/purchaseDialog/" + opm_productid + "/" + accountid;
	
	Shadowbox.open({
		
		title: "",
		player: "iframe",
		content: contentUrl,
		height: 390,
		width: 515
	});
}
// 20111217 mark


function shadowboxImage(imageid,width) {

	contentUrl = base_url + 'imageclass/imageViewer/' + imageid + "/" + width;
	
	Shadowbox.open({
	
		title:      '',
		player:       'iframe',
		content:    contentUrl

	});

	

}


function detailImageShadowbox(imageid) {

	srcUrl = $("#detailImage").attr('src');
	arrInfo = srcUrl.split("viewThumbnail/");
	arr2 = arrInfo[1].split("/");
	imageID = arr2[0];
	//alert(imageID);

	shadowboxImage(imageID,500);
	

}



function changeGroup(opm_productid,usergroupid,objChecked){
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	var url = base_url + 'ajax/changeGroup/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	// new jquery version.
	
	$.post(url, { opm_productid: opm_productid, usergroupid: usergroupid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invGroupRowTr_'+usergroupid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invGroupRowTd_'+usergroupid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invGroupRowImg_'+usergroupid).attr('src', base_url + '/resources/images/inv_groupicon.gif');

			} else if (data == 'deactivated') {
			
				$('#invGroupRowTr_'+usergroupid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invGroupRowTd_'+usergroupid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invGroupRowImg_'+usergroupid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});
	

}


function changeTerritory(opm_productid,territoryid,objChecked){
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	
	var url = base_url + 'ajax/changeTerritory/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	
	$.post(url, { opm_productid: opm_productid, territoryid: territoryid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invTerritoryRowTr_'+territoryid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invTerritoryRowTd_'+territoryid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invTerritoryRowImg_'+territoryid).attr('src', base_url + '/resources/images/inv_territoryicon.gif');

			} else if (data == 'deactivated') {
			
				$('#invTerritoryRowTr_'+territoryid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invTerritoryRowTd_'+territoryid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invTerritoryRowImg_'+territoryid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});

}

function changeRight(opm_productid,rightid,objChecked){
			
	var url = base_url + 'ajax/changeRight/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	
	
	$.post(url, { opm_productid: opm_productid, rightid: rightid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invRightRowTr_'+rightid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invRightRowTd_'+rightid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invRightRowImg_'+rightid).attr('src', base_url + '/resources/images/inv_righticon.gif');

			} else if (data == 'deactivated') {
			
				$('#invRightRowTr_'+rightid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invRightRowTd_'+rightid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invRightRowImg_'+rightid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});

}

function changePropertyTerritory(propertyid,territoryid,objChecked){
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	
	var url = base_url + 'ajax/changePropertyTerritory/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	
	$.post(url, { propertyid: propertyid, territoryid: territoryid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invTerritoryRowTr_'+territoryid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invTerritoryRowTd_'+territoryid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invTerritoryRowImg_'+territoryid).attr('src', base_url + '/resources/images/inv_territoryicon.gif');

			} else if (data == 'deactivated') {
			
				$('#invTerritoryRowTr_'+territoryid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invTerritoryRowTd_'+territoryid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invTerritoryRowImg_'+territoryid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});

}

function changeOfficeTerritory(officeid,territoryid,objChecked){
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	
	var url = base_url + 'ajax/changeOfficeTerritory/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	
	$.post(url, { officeid: officeid, territoryid: territoryid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invTerritoryRowTr_'+territoryid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invTerritoryRowTd_'+territoryid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invTerritoryRowImg_'+territoryid).attr('src', base_url + '/resources/images/inv_territoryicon.gif');

			} else if (data == 'deactivated') {
			
				$('#invTerritoryRowTr_'+territoryid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invTerritoryRowTd_'+territoryid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invTerritoryRowImg_'+territoryid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});

}

function changePropertyRight(propertyid,rightid,objChecked){
			
	var url = base_url + 'ajax/changePropertyRight/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
		
	$.post(url, { propertyid: propertyid, rightid: rightid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invRightRowTr_'+rightid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invRightRowTd_'+rightid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invRightRowImg_'+rightid).attr('src', base_url + '/resources/images/inv_righticon.gif');

			} else if (data == 'deactivated') {
			
				$('#invRightRowTr_'+rightid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invRightRowTd_'+rightid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invRightRowImg_'+rightid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});
	

}


function changePropertyChannel(propertyid,channelid,objChecked){
			
	var url = base_url + 'ajax/changePropertyChannel/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
		
	$.post(url, { propertyid: propertyid, channelid: channelid, onoff: onoff },
   	
   		function(data) {
    
     		if (data == 'activated') {
			
				$('#invChannelRowTr_'+channelid).removeClass("invGroupRow").addClass("invGroupRow_active");
				$('#invChannelRowTd_'+channelid).removeClass("invGroupText").addClass("invGroupText_active");
				$('#invChannelRowImg_'+channelid).attr('src', base_url + '/resources/images/inv_righticon.gif');

			} else if (data == 'deactivated') {
			
				$('#invChannelRowTr_'+channelid).removeClass("invGroupRow_active").addClass("invGroupRow");
				$('#invChannelRowTd_'+channelid).removeClass("invGroupText_active").addClass("invGroupText");
				$('#invChannelRowImg_'+channelid).attr('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
   	
   	});
	

}


function randomString() {

	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 10;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	
	return randomstring;
	
}



function getWindowSize()
{
	var w = 0;
	var h = 0;

	//IE
	if(!window.innerWidth)
	{
		//strict mode
		if(!(document.documentElement.clientWidth == 0))
		{
			w = document.documentElement.clientWidth;
			h = document.documentElement.clientHeight;
		}
		//quirks mode
		else
		{
			w = document.body.clientWidth;
			h = document.body.clientHeight;
		}
	}
	//w3c
	else
	{
		w = window.innerWidth;
		h = window.innerHeight;
	}
	return {width:w,height:h};
}

function confirmHideAllProducts() {

	if (confirm('This process cannot be undone! Be sure you want to do this!')) {
	
		document.userform.hideAllProducts.value = '1';
		document.userform.submit();
	
	} else {
	
		return false;
	
	}
	
}



