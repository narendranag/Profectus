<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>profectus</title>
	<link rel="stylesheet" href="/public/css/reset.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/grid.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/typography.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/forms.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/profectus.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/jquery.autocomplete.css" type="text/css" media="screen" title="no title" charset="utf-8">


	<script src="/public/js/jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/public/js/jquery.autocomplete.js" type="text/javascript" charset="utf-8"></script>
	
	<script type="text/javascript" charset="utf-8">
	
	// Helper Functions
	
	function selectItem(li, object) {
		
		switch(object)
		{
			case "Vendor" :
				Vendor.id = (li.extra) ? li.extra[0] : 0;
				break;
			
			case "Client" :
				Client.id = (li.extra) ? li.extra[0] : 0;
				break;
			
			case "Inventory" :
				Inventory.id = (li.extra) ? li.extra[0] : 0;
				Invoice.add_item();
				break;

			case "Invoice" :
				Invoice.id = (li.extra) ? li.extra[0] : 0;
				break;
		}
	}
	
	function parse_date(string) {
	    var date = new Date();
	    var parts = String(string).split(/[- :]/);
	    date.setFullYear(parts[0]);
	    date.setMonth(parts[1] - 1);
	    date.setDate(parts[2]);
	    date.setHours(parts[3]);
	    date.setMinutes(parts[4]);
	    date.setSeconds(parts[5]);
	    date.setMilliseconds(0);

	    return date;
	}
	
	function trim(str, chars) {
		return ltrim(rtrim(str, chars), chars);
	}

	function ltrim(str, chars) {
		chars = chars || "\\s";
		return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
	}

	function rtrim(str, chars) {
		chars = chars || "\\s";
		return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
	}
	
	if (! Array.prototype.forEach)
	{
	  Array.prototype.forEach = function(fun /*, thisp*/)
	  {
	    var len = this.length;
	    if (typeof fun != "function")
	      throw new TypeError();

	    var thisp = arguments[1];
	    for (var i = 0; i < len; i++)
	    {
	      if (i in this)
	        fun.call(thisp, this[i], i, this);
	    }
	  };
	}
	
	
	
	// The Vendor Model
	
	
	var Vendor = {
		
		"id" 		: 0,
		"name"		: "",
		"address"	: "",
		"number"	: "",
		"email"		: "",
		
		reset		: function() {
			
			this.id = 0;
			this.name = "";
			this.address = "";
			this.number = "";
			this.email = "";
			
			return true;
		},
		
		get			: function() {
			
			if(this.id > 0)
			{
				$.post(	"<?=site_url('vendors/get')?>",
						{ "id" : this.id },
						function(response) {
							if(response.error)
							{
								alert("Error: getting vendor with id " + this.id);
							}
							else
							{
								Vendor.id = response.id;
								Vendor.name = response.name;
								Vendor.address = response.address;
								Vendor.number = response.number;
								Vendor.email = response.email;
							}
						}, "json"
					);
			}
			else
			{
				alert("Error: No vendor selected, nothing to get");
			}
		},
		
		edit		: function() {
			
			$("#vendor_name").val(this.name);
			$("#vendor_address").val(this.address);
			$("#vendor_number").val(this.number);
			$("#vendor_email").val(this.email);
			
		},
		
		validate	: function() {
		
			var error = false;
			var message = "Error: \n";
		
			if(this.name.length == 0)
			{
				error = true;
				message += "Vendor name left blank\n";
			}
			else if(this.id == 0)
			{
				// Ensure a vendor by this name does not exist
				
				$.post(	"<?=site_url('vendors/get')?>",
						{
							"id"		: this.id,
							"name"		: this.name
						},
						function(response) {
							if(! response.error)
							{
								error = true;
								message += "Vendor by this name already exists.\n";
							}
						}, "json"
					);
			}


			// If error display messages and return false, else return true
			
			if(error)
				alert(message);
			
			return error;

		},
		
		save		: function() {
			
			$.post(	"<?=site_url('vendors/save')?>",
					{
						"id"		: this.id,
						"name"		: this.name,
						"address"	: this.address,
						"number"	: this.number,
						"email"		: this.email
					},
					function(response) {
						this.id = response;
					}
				);
		},
		
		submit		: function() {
			
			this.name = trim($("#vendor_name").val());
			this.address = trim($("#vendor_address").val());
			this.number = trim($("#vendor_number").val());
			this.email = trim($("#vendor_email").val());
			
			var error = this.validate();
			
			if(! error)
				this.save();
		}
	}


	// The Client Model

	var Client = {
		
		"id" 		: 0,
		"name"		: "",
		"address"	: "",
		"number"	: "",
		"email"		: "",
		
		reset		: function() {
			
			this.id = 0;
			this.name = "";
			this.address = "";
			this.number = "";
			this.email = "";
			
			return true;
		},
		
		get			: function() {
			
			if(this.id > 0)
			{
				$.post(	"<?=site_url('clients/get')?>",
						{ "id" : this.id },
						function(response) {
							if(response.error)
							{
								alert("Error: Retrieving client with id " + this.id);
							}
							else
							{
								this.id = response.id;
								this.name = response.name;
								this.address = response.address;
								this.number = response.number;
								this.email = response.email;
							}
						}, "json"
					);
			}
			else
			{
				alert("Error: No client selected, nothing to get");
			}
		},
		
		edit		: function() {
			
			if(this.id > 0)
			{
				this.get();
				$("#save_client").val(" Update ");
				$("#client_message").text("You can now update the client's contact details. Click Update when done.");
			}
			else
			{
				$("#save_client").val(" Add ");
				$("#client_message").text("This is a new client. Ask for their contact number, Click Add when done.");
			}
				
				
			$("#client_address").val(this.address);
			$("#client_number").val(this.number);
			$("#client_email").val(this.email);
			
			$("#edit_client").show();
			
		},
		
		validate	: function() {
		
			var error = false;
			var message = "Error: \n";
		
			if(this.name.length == 0)
			{
				error = true;
				message += "Client name left blank\n";
			}
			
			if(this.number.length == 0)
			{
				error = true;
				message += "Client number left blank\n";
			}
			
			// Ensure a client by this number does not exist
			
			if( (! error) && this.id == 0)
			{
				
				$.post(	"<?=site_url('clients/get')?>",
						{
							"id"		: this.id,
							"number"	: this.number
						},
						function(response) {
							
							if(! response.error)
							{
								error = true;
								message += "Client with this contact number already exists.\n";
								
								this.id = response.id;
								this.name = response.name;
								this.address = response.address;
								this.number = response.number;
								this.email = response.email;
								
								this.edit();
							}
							
						}, "json"
					);
			}


			// If error display messages and return false, else return true
			
			if(error)
				alert(message);
			
			return error;

		},
		
		save		: function() {
			
			
			error = this.validate();
			
			if(! error)
			{
				$.post(	"<?=site_url('clients/save')?>",
						{
							"id"		: this.id,
							"name"		: this.name,
							"address"	: this.address,
							"number"	: this.number,
							"email"		: this.email
						},
						function(response) {
							this.id = response;
							$("#edit_client").hide();
							Invoice.client_id = this.id;
						}
					);
			}
			

		},
		
		submit		: function() {
			
			this.name = trim($("#client_name").val());
			this.address = trim($("#client_address").val());
			this.number = trim($("#client_number").val());
			this.email = trim($("#client_email").val());
			
			var error = this.validate();
			
			if(! error)
				this.save();
		}
	}



	// The Detail Model
	
	var Detail = {
		
		"id"			: 0,
		"inventory_id"	: 0,
		"description"	: "",
		"price"			: 0.0,
		
		reset 			: function() {
			
			this.id 			= 0;
			this.inventory_id	= 0;
			this.description	= "";
			this.price			= 0.0;
		},
		
		
		add				: function() {
			
			
		}
	}


	// The Inventory Model
	
	var Inventory {
		
		"id" 			: 0,
		"vendor_id"		: 0,
		"code"			: "",
		"description"	: "",
		"cost_price"	: 0.0,
		"sale_price"	: 0.0,
		"status"		: 0,
		"invoice_id"	: 0,
		"tr_html"		: "",
		
		reset			: function() {
			
			this.id 			= 0;
			this.vendor_id		= 0;
			this.code			= "";
			this.description	= "";
			this.cost_price		= 0.0;
			this.sale_price		= 0.0;
			this.status			= 0;
			this.invoice_id		= 0;
			this.details		= Array();
			this.tr_html		= "";
			
			return true;
		},
		
		get 			: function() {
			
			var okay = true;
			
			if(this.id > 0)
			{
				$.post(	"<?=site_url('stock/get')?>",
						{ "id": this.id },
						function(response) {
							
							if(! response.error)
							{
								this.id 			= response.id;
								this.vendor_id		= response.vendor_id;
								this.code			= response.code;
								this.description	= response.description;
								this.cost_price		= response.cost_price;
								this.sale_price		= response.sale_price;
								this.status			= response.status;
								this.invoice_id		= response.invoice_id;
								this.tr_html		= response.tr_html;
							}
							else
							{
								okay = false;
								alert("Error fetching data");
							}
							
						}, "json"
					);
			}
			else
			{
				okay = false;
				alert("Don't know what inventory to get.");
			}
			
			return okay;
			
		},
		
		append			: function() {
			
			$("tbody").append(this.tr_html);
			
		},
		
		remove			: function() {

			var class_name = ".inventory_" + this.id;
			$(class_name).remove();
			
			this.reset();
			
		}		
	}
	
	
	
	// The Invoice Model
	
	
	var Invoice = {
		
		"id"			: 0,
		"number"		: 0,
		"client_id"		: 0,
		"date"			: null,
		"total"			: 0.0,
		"discount"		: 0.0,
		"advance"		: 0.0,
		"balance"		: 0.0,
		"seller_id"		: 0,
		"status"		: 0,
		"tbody_html"	: "",
		
		reset			: function() {
			
			this.id 		= 0;
			this.number		= 0;
			this.client_id	= 0;
			this.date		= null;
			this.total		= 0.0;
			this.discount	= 0.0;
			this.advance	= 0.0;
			this.balance	= 0.0;
			this.seller_id	= 0;
			this.status		= 0;
			this.tbody_html = "";
			
			Client.reset();
			Inventory.reset();
		},
		
		retotal			: function() {
			
			this.balance = this.total - this.discount - this.advance;
			
			$("#total").val(this.total);
			if(this.discount > 0)
				$("#discount").val(this.discount);
			$("#advance").val(this.advance);
			$("#balance").val(this.balance);
		},
		
		
		get 			: function() {
		
			var okay = true;
		
			if(Invoice.id > 0)
			{
				$.post(	"<?=site_url('/invoicing/get')?>",
						{ "id" : Invoice.id },
						function(response) {
							
							this.number = response.number;
							this.client_id = response.client_id;
							this.date = parse_date(response.date);
							this.total = response.total;
							this.discount = response.discount;
							this.advance = response.advance;
							this.balance = response.balance;
							this.seller_id = response.seller_id;
							this.status = response.seller_id;
							this.tbody_html = response.tbody_html;
							
							$("tbody").html(this.tbody_html);
							
						}, "json"
					);
			}
			else
			{
				alert("Nothing to get");
				okay = false;
			}
			
			return okay;
			
		},
		
		
		save			: function() {
			
			this.client_id = Client.id;
			
			$.post(	"<?=site_url('/invoicing/get')?>",
					{ 
						"id" 		: this.id,
						"client_id"	: this.client_id,
						"total"		: this.total,
						"discount"	: this.discount,
						"advance"	: this.advance,
						"balance"	: this.balance,
						"status"	: this.status
					},
					function (response)
					{
						this.id = response;
					}
				);
		}
		
		add_item		: function() {
			
			if(Invoice.id == 0)
				Invoice.save();
			
			var okay = Inventory.get();
			
			if(okay)
			{
				$.post(	"<?=site_url('invoicing/add_item')?>",
						{
							"invoice_id"	: Invoice.id,
							"inventory_id"	: Inventory.id
							"sale_price"	: Inventory.sale_price;
						},
						function(response)
						{
							Invoice.total = response;
							Invoice.retotal();
							Inventory.status = 2;
							Inventory.append();
						}
					);
			}
			
			
		},
		
		remove_item		: function(inventory_id)
		{
			
			Inventory.reset();
			Inventory.id = inventory_id;
			
			$.post(	"<?=site_url('invoicing/remove_item')?>",
					{
						"invoice_id"	: Invoice.id,
						"inventory_id"	: Inventory.id
					},
					function(response)
					{
						Invoice.total = response;
						Inventory.remove();
						Invoice.retotal();
					}
				);
		}		
	}
	
	

	$(document).ready(function() {
		
		// Init
		
		if($("#vendor").length)
			$("#vendor").autocomplete("<?=site_url('vendors/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, object:"Vendor"});
		
		if($("#client").length)
		{
			$("#client").autocomplete("<?=site_url('clients/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, object:"Client"});
			
			$("#client").blur( function() {
				Client.edit();
			});
			
			$("#save_client").click( function() {
				Client.save();
			});
		}
			
		
		if($("#inventory").length)
			$("#inventory").autocomplete("<?=site_url('stock/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, object:"Inventory"});
		
		if($("#invoice").length)
			$("#invoice").autocomplete("<?=site_url('invoicing/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, object:"Invoice"});
	
	
	});
		
	</script>
</head>

<body id="stock">
<div class="container">
	
	<div class="header span-24 last">
		<a href="#">profectus</a>
		<div class="right">
		<?=anchor("invoicing", "invoicing");?>
		<strong><?=anchor("stock", "stock");?></strong>
		<a href="javascript:void(0)" onclick="Inventory.add();">add stock</a>
		<a href="javascript:void(0)" onclick="Vendor.add();">add vendor</a>
		<?=anchor("auth/logout", "logout");?>
		</div>
	</div>
	
	<div class="prepend-15 span-9 last prepend-top">
		<h1>Jewelry By Arunima</h1>
	</div>
	
	<div class="prepend-1 span-3 append-1">
		<h2>STOCK</h2>
	</div>
	
	<div class="span-18 append-1 last">
		
		<label for="vendor">Vendor</label><br/><input type="text" name="vendor" value="" id="vendor">
		
		<table>
			<thead>
				<th>Code</th>
				<th>Item</th>
				<th class="amount">Amount</th>
			</thead>
			
			<tbody>

			</tbody>
			
		</table>
	</div>
</div>

<div id="edit_invoice" class="hidden">

	<!-- Choosing / Adding a client -->

	<label for="client">Client</label><br/>
	<input type="text" name="client" value="" id="client"> <span class="small quiet">Enter the client's name</span>

	<div id="edit_client" class="hidden">
		<p id="client_message" class="notice">You can now update the client's contact details. Click Update when done.</p>
		
		<label for="client_address">Address</label><br/>
		<input type="text" name="client_address" value="" id="client_address"><br/>
	
		<label for="client_number">Contact Number *</label><br/>
		<input type="text" name="client_number" value="" id="client_number"><br/>

		<label for="client_email">Email</label><br/>
		<input type="text" name="client_email" value="" id="client_email"><br/>
		
		<input type="button" name="save_client" value=" Add " id="save_client">

	</div>
		
	<label for="inventory">Inventory</label><br/>
	<input type="text" name="inventory" value="" id="inventory"><span class="small quiet">Enter the code (V2 999)</span>
	
</div>

</body>