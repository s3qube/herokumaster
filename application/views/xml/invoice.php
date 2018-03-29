<Invoice>
	<Id><?= $i->id ?></Id>
	<VendorId><?= $i->nv_customerid ?></VendorId>
	<ReferenceNumber><?= htmlspecialchars($i->referencenumber) ?></ReferenceNumber>
	<UserName><?= htmlspecialchars($i->username) ?></UserName>
	<Currency><?= $i->currencycode ?></Currency>
	<Total><?= $i->total ?></Total>
	<CreateDate><?= ($i->createdate ? date("m/d/Y",$i->createdate) : null); ?></CreateDate>
	<SubmitDate><?= ($i->submitdate ? date("m/d/Y",$i->submitdate) : null); ?></SubmitDate>
	
	<LineItems>
	
		<? foreach ($i->items as $item) { ?>
			<LineItem>
				<LineItemId><?= $item->id ?></LineItemId>
				<PropertyCode><?= $item->nv_propid ?></PropertyCode>
				<GlAccount><?= $item->glaccount ?></GlAccount>
				<OpmProductId><?= $item->opm_productid ?></OpmProductId>
				<ChannelCode><?= $item->channelcode ?></ChannelCode>
				<Property><?= substr(htmlspecialchars($item->property),0,18) ?></Property>
				<ProductName><?= substr(htmlspecialchars($item->productname),0,48) ?></ProductName>
				<Quantity>1</Quantity>
				<UnitPrice><?= $item->chargeamount ?></UnitPrice>
				<Notes><?= substr(htmlspecialchars($item->notes),0,48) ?></Notes>
				<ChargeAmount><?= $item->chargeamount ?></ChargeAmount>
			</LineItem>
		<? } ?>
	
	</LineItems>
</Invoice>