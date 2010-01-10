<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>profectus | stock import</title>
	
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
		
		append			: function() {
			
			$("tbody").append(this.tr_html);
			this.reset();
			
		},
		
		remove			: function() {

			var class_name = ".inventory_" + this.id;
			$(class_name).remove();
			this.reset();
			
		}		
	}
	
	</script>

</head>

<body id="upload-file-body">

<div class="container">

	<div class="header span-24 last">
		<span>profectus</span>
		<div class="right">
		<a href="/index.php/invoicing">invoicing</a>
		<a href="/index.php/stock">stock</a>
		<a href="/index.php/stock/import">import stock</a>
		<a href="/index.php/reports">reports<a/>
		<a href="auth/logout">logout</a>
		</div>
	</div>

	<div class="prepend-top prepend-8 span-8 append-8 box last">
		<?php if (isset($error)) echo "<div class='error'>" . $error . "</div>";?>
		<?php echo form_open_multipart('stock/import');?>
		<input type="file" name="userfile" size="20" />
		<input type="submit" value="upload" />
		</form>
	</div>

</div>