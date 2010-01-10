<?

class Stock extends Controller {
	
	function __construct() {
		
		parent::__construct();
		
		if(! $this->dx_auth->is_logged_in())
			redirect("auth/login");
		
	}
	
	
	function index($offset = 0) 
	{
	
		$inventoryObject = new Inventory;
		
		$inventoryObject->limit($offset, 20)->get();
		
		$data = $inventoryObject->all_to_array();
		
		$this->load->view("stock", $data);
	}
	
	function add()
	{
		$this->load->view("stock/add");
	}
	
	function import()
	{
		if($_FILE)
		{
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'xls|csv';
			$config['max_size']	= '4096';

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload())
			{
				$data["error"] = $this->upload->display_errors();
			}	
			else
			{
				$file = $this->upload->data();
				
				$this->_parse($file);
			}
		}
		else
			$this->load->view("stock/import");
	}
	
	
	// These are all AJAX methods
	
	function search($code)
	{
		if (isset($code) && strlen($code) > 1)
		{
			$inventoryObject = new Inventory();
			$inventoryObject->like('code', $code)->where("status", 1)->get();
			 
			foreach ($inventoryObject->all as $inventory)
			    echo "$inventory->code|$inventory->id\n";
    	} // end if
		
	} // end search()
	
	
	
	
	
	function get() {
		
		$id = $this->input->post("id");
		
		if($id == 0)
			$data["error"] = TRUE;
		else
		{
			$inventoryObject = new Inventory($id);
			
			if(! $inventoryObject->exists())
				$data["error"] = TRUE;
			else
			{
				$data = $inventoryObject->to_array();
				$data["details"] = $inventoryObject->detail->get()->all_to_array();
				$data["tr_html"] = $this->_html($inventoryObject);
				$data["error"] = FALSE;
			}
		}
				
		echo json_encode($data);
	}
	
	
	
	
	function save() {
		
		$id = $this->input->post("id");
		
		if($id == 0)
			$inventoryObject = new Inventory;
		else
			$inventoryObject = new Inventory($id);
		
		$inventoryObject->vendor_id = $this->input->post("vendor_id");
		$inventoryObject->code = $this->input->post("code");
		$inventoryObject->description = $this->input->post("description");
		$inventoryObject->cost_price = $this->input->post("cost_price");
		$inventoryObject->sale_price = $this->input->post("sale_price");
		$inventoryObject->status = $this->input->post("status");
		
		$inventoryObject->save();
		
		echo $inventoryObject->id;
	}
	
	
	
	function save_detail() {
		
		$id = $this->input->post("id");
		
		if($id == 0)
			$detailObject = new Detail;
		else
			$detailObject = new Detail($id);
				
		$detailObject->inventory_id = $this->input->post("inventory_id");
		$detailObject->description = $this->input->post("description");
		$detailObject->price = $this->input->post("price");
		
		$detailObject->save();
		
		echo $detailObject->id;
	}
	
	
	/*
		These are all private methods
	*/
	
	
	function _html($inventoryObject)
	{
		$tr_html = "<tr class='inventory_$inventoryObject->id'>";
		
		$tr_html .= "	<td class=\"topborder\"><a href='javascript:void(0);' onclick='Invoice.remove_item($inventoryObject->id)'><img src='/public/css/icons/cross.png'></a>$inventoryObject->code</td>";
		$tr_html .= "	<td class=\"topborder\">$inventoryObject->description</td>";
		$tr_html .= "	<td class=\"amount topborder\">Rs. $inventoryObject->sale_price</td>";
		$tr_html .= "</tr>";
		
		if($inventoryObject->detail->exists())
		{
			foreach($inventoryObject->detail->all as $detail)
			{
				$tr_html .= "<tr id='detail_$detail->id' class='inventory_$inventoryObject->id'>";
				$tr_html .= "	<td class=\"details\"></td>";
				$tr_html .= "	<td class=\"details small\">$detail->description</td>";
				$tr_html .= "	<td class=\"small amount\">Rs. $detail->price</td>";
				$tr_html .= "</tr>";
			}
		}
		
		return($tr_html);
	}
	
	
	function _parse($file, $template = 1) 
	{
		
		switch($template)
		{
			case 1 :
				$serial_number = 0;
				$type = 1;
				$code = 2;
				$number = 3;
				$description = 4;
				break;
			
		}
		
		$this->load->library("cvsreader");
		
		$filedata = $this->csvreader->parse_file($file["full_path"]);
		
		$inventoryObject = new Inventory;
		
		$rownumber = 0;
		
		foreach ($filedata as $row) 
		{	
			foreach ($row as $csvstring) 
			{
				$elements = array_fill(0,5,"");
				
				$elements = $this->csvreader->get_csv_values($csvstring);
				
				if( strlen(trim($elements[$code])) > 0)
				{

					$rownumber = $rownumber + 1; // Just a quick check on the number of rows being added
					
					$inventoryObject->vendor_id = $elements[$vendor_id];
					$inventoryObject->type = ucwords(strtolower(trim($elements[$type])));
					$inventoryObject->code = strtoupper(trim($elements[$code]));
					$inventoryObject->sale_price = $elements[$number] + 100;
					$inventoryObject->cost_price = .45 * $inventoryObject->sale_price;
					$inventoryObject->description = $elements[$description];
					$inventoryObject->status = 1;
					
					$inventoryObject->save();
					
					
				}

				$inventoryObject->clear();
			}
		}
	}
}
?>