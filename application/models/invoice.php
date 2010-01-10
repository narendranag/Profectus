<?

class Invoice extends Datamapper {
	
	var $has_one = array("client");
	var $has_many = array("inventory");
	
	var $status = array(
					"Advance Paid" => 1,
					"Total Paid" => 2
					);
	
	
	function __construct($id = NULL) {
		parent::__construct($id);
	}
	
}
?>