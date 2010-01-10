<?

class Client extends Datamapper {
	
	var $has_many = array("invoice");
	
	function __construct($id = NULL) {
		parent::__construct($id);
	}
	

}
?>