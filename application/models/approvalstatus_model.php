<?

class Approvalstatus_model extends CI_Model {

    function ApprovalstatusModel()
    {
        // Call the Model constructor
		parent::__construct();   
    }
    
    function fetchApprovalStatuses($overallOnly = false) // some statuses don't apply to overall products (e.g. "Approved w/ revisions")
    {
    
		$sql = "SELECT opm_approvalstatuses.*
				FROM opm_approvalstatuses
				WHERE opm_approvalstatuses.approvalstatusid <> 2
				ORDER BY opm_approvalstatuses.order";
        	
        $query = $this->db->query($sql);
        
        $arrAppStatus[0] = array("id"=>"p","status"=>"Pending Approval");
        
        foreach ($query->result() as $row) {
        
        		$arrAppStatus[] = array("id"=>$row->approvalstatusid,"status"=>$row->approvalstatus);
        
        }
        	
        	
        return $arrAppStatus;
        
        
    }
    
    function updateApprovalStatus($opm_productid) {
        
    	$CI =& get_instance();    	
    	$CI->load->model('products_model');
    	$product = $CI->products_model->fetchProductInfo($opm_productid,false,true);
    	
    	if ($product->approvalstatusid == $this->config->item('appStatusExpired')) {
    	
    		$approvalStatusID = $this->config->item('appStatusExpired');
    	
    	} else {
    	
    	
    		$origApprovalStatusID = $product->approvalstatusid;
    	
	    	if ($product->sampleappstatusid == 1) { // sample approval status trumps all
	    	
	    		$approvalStatusID = $this->config->item('appStatusApproved');
	    		
	    	} else if ($product->active_resubmit == 1) {
	    	
	    		$approvalStatusID = $this->config->item('appStatusAwaitingRevisions');
	    	
	    	} else {
	    	
		    	
		    	foreach ($product->approvalInfo as $ai) { // create array of approvalstatusids
		    		
		    		if ($ai->approvalrequired) // make sure contact's approval is required, otherwise their status will not be considered!
		    			$arrAppStatusIDs[] = $ai->approvalstatusid;
		    	
		    	}
		    	
		    	
		    	if ($product->approval_methodid == 1) { // single contact approval
		    	
		    		if ( in_array($this->config->item('appStatusApproved'), $arrAppStatusIDs) || in_array($this->config->item('appStatusApprovedWComments'), $arrAppStatusIDs)) // there is an approval, product must be approved.
						$approvalStatusID = $this->config->item('appStatusApproved');
		    		else if (in_array($this->config->item('appStatusRejected'), $arrAppStatusIDs)) // there is a rejection, product must be rejected.
		    			$approvalStatusID = $this->config->item('appStatusRejected');
		    		else // none of the above. must be pending.
		    			$approvalStatusID = 0;
		    		
		    	} else { // multiple contact approval
		    	
		    		if (in_array($this->config->item('appStatusRejected'), $arrAppStatusIDs)) { // if there are any rejections, product is automatically rejected.
		    		
		    			$approvalStatusID = $this->config->item('appStatusRejected');
		    		
		    		} else if (in_array(0,$arrAppStatusIDs)) { // if there are any pendings, product is pending!
		    		
		    			$approvalStatusID = 0;
		    		
		    		} else { // product must be approved, since there are no pendings or rejections, right?!
		    		
		    			$approvalStatusID = $this->config->item('appStatusApproved');
		    		
		    		}
		    	
		    	}
	    	
	    	
	    	
	    	}
	    	
	    	
	    	
	    	
	    	$sql = "UPDATE opm_products SET approvalstatusid = '".$approvalStatusID."' WHERE opm_productid = '".$opm_productid."'";
	    	$this->db->query($sql);
	    	
	    	// lock product if approved
	    	
	    	if ($approvalStatusID = $this->config->item('appStatusApproved')) {
		    	
		    	$CI->products_model->changeLockStatus($opm_productid,1);

	    	}
	    	
	    	// add history entry, if there is a change.
	    	
	    	if ($origApprovalStatusID != $approvalStatusID) {
	    		
	    		if ($approvalStatusID != 0) {
				
					$sql = "SELECT approvalstatus FROM opm_approvalstatuses WHERE approvalstatusid = '".$approvalStatusID."'";
					$result = $this->db->query($sql);
					$row = $result->row();
				
				} else {
				
					$row->approvalstatus = "Pending Approval";
				
				}
				
				$this->opm->addHistoryItem($opm_productid,"Product approval status changed to " . $row->approvalstatus);
	    	
	    	}
	    	
    	
    	}
    	
    	
    	
    	return $approvalStatusID;
    	
    
    }
    
    function changeConceptApprovalStatus($opm_productid,$approvalstatusid) {
    
    
		if ($approvalstatusid == 0) {
		
			// we are approving concept.
			
			$sql = "UPDATE opm_products SET approvalstatusid = 0 WHERE opm_productid = " . $this->db->escape($opm_productid);
			
			if ($this->db->query($sql))
				return true;
			else
				return false;
		
		}
    
    }
    
    function changeApprovalStatus($userid,$opm_productid,$approvalstatusid,$verbal = false,$revisions = "") {
    
    	if ($approvalstatusid == 0) { // this is a reversal, delete entry!
    	
    		// determine status prior to reversal.
    		
    		$sql = "SELECT opm_approvalstatuses.approvalstatus,users.username
    				FROM opm_approvalstatus
    				LEFT JOIN opm_approvalstatuses ON opm_approvalstatuses.approvalstatusid = opm_approvalstatus.approvalstatusid
    				LEFT JOIN users ON users.userid = opm_approvalstatus.userid
    				WHERE opm_approvalstatus.opm_productid = '".$opm_productid."' AND opm_approvalstatus.userid = '".$userid."'";
    				
			$result = $this->db->query($sql);
			
			if ($result->num_rows() > 0) {
			
				$row = $result->row();
				
				$sql = "DELETE FROM opm_approvalstatus WHERE opm_productid = '".$opm_productid."' AND userid = '".$userid."'";
				$this->db->query($sql);
				
				// add history entry
						
				$this->opm->addHistoryItem($opm_productid, $row->approvalstatus . " by " . $row->username . " REVERSED BY ".$this->userinfo->username);
				
				// send email to appropriate users
				
				// assemble data for email!
				
				$arrData['username'] = $this->userinfo->username;
				$arrData['approvalstatus'] = "Reversed " .$row->approvalstatus . " by " . $row->username . " on ";
				$arrData['productInfo'] = $this->products_model->fetchProductInfo($opm_productid);
				
				$this->opm->sendProductEmail($opm_productid,"approval_status_changed",$arrData);
				
				//update overall product status
				
				$this->updateApprovalStatus($opm_productid);
				
				return true;
			
			
			} else {
			
				$this->opm->displayError("Nothing to reverse!","/products/view/" . $opm_productid);
				return true;
			
			}
     		
     	}
     		
     	// first check if an approval entry exists for this user, product!
     	
     	$sql = "SELECT * FROM opm_approvalstatus
     			WHERE opm_productid = '".$opm_productid."'
     			AND userid = '".$userid."'";
     	
     	$result = $this->db->query($sql);
     	
     	if ($result->num_rows == 0) { // no approval yet exists, insert entry!
     		
     		if (!$verbal) { // this is not a verbal approval, no verbal_userid needed.
     			
     			$sql = "INSERT INTO opm_approvalstatus (approvalstatusid,opm_productid,userid,timestamp)
     					VALUES ('".$approvalstatusid."','".$opm_productid."','".$userid."',".mktime().")";
     		
     		} else {
     			
     			$sql = "INSERT INTO opm_approvalstatus (approvalstatusid,opm_productid,userid,verbal_userid,timestamp)
     					VALUES ('".$approvalstatusid."','".$opm_productid."','".$userid."','".$this->userinfo->userid."',".mktime().")";
     		
     		}
     		
     		$this->db->query($sql);
     		
     		// add history entry
    	
			$sql = "SELECT approvalstatus FROM opm_approvalstatuses WHERE approvalstatusid = '".$approvalstatusid."'";
			$result = $this->db->query($sql);
			$row = $result->row();
			
			$this->load->model('users_model');
    		$appuser = $this->users_model->fetchUserInfo($userid);
			
			
			// assemble data for email!
				
				$arrData['username'] =  $appuser->username;
				$arrData['productInfo'] = $this->products_model->fetchProductInfo($opm_productid);
				$arrData['revisions'] = $revisions;
			
			if ($verbal) {
			
				$arrData['approvalstatus'] = $row->approvalstatus . " (verbal from ".$this->userinfo->username.")";
				$this->opm->addHistoryItem($opm_productid,"Product " . $row->approvalstatus . " by " . $appuser->username . " (verbal from ".$this->userinfo->username.")");
			
			} else {
			
				$arrData['approvalstatus'] = $row->approvalstatus;
				$this->opm->addHistoryItem($opm_productid,"Product " . $row->approvalstatus . " by " . $appuser->username);
			
			
			}
				
     		
     		// send email!
     		
     		$this->opm->sendProductEmail($opm_productid,"approval_status_changed",$arrData);
     		
     		// update overall product approval status!
     		
     		$this->updateApprovalStatus($opm_productid);
     		return true;
     		
		} else { 
		
			$this->opm->displayError("Couldn't change approval status","/products/view/" . $opm_productid);
			return false;
		
		}
     
    
    }
    
    function expireProduct($opm_productid,$onoff) { // onoff: 1=expire,2=unexpire
    
    
    	if ($onoff) {
    	
    		$sql = "UPDATE opm_products SET approvalstatusid = ".$this->config->item('appStatusExpired')." WHERE opm_productid = " . $this->db->escape($opm_productid);
    	
    	} else {
    	
    		$sql = "UPDATE opm_products SET approvalstatusid = 0 WHERE opm_productid = " . $this->db->escape($opm_productid);
    	
    	}
    	
    	if ($this->db->query($sql))
    			return true;
    		else
    			return false;
    
    }
    
    function fetchExploitStatus($id) {
	    
	    $sql = "SELECT * FROM opm_exploit_statuses WHERE id = " . $this->db->escape($id);
	    $query = $this->db->query($sql);
	    
	    return $query->row()->exploitstatus;
	    
    }
    
    function fetchUsageStatus($id) {
	    
	    $sql = "SELECT * FROM opm_usage_statuses WHERE id = " . $this->db->escape($id);
	    $query = $this->db->query($sql);
	    
	    return $query->row()->usagestatus;
	    
    }
    

}

?>