<script language="javascript">

	function removeGListItem(key) {
					
		alert("removin key " + key);

		$("#removeItem").val(key);
		$("#reqForm").submit();
		
			
	}

	$(document).ready(function() {
	
		
	
		// set "clicked" var so we know how form was submitted
		
		$( "#garmentList" ).load( "<?= base_url();?>events/ajaxGarmentList" );
		
		$("form input[type=submit]").click(function() {
		    $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
		    $(this).attr("clicked", "true");
		});
		
		$('select.submitter').change(function () {
		
			$("#reqForm").submit();
		
		});
		
		$( "input.submitter,textarea.submitter" ).blur(function() {
		
			$("#reqForm").submit();
		
		});
		
		$("#reqForm").submit(function(e) {
				
				var val = $("input[type=submit][clicked=true]").val();
				
				if (val != 'Submit Request') {
				
					if (val == "Add Garment") {
					
						$("#addGarment").val("1");
												
						if (!($("#add_catid").val()) || !($("#add_colorid").val()) || !($("#add_numgoods").val())) {
							
							alert("Cannot add garment. Missing info.");
							$("input[type=submit]").attr("clicked", "false");
							return false;
							
						}
					
					} else {
				
						$("#addGarment").val("0");
						
					}	
	
			
					//alert("val:"+val);
					console.log( $( this ).serializeArray() );
				    var postData = $(this).serializeArray();
				    
				 // alert(postData);
				    var formURL = "<?= base_url();?>events/ajaxReqFormHandler";
				    $.ajax(
				    {
				        url : formURL,
				        type: "POST",
				        data : postData,
				        success:function(data, textStatus, jqXHR) 
				        {
				           $("#garmentList").load( "<?= base_url();?>events/ajaxGarmentList" );
				           $('#add_catid').prop('selectedIndex',0);
				           $('#add_colorid').prop('selectedIndex',0);
				           $("#add_numgoods").val('');
				           $("#removeItem").val('');
				        },
				        error: function(jqXHR, textStatus, errorThrown) 
				        {
				             //alert("epic fail brahhh!");      
				        }
				    });
				    
				    // reset "clicked" attr 
				    
				    $("input[type=submit]").attr("clicked", "false");
				    
				    e.preventDefault(); //STOP default action
				 
				} 
				
				
				
			});
			
				
 
 //Submit  the FORM
	
		$("#reqForm").validate();

		/*function updateProductLines() {
		
			var propertyid = $('#propertySelect').val();  
			
			if (propertyid != "") {
			
				//alert("bow wow");
			
				var url = '<?= base_url();?>ajax/fetchProductLineJson/'+propertyid+'/<?= $product->opm_productid?>';
				
				//$('#productLineSelect').load(url);
				
				
				$.getJSON(url, function(data){
			
					//alert(data);
			
					var html = '';
					var len = data.length;
				
				    for (var i = 0; i< len; i++) {
				   		
						html += '<option value="' + data[i].productlineid + '">' + data[i].productline + '</option>';
				   
				    }
			
					//alert(html);
					$('#productLineSelectEl').find('option').remove().end().append(html).trigger("liszt:updated");
				
				});
			
				// also fetch default description
				
				<? if ($mode == 'add') { ?>
				
					var url = '<?= base_url();?>ajax/fetchDefaultProductDescription/';
					
					$.post(url, { propertyid: propertyid}, function(data) {
	  				
	  					if (data != '') {
	  						$("textarea#productdesc").val(data);
						}
						
					});
				
				<? } ?>

				
			}
			
			
			//$("#productLineSelectEl");
				
		
		}*/
		
		//updateProductLines(); // update product lines onload, in case back button was pushed
	
		$(".chzn-select").chosen(function() {
 			
 			//updateProductLines();
 
		});

	
		/*$('#propertySelect').change(function() {
 			
 			updateProductLines();
 
		});*/
	
	});
	

</script>

<div class="reqContainer">
	
	<form id="reqForm" method="post" name="reqForm" action="<?= base_url() ?>events/saveRequest">
	<div class="reqCol1">
		
		<h3 class="reqField">Property <span class="productEditRequired">(required)</span></h3>
							
		<select name="propertyid" class="reqField required chzn-select submitter" id="propertyid">
			
			<option value="">Please Select...</option>
			
			<? foreach ($properties->result() as $p) { ?>
			
				<option value="<?= $p->propertyid ?>" <? if ($formData['propertyid'] == $p->propertyid) echo "SELECTED" ?>><?= $p->property ?></option>
			
			<? } ?>
			
		</select>
		
		<br /><br />		
		
		<h3 class="reqField">Bravado A&R <span class="productEditRequired">(required)</span></h3>
							
		<select name="arid" class="reqField required chzn-select submitter" id="arid">
			
			<option value="">Please Select...</option>
			
			<? foreach ($internalUsers->result() as $u) { ?>
			
				<option value="<?= $u->userid ?>" <? if ($formData['arid'] == $u->userid) echo "SELECTED" ?>><?= $u->username ?></option>
			
			<? } ?>
			
		</select>
		
		<br /><br />	
		
		<h3 class="reqField">Design Process <span class="productEditRequired">(required)</span></h3>
							
		<select name="design_process" class="reqField required chzn-select submitter" id="arid">
			
			<option value="">Please Select...</option>
			
			<option value="IN_HOUSE" <? if ($formData['design_process'] == 'IN_HOUSE') echo "SELECTED" ?>>In House Only</option>
			<option value="FREELANCE_OK" <? if ($formData['design_process'] == 'FREELANCE_OK') echo "SELECTED" ?>>Freelance OK</option>
			
		</select>
		
		<br /><br />			
		
		<h3 class="reqField">Tour Start Date <span class="productEditRequired">(required)</span></h3>
							
		<input type="text" class="w8em format-m-d-y divider-dash highlight-days-12 submitter" id="dp-normal-b1" name="tourStartDate" value="<?= (isset($formData['tourStartDate']) ? $formData['tourStartDate'] : null) ?>" maxlength="10" style="font-size:13pt;width:300px;"  />	
				
		<br /><br />
		
		<h3 class="reqField">Art Submission Deadline <span class="productEditRequired">(required)</span></h3>
							
		<input type="text" class="w8em format-m-d-y divider-dash highlight-days-12 submitter" id="dp-normal-b2" name="artSubmissionDeadline" value="<?= (isset($formData['artSubmissionDeadline']) ? $formData['artSubmissionDeadline'] : null) ?>" maxlength="10" style="font-size:13pt;width:300px;" />	
				
		<br /><br />
		
		<h3 class="reqField">Sample Approval Deadline</h3>
							
		<input type="text" class="w8em format-m-d-y divider-dash highlight-days-12 submitter" id="dp-normal-b3" name="sampleApprovalDeadline" value="<?= (isset($formData['sampleApprovalDeadline']) ? $formData['sampleApprovalDeadline'] : null) ?>" maxlength="10" style="font-size:13pt;width:300px;" />	
				
		<br /><br />
		

		
		<!--<textarea name="booboo">GOGOGOGOG  lalalala </textarea>-->

		
		
	</div>
	
	<div class="reqCol2">
		
		<h3 class="reqField">Bravado Production <span class="productEditRequired">(required)</span></h3>
							
		<select name="prodid" class="reqField required chzn-select submitter" id="prodid">
			
			<option value="">Please Select...</option>
			
			<? foreach ($internalUsers->result() as $u) { ?>
			
				<option value="<?= $u->userid ?>" <? if ($formData['prodid'] == $u->userid) echo "SELECTED" ?>><?= $u->username ?></option>
			
			<? } ?>
			
		</select>
		
		<br /><br />		
		
		<h3 class="reqField">Bravado Creative <span class="productEditRequired">(required)</span></h3>
							
		<select name="creativeid" class="reqField required chzn-select submitter" id="creativeid">
			
			<option value="">Please Select...</option>
			
			<? foreach ($internalUsers->result() as $u) { ?>
			
				<option value="<?= $u->userid ?>" <? if ($formData['creativeid'] == $u->userid) echo "SELECTED" ?>><?= $u->username ?></option>
			
			<? } ?>
			
		</select>
		
		<br /><br />		
		
		<h3 class="reqField">Estimated Art Costs:</span></h3>
							
		<input type="text" name="estArtCosts" value="<?= $formData['estArtCosts'] ?>" class="submitter"/>
		
		<br /><br />	
		
		<h3 class="reqField">Rush Processing <span class="productEditRequired">(less than 2 wks)</span></h3>
							
		<select name="rushProcessing" class="reqField required chzn-select submitter" id="rushProcessing">
			
			<option value="NO" <? if ($formData['rushProcessing'] == 'NO') echo "SELECTED" ?>>No</option>
			<option value="YES" <? if ($formData['rushProcessing'] == 'YES') echo "SELECTED" ?>>Yes</option>
			
		</select>
		
	</div>
	
	<br />
	
	<div class="searchDiv"></div>
	
	<br />
	
	<div class="reqFormGarments">
		
		<h3 class="reqField">Add Garment:</h3>
		
		<select name="add_catid" id="add_catid" style="height:24px;">
			
			<option value="">Please Select...</option>
			
			<? foreach ($categories as $key=>$cat) { ?>
							
				<option value="<?= $key ?>" ><?= $cat ?></option>
			
			<? } ?>
			
		</select>
		
		<select name="add_colorid" id="add_colorid" style="height:24px;">
		
			<option value="0">Add Color...</option>
			
			<? foreach ($colors->result() as $c) { ?>
			
				<option value="<?= $c->id?>"><?= $c->color ?></option>
			
			<? } ?>
			
		</select>
		
		Num of goods:
		
		<input type="text" name="add_numgoods" id="add_numgoods" size="4" />
		
		<input type="hidden" name="addGarment" id="addGarment" value="0" />
		
		<input type="submit" name="addGarmentBtn" id="submitBtn" value="Add Garment" />
		
		<br /><br/>
		
		<div id="garmentList">
			
			
			
		</div>
		
		
		
	</div>
	
	<br /><br />
	
	<h3 class="reqField">Notes / Additional Direction:</h3>
	
	<textarea name="reqNotes" id="reqNotes" class="submitter"><?= $formData['reqNotes'] ?></textarea>
	
	<br /><br />
	
	<input type="submit" name="submitRequest" class="invoiceBtn" value="Submit Request" />
	
	<input type="hidden" name="removeItem" id="removeItem" value="" />


	</form>
	<pre>
	<?//print_r($formData); ?>
	</pre>
</div>



