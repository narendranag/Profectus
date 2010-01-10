<?

class Detail extends Datamapper {
	
	var $has_one = array("inventory");
	
	function __construct($id = NULL) {
		parent::__construct($id);
	}

	
}
?>