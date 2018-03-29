
function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1) 
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

  function findPosY(obj)
  {
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

// Copyright 2006-2007 javascript-array.com

var timeout	= 500;
var closetimer	= 0;
var ddmenuitem	= 0;

// open hidden layer
function mopen(id)
{	
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.display = 'none';

	// get new layer and show it
	 
	ddmenuitem = document.getElementById(id);
	
	// determine appropriate position for element based on it's opener! - TE 
	
	var temp = new Array();
	temp = id.split('_');
	var contactID = temp[1];
	
	// alert("CONTACT ID:"+contactID);
		
	openerObject = document.getElementById('appMenuOpener_'+contactID);
	openerX = findPosX(openerObject);
	openerY = findPosY(openerObject);
	
	//alert("pos of opener is" + openerX + "x" + openerY);
	
	ddmenuitem.style.top = (openerY + 9) + "px";
	ddmenuitem.style.left = (openerX - 150) + "px";
	//ddmenuitem.style.visibility = 'visible';

	opm.showDiv(ddmenuitem.id);
}

function mopen_label(id) // this is the mopen function modded to display image labels - just some different positioning stuff.
{	
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.display = 'none';

	// get new layer and show it
	 
	if (ddmenuitem = document.getElementById(id)) { // ensure that we have a valid menu - TE
	
		// determine appropriate position for element based on it's opener! - TE 
		
		var temp = new Array();
		temp = id.split('_');
		var contactID = temp[1];
		
		// alert("CONTACT ID:"+contactID);
		
		openerObject = document.getElementById('appMenuOpener_'+contactID);
		openerX = findPosX(openerObject);
		openerY = findPosY(openerObject);
		
		//alert("pos of opener is" + openerX + "x" + openerY);
		
		ddmenuitem.style.top = (openerY + 9) + "px";
		ddmenuitem.style.left = (openerX + 50) + "px";
		//ddmenuitem.style.visibility = 'visible';
		opm.showDiv(ddmenuitem.id);
		
	}
	
}


// close showed layer
function mclose()
{
	if(ddmenuitem) ddmenuitem.style.display = 'none';
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

// close layer when click-out
document.onclick = mclose; 