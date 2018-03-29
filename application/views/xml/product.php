
<Product>
	<SKU><?= $p['sku'] ?></SKU>
	<ShortSKU><?= substr($p['sku'],0,11) ?></ShortSKU>
	<BodyStyle><?= $p['bodystyle'] ?></BodyStyle>
	<ShortName><?= $p['shortname'] ?></ShortName>
	<ProductName><?= $p['productname'] ?></ProductName>
	<LastModified><?= opmDate($p['lastmodified'], true)?></LastModified>
	<Size><?= $p['size'] ?></Size>
	<Color><?= $p['color'] ?></Color>
</Product>