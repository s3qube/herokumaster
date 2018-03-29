

var opm = {

		changeOptionColor: function(fieldname,id){
			
			//alert($(fieldname+'_'+id).checked);
			
			if ($(fieldname+'_'+id).checked == true) {
			
				/*$(target).setStyles({
						'display': 'block',
						'visibility':'visible',
						'opacity':0
				});*/
				
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
			
				/*$(target).setStyles({
						'display': 'block',
						'visibility':'visible',
						'opacity':0
				});*/
				
				//alert("you checked it");
				
				
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
			
			// alert($('usergroup_'+target).getStyle('visibility'));
			
			/*if ($(target).getStyle('visibility') == 'hidden')
				opm.showDiv(target);
			else
				opm.hideDiv(target);*/
				
				
			
				
			/*if ($('#'+target).is(":visible")) {
				
				$('#'+target).fadeOut();
				
								
			} else {
			
				$('#'+target).css('display') = 'block';
				$('#'+target).css('visibility') = 'visible';
				//$('#'+target).fadeIn();
				
				
			}*/
			
			$('#'+target).toggle('slow', function() {
   			
   				 // Animation complete.
 			 
 			 });
		
		},

		showDiv: function(target) {
		
			/*$(target).setStyles({
			'display': 'block',
			'visibility':'visible',
			'opacity':0
			});
			
			new Fx.Style($(target), "opacity", {duration:700}).start(1.0);*/
			
			//alert(target);
			
			$('#'+target).fadeIn();
			
		//	alert("Tried to show:"+'#'+target);
		
		},
		
		hideDiv: function(target) {
		
			/*new Fx.Style($(target), "opacity", {duration:700}).start(0.0).addEvent('onComplete', function () {

			$(target).setStyles({
			'display': 'none',
			'visibility':'hidden'
			});
		}.bind(this));*/
		
			
			/*$(target).setStyles({
			'display': 'none',
			'visibility':'hidden'
			});*/
			
			
			$('#'+target).fadeOut;
			
		
		},
		
		swapDetailImg: function(strSrc){
		
			/*new Fx.Style($('detailImageDiv'), "opacity", {duration:300}).start(0.0).addEvent('onComplete', function () {

				$('detailImage').setProperty('src',strSrc);
				
				new Fx.Style($('detailImageDiv'), "opacity", {duration:300}).start(1.0);
				
			}.bind(this));*/
			
			
			//$('#detailImage').fadeOut('fast').attr("src",strSrc).fadeIn('fast');
			
			
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
			$('#content').load(url);		
		
		}
			
}




$(window).load(function() {
	
	if (typeof Shadowbox != 'undefined') {
 	
		Shadowbox.init({
	
			skipSetup: true,
			onClose: reloadContent
		
		});
	
	}
      
});


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
	
	
	//alert(contentUrl);
	
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
	
	
	//alert(contentUrl);
	
	Shadowbox.open({
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     450,
		width:      290
	});
	

}

function openGuestUploadWindow(opmProductID,fileType) {


	contentUrl = base_url + 'guestDownload/setup/' + opmProductID + '/' + fileType + '/' + '1'; // 1 indicates that this is an upload!
	
	
	//alert(contentUrl);
	
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
	
	
	//alert(contentUrl);
	
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
	
	
	//alert(contentUrl);
	
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
	
	//alert(contentUrl);
	
	Shadowbox.open({
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     350,
		width:      600
	});
	

}

function openAccountsWindow(opm_productid,accountid) {

	contentUrl = base_url + 'products/purchaseDialog/' + opm_productid + "/" + accountid;

	
	Shadowbox.open({
		title:      '',
		player:       'iframe',
		content:    contentUrl,
		height:     275,
		width:      400
	});
	
	

}


function shadowboxImage(imageid,width) {

	contentUrl = base_url + 'imageclass/imageViewer/' + imageid + "/" + width;

	//varContent = "<div align='center' style='padding-top:2px'><img src='" + base_url + "imageclass/viewThumbnail/" + imageid + "/" + "800'" + "></div>";
	
	//alert(contentUrl);
	
	Shadowbox.open({
		title:      '',
		player:       'iframe',
		content:    contentUrl,

	});
	
	//type:       'html',
	

}

function detailImageShadowbox(imageid) {

	srcUrl = $("#detailImage").attr('src');
	arrInfo = srcUrl.split("viewThumbnail/");
	arr2 = arrInfo[1].split("/");
	imageID = arr2[0];
	//alert(imageID);

	shadowboxImage(imageID,500);

	//varContent = "<div align='center'><img src=" + srcUrl +"></div>";
	
	//alert(contentUrl);
	
	/*Shadowbox.open({
		title:      '',
		type:       'html',
		content:    varContent,
		height:     500,
		width:      500
	});*/
	

}


function changeGroup(opm_productid,usergroupid,objChecked){
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	var url = base_url + 'ajax/changeGroup/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	/*new Ajax(url, {
		method: 'post',
		postBody: 'opm_productid=' + opm_productid + "&usergroupid=" + usergroupid + "&onoff=" + onoff,
		onComplete: function(response) {
			
			if (response == 'activated') {
			
				$('invGroupRowTr_'+usergroupid).setProperty('class','invGroupRow_active');
				$('invGroupRowTd_'+usergroupid).setProperty('class','invGroupText_active');
				$('invGroupRowImg_'+usergroupid).setProperty('src', base_url + '/resources/images/inv_groupicon.gif');
			
			} else if (response == 'deactivated') {
			
				$('invGroupRowTr_'+usergroupid).setProperty('class','invGroupRow');
				$('invGroupRowTd_'+usergroupid).setProperty('class','invGroupText');
				$('invGroupRowImg_'+usergroupid).setProperty('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}
			
			
		
		}
	}).request();*/
	
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
	
	/*new Ajax(url, {
		method: 'post',
		postBody: 'opm_productid=' + opm_productid + "&territoryid=" + territoryid + "&onoff=" + onoff,
		onComplete: function(response) {
			
			if (response == 'activated') {
			
				$('invTerritoryRowTr_'+territoryid).setProperty('class','invGroupRow_active');
				$('invTerritoryRowTd_'+territoryid).setProperty('class','invGroupText_active');
				$('invTerritoryRowImg_'+territoryid).setProperty('src', base_url + '/resources/images/inv_territoryicon.gif');
			
			} else if (response == 'deactivated') {
			
				$('invTerritoryRowTr_'+territoryid).setProperty('class','invGroupRow');
				$('invTerritoryRowTd_'+territoryid).setProperty('class','invGroupText');
				$('invTerritoryRowImg_'+territoryid).setProperty('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}	
		
		}
	}).request();*/
	
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
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	
	var url = base_url + 'ajax/changeRight/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	/*new Ajax(url, {
		method: 'post',
		postBody: 'opm_productid=' + opm_productid + "&rightid=" + rightid + "&onoff=" + onoff,
		onComplete: function(response) {
			
			if (response == 'activated') {
			
				$('invRightRowTr_'+rightid).setProperty('class','invGroupRow_active');
				$('invRightRowTd_'+rightid).setProperty('class','invGroupText_active');
				$('invRightRowImg_'+rightid).setProperty('src', base_url + '/resources/images/inv_righticon.gif');
			
			} else if (response == 'deactivated') {
			
				$('invRightRowTr_'+rightid).setProperty('class','invGroupRow');
				$('invRightRowTd_'+rightid).setProperty('class','invGroupText');
				$('invRightRowImg_'+rightid).setProperty('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}	
		
		}
	}).request();*/
	
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
	
	/*new Ajax(url, {
		method: 'post',
		postBody: 'propertyid=' + propertyid + "&territoryid=" + territoryid + "&onoff=" + onoff,
		onComplete: function(response) {
			
			if (response == 'activated') {
			
				$('invTerritoryRowTr_'+territoryid).setProperty('class','invGroupRow_active');
				$('invTerritoryRowTd_'+territoryid).setProperty('class','invGroupText_active');
				$('invTerritoryRowImg_'+territoryid).setProperty('src', base_url + '/resources/images/inv_territoryicon.gif');
			
			} else if (response == 'deactivated') {
			
				$('invTerritoryRowTr_'+territoryid).setProperty('class','invGroupRow');
				$('invTerritoryRowTd_'+territoryid).setProperty('class','invGroupText');
				$('invTerritoryRowImg_'+territoryid).setProperty('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}	
		
		}
	}).request();
	
	*/
	
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

function changePropertyRight(propertyid,rightid,objChecked){
			
	//var url = base_url + 'ajax/changeGroup/' + opm_productid + "/" + usergroupid + "/" + onoff;
	
	var url = base_url + 'ajax/changePropertyRight/';
				
	if (objChecked)
		onoff = 1;
	else
		onoff = 0;
	
	/*new Ajax(url, {
		method: 'post',
		postBody: 'propertyid=' + propertyid + "&rightid=" + rightid + "&onoff=" + onoff,
		onComplete: function(response) {
			
			if (response == 'activated') {
			
				$('invRightRowTr_'+rightid).setProperty('class','invGroupRow_active');
				$('invRightRowTd_'+rightid).setProperty('class','invGroupText_active');
				$('invRightRowImg_'+rightid).setProperty('src', base_url + '/resources/images/inv_righticon.gif');
			
			} else if (response == 'deactivated') {
			
				$('invRightRowTr_'+rightid).setProperty('class','invGroupRow');
				$('invRightRowTd_'+rightid).setProperty('class','invGroupText');
				$('invRightRowImg_'+rightid).setProperty('src', base_url + '/resources/images/x.gif');
				
			} else {
			
				alert("there was a problem with your request");
			
			}	
		
		}
	}).request();
	
	*/
	
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



function doSomething(response) {

	alert(response);
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
 var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
   var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
   if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function randomString() {
	
	alert("ello");

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


