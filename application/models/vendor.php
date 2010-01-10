<?

class Vendor extends Datamapper {
	
	var $has_many = array("inventory");
	
	function __construct($id = NULL) {
		parent::__construct($id);
	}	
	

}
?>