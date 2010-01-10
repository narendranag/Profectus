<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>profectus | invoice</title>
	
	<!--
		STYLESHEETS
	-->
	
	<link rel="stylesheet" href="/public/css/reset.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/grid.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/typography.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/forms.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/profectus.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="/public/css/jquery.autocomplete.css" type="text/css" media="screen" title="no title" charset="utf-8">

	<!--
	
		JAVASCRIPT
		
	-->

	<script src="/public/js/jquery-1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/public/js/jquery.autocomplete.js" type="text/javascript" charset="utf-8"></script>
	<script src="/public/js/jquery.hotkeys-0.7.9.min.js" type="text/javascript" charset="utf-8"></script>
	
	<script type="text/javascript" charset="utf-8">
	
	// Helper Functions
	
	function selectItem(li, elementID) {		
		$("#"+elementID).val(0);
		var setVal = (li.extra) ? li.extra[0] : 0;
		$("#"+elementID).val(setVal);
		
	}
	
	function parse_date(string) {
	    var date = new Date();
	    var parts = String(string).split(/[- :]/);
	    date.setFullYear(parts[0]);
	    date.setMonth(parts[1] - 1);
	    date.setDate(parts[2]);
	
		if(trim(parts[5]) == "pm")
		{
			if(parts[3] != 12)
				parts[3] += 12;
		}
		else
		{
			if(parts[3] == 12)
				parts[3] = 0;
 		}
			
				
	    date.setHours(parts[3]);
	    date.setMinutes(parts[4]);
	    date.setSeconds(0);
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
			
			this.id = $("#vendor_id").val();
			
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
			
			this.name = trim($("#vendor_name").val());
			this.address = trim($("#vendor_address").val());
			this.number = trim($("#vendor_number").val());
			this.email = trim($("#vendor_email").val());
			
			var error = this.validate();
			
			if(! error)
			{
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
			}

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
			
			this.id = $("#client_id").val();
			
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
								
								$("#client-name").text(this.name);
								$("#client-number").text(this.number);
								$("#client_name").val(this.name);
								$("#client_address").val(this.address);
								$("#client_number").val(this.number);
								$("#client_email").val(this.email);
								
							}
						}, "json"
					);
			}
		},
		
		edit		: function() {
			
			if(this.id > 0)
			{
				$("#save_client").val(" Update ");
				$("#client_message").text("You can now update the client's contact details. Click Update when done.");
			}
			else
			{
				this.name = trim($("#client").val());
				
				$("#save_client").val(" Add ");
				$("#client_message").text("This is a new client. Ask for their contact number, Click Add when done.");
			}
				
			$("#client_name").val(this.name);	
			$("#client_address").val(this.address);
			$("#client_number").val(this.number);
			$("#client_email").val(this.email);
			
			$("#edit_client").show();
			
			$("#client_address").focus();
			
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
			
			this.name = trim($("#client_name").val());
			this.address = trim($("#client_address").val());
			this.number = trim($("#client_number").val());
			this.email = trim($("#client_email").val());
			
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
							$("#client-name").text(this.name);
							$("#client-number").text(this.number);
						}
					);
			}
		}
	}


	// The Inventory Model
	
	var Inventory = {
		
		"id" 			: 0,
		"vendor_id"		: 0,
		"code"			: "",
		"description"	: "",
		"sale_price"	: 0.0,
		"status"		: 0,
		"invoice_id"	: 0,
		"tr_html"		: "",
		
		reset			: function() {
			
			$("#inventory_id").val(0);
			$("#inventory").val("");
			
			this.id 			= 0;
			this.vendor_id		= 0;
			this.code			= "";
			this.description	= "";
			this.sale_price		= 0.0;
			this.status			= 0;
			this.invoice_id		= 0;
			this.tr_html		= "";
			
			return true;
		},
		
		get 			: function() {
			
			var okay = true;
			
			this.id = $("#inventory_id").val();
			
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
			
			Vendor.reset();
			Client.reset();
			Inventory.reset();
		},
		
		retotal			: function() {
			
			this.balance = this.total - this.discount - this.advance;
			
			$("#total").val(this.total);
			if(this.discount > 0)
			{
				$("#discount").val(this.discount);
				$("#discount_tr").show();
			}
			else
			{
				$("#discount_tr").hide();
			}
				
			$("#advance").val(this.advance);
			$("#balance").val(this.balance);
		},
		
		
		get 			: function() {
		
			this.id = trim($("#invoice_id").val());
		
			var okay = true;
		
			if(Invoice.id.length > 0)
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
							
							Client.id = this.client_id;
							Client.get();
							
							$("#invoice-number").text(this.number);
							$("#invoice-date").text(this.date.toDateString());
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
			
			$.ajax({	
					type: "POST",
					async: false,
					
					url: "<?=site_url('/invoicing/save')?>",
					
					data: { 
						"id" 		: this.id,
						"client_id"	: this.client_id,
						"total"		: this.total,
						"discount"	: this.discount,
						"advance"	: this.advance,
						"balance"	: this.balance,
						"status"	: this.status
					},
					
					success:  function (response)
					{
						Invoice.id = response;
						/*
						Invoice.number = response.number;
						Invoice.tbody_html = response.tbody_html;
						
						$("tbody").html(Invoice.tbody_html);
						$("#invoice-number").text(Invoice.number);
						*/
						
					}
				});
		},
		
		add_item		: function() {
						
			var okay = Inventory.get();
						
			if(okay)
			{
				if(this.id == 0)
					Invoice.save();
			
				$.post(	"<?=site_url('invoicing/add_item')?>",
						{
							"invoice_id"	: Invoice.id,
							"inventory_id"	: Inventory.id,
							"sale_price"	: Inventory.sale_price
						},
						function(response)
						{
							
							Invoice.total = response.total;
							Invoice.tbody_html = response.tbody_html
							
							$("tbody").html(Invoice.tbody_html);
							Invoice.retotal();
							Inventory.reset();
							
						}, "json"
					);
			}
			
			
		},
		
		remove_item		: function(inventory_id) {
			
			Inventory.reset();
			Inventory.id = inventory_id;
			
			$.post(	"<?=site_url('invoicing/remove_item')?>",
					{
						"invoice_id"	: Invoice.id,
						"inventory_id"	: Inventory.id
					},
					function(response)
					{
						Invoice.total = response.total;
						Invoice.tbody_html = response.tbody_html;
						$("tbody").html(response.tbody_html);
					}
				);
		}		

	}
	

	// Wizard Stages
	
	var Stage = {
		
		toggle: function() {
			var text = ($("#interaction-page-toggle-button").html() == "show pane") ? "hide pane" : "show pane";
			$("#interaction-page-toggle-button").html(text);
			$("#interaction-pane").toggle("slow");
		},
		
		reset: function() {
			$("input").val("");
			this.one();
		},
		
		one	: function () {
			$("#stage-1").hide();
			$("#stage-2").hide();
			$("#stage-3").hide();
			$("#discount-edit").hide();
			$("#stage-1").show();
		},
		
		two : function() {
			$("#stage-1").hide();
			$("#stage-2").hide();
			$("#stage-3").hide();
			$("#discount-edit").hide();
			Client.edit();
			$("#stage-2").show();
		},
		
		three : function() {
			
			Invoice.client_id = Client.id;
			Invoice.save();
			
			$("#stage-1").hide();
			$("#stage-2").hide();
			$("#stage-3").hide();
			$("#discount-edit").hide();
			$("#stage-3").show();
		},

		discount: function() {
			$("#stage-1").hide();
			$("#stage-2").hide();
			$("#stage-3").hide();
			$("#discount-edit").show();
		}
	}

	// On DOM Load
	
	$(document).ready(function() {
		
		// Init
		
		$("#vendor").autocomplete("<?=site_url('vendors/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, elementID:"vendor_id"});		
		$("#client").autocomplete("<?=site_url('clients/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, elementID:"client_id"});
		$("#inventory").autocomplete("<?=site_url('stock/search')?>", { delay:100, minChars:2, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, elementID:"inventory_id"});
		$("#invoice_number").autocomplete("<?=site_url('invoicing/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, elementID:"invoice_id"});
		
		$("#interaction-page-toggle-button").click( Stage.toggle );
		$(document).bind('keydown', 'alt+shift+d', Stage.discount );
		
		Stage.one();
		
		// Stages 
		
		// One
		
		$("#choose_client").click( function() {
			Client.get();
			Stage.two();
		});
		
				
		$("#get_invoice").click( function() {
			var success = Invoice.get();
			if(success)
				Stage.two();
		});
		
		
		// Two
		
		$("#save_client").click( function() {
			Client.save();
			Stage.three();
		});
		
		$("#next").click( Stage.three );
		
		
		// Three 
		
		$("#choose_inventory").click( function() {
			Invoice.add_item();
		});
		
		
		// Discount
		
		$("#calculate_discount").click( function() {
			var discount_percentage = $("#discount_amount").val();
			Invoice.discount = Invoice.total * (discount_percentage / 100);
			Invoice.retotal();  
		});
		
		$("#give_discount").click( function() {
			var discount_percentage = $("#discount_amount").val();
			Invoice.discount = Invoice.total * (discount_percentage / 100);
			Invoice.retotal();
			
			Stage.three();
		});
		
		// Finish
		
		$("#done").click( function() {
			Invoice.save();
			Invoice.reset();
			Stage.reset();
		});
		
	});
		
	</script>

</head>

<body id="invoice-body"><div class="container">

<div class="span-9">

	<div class="prepend-top left-shift append-bottom">PROFECTUS</div>

	<div class="menu selected">BILLING</div>

	<div class="interface">
		<div id="stage-1">
			<p class="quiet">Enter the name of the client you want to create an invoice for or type in the invoice number you want to load.</p>
			<label for="client" >
				Choose Client
			</label><br/>
			<input type="hidden" name="client_id" value="0" id="client_id">
			<input type="text" name="client" value="" id="client">
			<input type="button" name="choose_client" value=" Go " id="choose_client">
			
			<hr class="space" />
			<p class="quiet">OR</p>

			<input type="hidden" name="invoice_id" value="" id="invoice_id">
			<label for="invoice_number">Invoice Number</label><br/><input type="text" name="invoice_number" value="" id="invoice_number">
			<input type="button" name="get_invoice" value=" Load Invoice " id="get_invoice">
		</div>

		<div id="stage-2" class="hide">
			<p id="client_message" class="quiet">You can now update the client's contact details. <br/>Click <span class="loud">Update</span> when done, or click <span class="loud">Next</span> to start filling in invoice details.</p>

			<div class="span-4">
			<label for="client_address">Name</label><br/>
			<input type="text" name="client_name" value="" id="client_name"><br/>	
			</div>

			<div class="span-4">
			<label for="client_address">Address</label><br/>
			<input type="text" name="client_address" value="" id="client_address"><br/>
			</div>

			<div class="span-4">
			<label for="client_number">Contact Number *</label><br/>
			<input type="text" name="client_number" value="" id="client_number"><br/>
			</div>

			<div class="span-4">
			<label for="client_email" >Email Address</label><br/>
			<input type="text" name="client_email" value="" id="client_email"><br/>
			</div>

			<div class="clear"></div>

			<input type="button" name="save_client" value=" Add " id="save_client">
			<input type="button" name="next" value=" Next " id="next">
		</div>

		<div id="stage-3" class="hide">
			<p class="quiet">Enter item code as written on tag (JBA-1).</p>

			<label for="inventory">Add Item</label><br/>
			<input type="hidden" name="inventory_id" value="0" id="inventory_id">
			<input type="text" name="inventory" value="" id="inventory">
			<input type="button" name="choose_inventory" value=" Add " id="choose_inventory">
		</div>

		<div id="discount-edit" class="hide">
			<p class="quiet">Enter discount percentage: 1-5. Do not input the percentage sign.</p>

			<label for="discount_percentage">Discount Percentage</label><br/>
			<input type="text" name="discount_amount" value="0.0" id="discount_amount"><br/>
			<input type="button" name="calculate_discount" value=" Calculate Discount " id="calculate_discount">
			<input type="button" name="give_discount" value=" Give Discount " id="give_discount">
		</div>


	</div>

	<div class="interface-menu">
		<a href="javscript:Stage.print()">PRINT</a> or <a href="javascript:Stage.restart()">Start Over</a>
	</div>

	<div class="menu disabled">STOCK</div>
	<div class="menu unselected">CLIENTS</div>
	<div class="menu unselected">VENDORS</div>
	<div class="menu unselected">REPORTS</div>
	<div class="menu unselected">LOGOUT</div>
</div>

<div class="span-15 last">
	<div id="display">
		<div class="prepend-top">
			<h1>Jewelry By Arunima</h1>
			<h3>INVOICE</h3>
		</div>

		<div class="span-3">
			<span id="invoice-number"></span><br/>
			<span id="tax-number">TIN: 08354051856</span><br/>
		</div>

		<div class="span-11 last">
			<span id="invoice-date"></span><br/>
			Name: <strong><span id="client-name">&nbsp; </span></strong><br/>
			Phone Number: <span class="small" id="client-number">&nbsp;</span>
		</div>
		
		<hr class="space" />
		
		<div class="span-3">
			<p class="small">
				B-129 Sector 14,<br/>
				<strong>Noida</strong> 201301<br/>
				458 Nemi Sagar Colony,<br/>
				<strong>Jaipur</strong> 302021
			</p>
			<p class="small">
				<strong>T</strong> +91 99107 77092<br/>
				<strong>F</strong> +91 120 4359434<br/>
				<strong>E</strong> sales@jewelrybyarunima.com<br/>
				<strong>W</strong> jewelrybyarunima.com
			</p>
		</div>

		<div class="span-11 last">

			<table>
				<thead>
					<th>Code</th>
					<th>Item</th>
					<th class="amount">Amount</th>
				</thead>

				<tbody>
					<tr>
						<td></td>
						<td></td>
						<td></td>
				</tbody>

			</table>
		</div>
	</div>
</div>

</div></body>