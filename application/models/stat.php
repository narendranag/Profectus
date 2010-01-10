<?

class Stat extends Datamapper {
	
	function __construct($id = 1) {
		parent::__construct($id);
	}
	
	function new_invoice_number() {
		return $this->invoice_number + 1;
	}
	
	function post($items_sold, $revenue, $profit = 0.0) 
	{

		$this->invoice_number +=  1;
		$this->revenue +=  $revenue;
		$this->profit +=  $profit;
		$this->items_sold +=  $items_sold;
		
		$this->save();
	}
}
?>