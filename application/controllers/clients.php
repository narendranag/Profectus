<?

class Clients extends Controller {
	
	function __construct() 
	{
		
		parent::__construct();
		
		if(! $this->dx_auth->is_logged_in())
			redirect("auth/login");	
	}
	
	
	
	function search($name)
	{
		if (isset($name) && strlen($name) > 2)
		{
			$clientObject = new Client();
			$clientObject->like('name', $name, "after")->get();
			 
			foreach ($clientObject->all as $client)
			    echo "$client->name|$client->id\n";			

    	} // end if
		
	} // end search()
	
	
	
	function get()
	{
		$id = $this->input->post("id");
		
		if($id > 0)
		{
			$clientObject = new Client($id);
			
			if(! $clientObject->exists())
			{
				$data["error"] = TRUE;
				$data["message"] = "Couldn't find in DB";
			}
				
			else
			{
				$data = $clientObject->to_array();
				$data["error"] = FALSE;
			}
				
		}
		else
		{
			$data["error"] = TRUE;
			$data["message"] = "ID not greater than 0";
		}
			
		
		echo json_encode($data);
	}
	
	
	
	function save()
	{		
		$id = $this->input->post("id");
		
		if($id == 0)
			$clientObject = new Client;
		else
			$clientObject = new Client($id);

		$clientObject->name = $this->input->post("name", TRUE);
		$clientObject->address = $this->input->post("address", TRUE);
		$clientObject->number = $this->input->post("number", TRUE);
		$clientObject->email = $this->input->post("email", TRUE);

		$clientObject->save();

		echo $clientObject->id;
		
	}
}
?>