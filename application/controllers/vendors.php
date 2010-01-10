<?

class Vendors extends Controller {
	
	function __construct() {
		
		parent::__construct();
		
		if(! $this->dx_auth->is_logged_in())
			redirect("auth/login");
		
	}
	
	function search($name)
	{
		if (isset($name) && strlen($name) > 2)
		{
			$vendorObject = new Vendor();
			$vendorObject->like('name', $name, "after")->get();
			 
			foreach ($vendorObject->all as $vendor)
			    echo "$vendor->name|$vendor->id\n";			

    	} // end if
		
	} // end search()
	
	
	
	function get()
	{
		
		$id = $this->input->post("id");
		
		if($id == 0)
		{
			$name = $this->input->post("name", TRUE);
			$vendorObject = new Vendor;
			$vendorObject->get_by_name($name);
			if($vendorObject->exists())
			{
				$data = $vendorObject->to_array();
				$data["error"] = FALSE;
			}
			else
				$data["error"] = TRUE;
		}
		else
		{
			$vendorObject = new Vendor($id);
			
			if(! $vendorObject->exists())
				$data["error"] = TRUE;
			else
			{
				$data = $vendorObject->to_array();
				$data["error"] = FALSE;
			}
				
		}
		
		echo json_encode($data);
	}
	
	
	
	function save()
	{		
		$id = $this->input->post("id");
		
		if($id == 0)
			$vendorObject = new Vendor;
		else
			$vendorObject = new Vendor($id);

		$vendorObject->name = $vendorObject->input->post("name", TRUE);
		$vendorObject->address = $vendorObject->input->post("address", TRUE);
		$vendorObject->number = $vendorObject->input->post("number", TRUE);
		$vendorObject->email = $vendorObject->input->post("email", TRUE);

		$vendorObject->save();

		echo $vendorObject->id;
	}
}
?>