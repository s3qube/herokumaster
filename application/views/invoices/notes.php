
<? foreach ($notes->result() as $n) { ?>
		
		<table border="0" style="margin-left:20px; margin-right:20px; margin-top:10px;">
						
			<tr>
				<td valign="top"><? $this->opm->displayAvatar(24); ?></td>
				<td valign="top">
					<div class="prodSummaryForumHeader">Posted <?= opmDate($n->timestamp) ?> by <?= $n->username ?></div>
					<!--<div class="prodSummaryForumTitle">Lookin good boss</div>-->
					<div class="prodSummaryForumPost" style="margin-top:10px;"><?= nl2br($n->note) ?></div>
				</td>
			</tr>
			
		</table>
		
		<br />
		
		<div class="invoiceCommentDiv"></div>
		
<? } ?>