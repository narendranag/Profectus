function selectItem(li, elementID) {
	$("#"+elementID).val(0);
	var setVal = (li.extra) ? li.extra[0] : 0;
	$("#"+elementID).val(setVal);
}