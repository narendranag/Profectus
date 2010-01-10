<?

class Inventory extends Datamapper {
	
	var $table = "inventory";
	var $has_many = array("detail");
	var $has_one = array("vendor", "invoice");
	
	var $status = array(
					"In Stock" => 1,
					"Sold" => 2,
					"Returned" => 3					
					);
	
	function __construct($id = NULL) {
		parent::__construct($id);
	}
	
}
?>