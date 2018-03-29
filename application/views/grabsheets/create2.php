
<style>
	#sortable1, #sortable2, #sortable3 { list-style-type: none; margin: 0; padding: 0 0 2.5em; float: left; margin-right: 10px; }
	#sortable1 li, #sortable2 li, #sortable3 li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; width: 120px; }

	#sortable2, #sortable3 { border: solid 1px black; float:right;}
	
	#sortable2 li, #sortable3 li { display:inline-block; width: 70px;}
	
	#sortable3 { margin-top:20px;}
	
	#gsCntrls {
		
		
		width:300px;
		border: 1px solid #cccccc;
		min-height:400px;
		margin-left:10px;
		margin-top:-1px;
		
		padding-top:30px;
		padding-left:10px;
		
		
	}
	
	#gsProdSearch {
		
		display:none;
		width:300px;
		border: 1px solid #cccccc;
		min-height:400px;
		margin-left:10px;
		margin-top:-1px;
		
		padding-top:30px;
		padding-left:10px;
		
		
	}
	
	#gsTabs {
		
		margin-left:10px;
		
	}
	
	.gsTab {
		
		
		width:120px;
		border:1px solid #cccccc;
		display:inline-block;
		height:30px;
		text-align: center;
		line-height: 30px;	
		background-color: #efefef;	
	}
	
	div.gsField {
		
		display:inline-block;
		width:70px;
		padding-bottom:20px;
	}
	
	input.gsField {
		
		display:inline-block;
		width:150px;
		
				
		
	}
	
	#gsSheet {
		
		float: right;
		width:400px;
		margin-right:30px;
	}
	
	#gsSheetCover {
		
		
		height:300px;
		background-color: #000000;
		
	}
	
	#gsPageCntrls {
		
		float:right;
		width:390px;
		border: 1px solid #cccccc;
		min-height:200px;
		margin-right:30px;
		margin-top:20px;
		
		padding-top:30px;
		padding-left:10px;
		
		
	}
	
	#gsPageNav {
		
		margin-top:10px;
		float: right;
		width:400px;
		margin-right:30px;
	}

</style>

<script>
  $(function() {
    $( "#sortable1, #sortable2, #sortable3" ).sortable({
      connectWith: ".connectedSortable",
      over: function(event, ui) {
	      
	      alert(event.target.id);
	      console.log(event);
	      
      }
    }).disableSelection();
  });
  
 // alert("boo"); 
  
  jQuery(document).ready(function(){
  
	  $("#gsTabProducts").click(function() { 
		 
		 	$("#gsCntrls").fadeOut('fast');
		 	$("#gsProdSearch").fadeIn('fast'); 
		  
	  });
	  
	  $("#gsTabSummary").click(function() {
		 
		 	$("#gsProdSearch").fadeOut('fast');
		 	$("#gsCntrls").fadeIn('fast'); 
		  
	  });
  
  });
  
</script>

<div id="gsSheet">
	
	<div id="gsSheetCover"></div>
	
</div>

<div id="gsPageNav">
	
	<a href="#">Cover</a>
	
	<? for ($x=1;$x<10;$x++) { ?>
	
		<a href="#"><?= $x ?></a>
	
	<? } ?>
	
</div>

<div id="gsPageCntrls">
	
	
	<div class="gsField">Page Style</div>
	<select class="gsField">
	
		<option value="0">1x4</option>
		
	</select>
	
</div>


<div id="gsTabs">
	
	<div class="gsTab" id="gsTabSummary">Summary</div>
	<div class="gsTab" id="gsTabProducts">Products</div>
	
</div>

<div id="gsCntrls">
	
	<div class="gsField">Title</div>
	<input class="gsField" />
	
	<br />
	
	<div class="gsField">Client Logo</div>
	<select class="gsField">
	
		<option value="0">Hot Topic</option>
		
	</select>
	
	<br />
	
	<div class="gsField">Band Logo</div>
	<select class="gsField">
	
		<option value="0">Hot Topic</option>
		
	</select>
	
	<br />
	
	<div class="gsField">Branding</div>
	<select class="gsField">
	
		<option value="0">Hot Topic</option>
		
	</select>
	
	
	
</div>

<div id="gsProdSearch">
	
	<div class="gsField">Proududds</div>
	<input class="gsField" />
	
	<br />
	
	<div class="gsField">Client Logo</div>
	<select class="gsField">
	
		<option value="0">Hot Topic</option>
		
	</select>
	
	<br />
	
	<div class="gsField">Band Logo</div>
	<select class="gsField">
	
		<option value="0">Hot Topic</option>
		
	</select>
	
	<br />
	
	<div class="gsField">Branding</div>
	<select class="gsField">
	
		<option value="0">Hot Topic</option>
		
	</select>
	
	<!--<li onmousedown="killGsTooltip();" onmouseover="showGsTooltip(this,11325);" onmouseout="hideGsTooltip();" class="gsItem" alt="11325" id="gsItem_11325" background-image="/imageclass/viewThumbnail/11325" style="background-image: url(http://dev.opm.com/imageclass/viewThumbnail/11325/30);">Trans-Siberian Orchestra&nbsp;&nbsp;//&nbsp;&nbsp;Winter Queen Tour shirt&nbsp;&nbsp;<span style="color:green;font-weight:bold;font-size:12px;">?</span>&nbsp;&nbsp;<a href="#" onclick="setGSCommentID(11325); return false;" class="blueLink">?</a></li>-->

	<li class="gsItem" alt="11325" id="gsItem_11325" background-image="/imageclass/viewThumbnail/11325" style="background-image: url(http://dev.opm.com/imageclass/viewThumbnail/11325/30);">Trans-Siberian Orchestra&nbsp;&nbsp;//&nbsp;&nbsp;Winter Queen Tour shirt&nbsp;&nbsp;<span style="color:green;font-weight:bold;font-size:12px;">?</span>&nbsp;&nbsp;<a href="#" onclick="setGSCommentID(11325); return false;" class="blueLink">?</a></li>
	
	
</div>


<div style="clear:both"><br /></div>

<!--
<ul id="sortable1" class="connectedSortable">

  <li class="ui-state-default">Item 1</li>
  <li class="ui-state-default">Item 2</li>
  <li class="ui-state-default">Item 3</li>
  <li class="ui-state-default">Item 4</li>
  <li class="ui-state-default">Item 5</li>

</ul>
 
<ul id="sortable2" class="connectedSortable">

  <li class="ui-state-highlight">Item 1</li>
  <li class="ui-state-highlight">Item 2</li>
  <li class="ui-state-highlight">Item 3</li>
  <li class="ui-state-highlight">Item 4</li>


</ul>


<ul id="sortable3" class="connectedSortable">

  <li class="ui-state-highlight" style="background-image: url(<?= base_url() ?>imageclass/viewThumbnail/4/50);">Item 1</li>
  <li class="ui-state-highlight">Item 2</li>
  <li class="ui-state-highlight">Item 3</li>
  <li class="ui-state-highlight">Item 4</li>


</ul>

-->