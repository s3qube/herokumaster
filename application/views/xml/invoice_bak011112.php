<Invoice>
	<Id><?= $i->id ?></Id>
	<VendorId><?= $i->nv_customerid ?></VendorId>
	<ReferenceNumber><?= htmlentities($i->referencenumber) ?></ReferenceNumber>
	<UserName><?= htmlentities($i->username) ?></UserName>
	<Currency><?= $i->currencycode ?></Currency>
	<Total><?= $i->total ?></Total>
	<CreateDate><?= ($i->createdate ? date("m/d/Y",$i->createdate) : null); ?></CreateDate>
	<SubmitDate><?= ($i->submitdate ? date("m/d/Y",$i->submitdate) : null); ?></SubmitDate>
	
	<LineItems>
	
		<? foreach ($i->items as $item) { ?>
			<LineItem>
				<LineItemId><?= $item->id ?></LineItemId>
				<PropertyCode><?= $item->nv_propid ?></PropertyCode>
				<OpmProductId><?= $item->opm_productid ?></OpmProductId>
				<ChannelCode><?= $item->channelcode ?></ChannelCode>
				<Property><?= htmlentities($item->property) ?></Property>
				<ProductName><?= htmlentities($item->productname) ?></ProductName>
				<ChargeTypeId><?= $item->chargetypeid ?></ChargeTypeId>
				<ChargeType><?= $item->chargetype ?></ChargeType>
				<Quantity>1</Quantity>
				<UnitPrice><?= $item->chargeamount ?></UnitPrice>
				<Notes><?= htmlentities($item->notes) ?></Notes>
				<ChargeAmount><?= $item->chargeamount ?></ChargeAmount>
			</LineItem>
		<? } ?>
	
	</LineItems>
</Invoice>