<?

class Invoicing extends Controller {
	
	
	function __construct() {
		
		parent::__construct();
		
		if(! $this->dx_auth->is_logged_in())
			redirect("auth/login");
		
	}
	
	
	function index()
	{
		
		$this->load->view("invoice");
	}
	
	
	
	function search($number)
	{
		if (isset($number) && strlen($number) > 2)
		{
			$invoiceObject = new Invoice();
			$invoiceObject->like('number', $number, "after")->get();
			 
			foreach ($invoiceObject->all as $invoice)
			    echo "$invoice->number|$invoice->id\n";			

    	} // end if
		
	} // end search()
	
	
	function get() {
		
		$id = $this->input->post("id");
		
		if($id == 0)
			$data["error"] = TRUE;
		else
		{
			$invoiceObject = new Invoice($id);
			$data = $invoiceObject->to_array();
			$data["tbody_html"] = $this->_html($invoiceObject);
			$data["error"] = FALSE;
		}

		echo json_encode($data);
	}
	
	
	function save() 
	{
		
		$id = $this->input->post("id");
		
		if($id > 0)
		{
			$invoiceObject = new Invoice($id);
		}
		else 
		{
			$invoiceObject = new Invoice;
			$invoiceObject->date = date("Y-m-d H:i:s", now());
			$invoiceObject->seller_id = $this->dx_auth->get_user_id();

			$invoiceObject->save();
			
			$invoiceObject->number = date("Ymd", strtotime($invoiceObject->date)) . "-" . $invoiceObject->id;
		}

		$invoiceObject->client_id = $this->input->post("client_id");
		$invoiceObject->total = $this->input->post("total", TRUE);
		$invoiceObject->discount = $this->input->post("discount", TRUE);
		$invoiceObject->advance = $this->input->post("advance", TRUE);
		$invoiceObject->balance = $this->input->post("balance", TRUE);
		$invoiceObject->status = $this->input->post("status", TRUE);
		
		$invoiceObject->save();
		
		echo $invoiceObject->id;
	}
	

	function add_item() {
		
		$invoice_id = $this->input->post("invoice_id");
		$inventory_id = $this->input->post("inventory_id");
		
		$inventoryObject = new Inventory($inventory_id);
		
		$inventoryObject->invoice_id = $invoice_id;
		$inventoryObject->status = 2;				// Sold
		$inventoryObject->save();
		
		
		$invoiceObject = new Invoice($invoice_id);
		$invoiceObject->total += $inventoryObject->sale_price;
		$invoiceObject->save();
		
		$data["total"] = $invoiceObject->total;
		$data["tbody_html"] = $this->_html($invoiceObject);
		
		echo json_encode($data);
	}
	
	function remove_item() {
		
		$invoice_id = $this->input->post("invoice_id");
		$inventory_id = $this->input->post("inventory_id");
		
		$inventoryObject = new Inventory($inventory_id);
		
		$inventoryObject->invoice_id = NULL;
		$inventoryObject->status = 1;				// In Stock
		$inventoryObject->save();
		
		
		$invoiceObject = new Invoice($invoice_id);
		$invoiceObject->total -= $inventoryObject->sale_price;
		$invoiceObject->save();
		
		$data["total"] = $invoiceObject->total;
		$data["tbody_html"] = $this->_html($invoiceObject);
		
		echo json_encode($data);
	}
	
	
	
	
	
	
	
	function _html($invoiceObject)
	{
		
		$tbody_html = "";
		
		foreach($invoiceObject->inventory->get()->all as $inventoryObject)
		{
			$tbody_html .= "<tr class='inventory_$inventoryObject->id'>";

			$tbody_html .= "	<td class=\"topborder\"><a href='javascript:void(0);' onclick='Invoice.remove_item($inventoryObject->id)'><img src='/public/css/icons/cross.png'></a>$inventoryObject->code</td>";
			$tbody_html .= "	<td class=\"topborder\">$inventoryObject->description</td>";
			$tbody_html .= "	<td class=\"amount topborder\">Rs. $inventoryObject->sale_price</td>";
			$tbody_html .= "</tr>";
			
			foreach($inventoryObject->detail->get()->all as $detail)
			{
				$tbody_html .= "<tr id='detail_$detail->id' class='inventory_$inventoryObject->id'>";
				$tbody_html .= "	<td class=\"details\"></td>";
				$tbody_html .= "	<td class=\"details small\">$detail->description</td>";
				$tbody_html .= "	<td class=\"small amount\">Rs. $detail->price</td>";
				$tbody_html .= "</tr>";
			}
		}
		
		$tbody_html .= "<tr>";
		$tbody_html .= "	<td colspan=2 class=\"summary\">Total</td>";
		$tbody_html .= "	<td id=\"total\" class=\"amount summary\">$invoiceObject->total</td>";
		$tbody_html .= "</tr>";
		
		$class_name = ($invoiceObject->discount > 0) ? "" : "hide";
		$tbody_html .= "<tr id=\"discount_tr\" class=\"$class_name\">";
		$tbody_html .= "	<td colspan=2 class=\"summary\">Discount</td>";
		$tbody_html .= "	<td id=\"discount\" class=\"amount summary\">$invoiceObject->discount</td>";
		$tbody_html .= "</tr>";
		
		$tbody_html .= "<tr>";
		$tbody_html .= "	<td colspan=2 class=\"summary\">Advance</td>";
		$tbody_html .= "	<td id=\"advance\" class=\"amount summary\">$invoiceObject->advance</td>";
		$tbody_html .= "</tr>";
		
		$tbody_html .= "<tr>";
		$tbody_html .= "	<td colspan=2 class=\"summary\">Balance</td>";
		$tbody_html .= "	<td id=\"balance\" class=\"amount summary\">$invoiceObject->balance</td>";
		$tbody_html .= "</tr>";
		
		return($tbody_html);
	}
	
}
?>