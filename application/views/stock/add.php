<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>profectus | stock add</title>
	
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
								
								$("#vendor-name").text(this.name);
								$("#vendor-number").text(this.number);
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
							$("#vendor-name").text(this.name);
							$("#vendor-number").text(this.number);
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
			this.vendor_id		= Vendor.id;
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
		
		validate		: function() {
			
		}
		
		save 			: function() {
			this.vendor_id = Vendor.id;
			this.code = trim($("#code").val());
			this.description = trim($("#description").val());
			this.cost_price = trim($("#cost_price").val());
			this.sale_price = trim($("#sale_price").val());
			
			var error = this.validate();
			
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

	// The Wizard Manager
	
	var Stage = {
		
		toggle: function() {
			var text = ($("#button").html() == "show pane") ? "hide pane" : "show pane";
			$("#button").html(text);
			$("#interaction-pane").toggle("slow");
		},
		
		reset: function() {
			$("input").val("");
			this.one();
		},
		
		one	: function () {
			$("#stage-2").hide();
			$("#stage-3").hide();
			$("#discount").hide();
			$("#stage-1").show();
		},
		
		two : function() {
			$("#stage-1").hide();
			$("#stage-3").hide();
			$("#details").hide();
			Vendor.edit();
			$("#stage-2").show();
		},
		
		three : function() {
			$("#stage-1").hide();
			$("#stage-2").hide();
			$("#details").hide();
			$("#stage-3").show();
		},

		discount: function() {
			$("#stage-1").hide();
			$("#stage-2").hide();
			$("#stage-3").hide();
			$("#detail").show();
		}
	}

	// On DOM Load
	
	$(document).ready(function() {
		
		// Init
		
		$("#vendor").autocomplete("<?=site_url('vendors/search')?>", { delay:100, minChars:3, matchSubset:1, matchContains:1, cacheLength:10, onItemSelect:selectItem, selectOnly:1, elementID:"vendor_id"});		
		$("#interaction-page-toggle-button").click( Stage.toggle );
		$(document).bind('keydown', 'alt+shift+d', Stage.details );
	
	});
		
	</script>

</head>

<body id="upload-file-body">

<div class="container showgrid">

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
	
	<div id="interaction-pane" class="prepend-1 span-22 append-1 last hide">
	<div class="wizard">
		
		<div id="stage-1">
			<p class="quiet">Enter the name of the vendor you want to add stock for.</p>
			<label for="vendor" >
				Choose Vendor
			</label><br/>
			<input type="hidden" name="vendor_id" value="0" id="vendor_id">
			<input type="text" name="vendor" value="" id="vendor">
			<input type="button" name="choose_vendor" value=" Go " id="choose_vendor">
		</div>
		
		<div id="stage-2" class="hide">
			<p id="vendor_message" class="quiet">You can now update the vendor's contact details. <br/>Click <span class="loud">Update</span> when done, or click <span class="loud">Next</span> to start adding stock.</p>
			
			<div class="span-4">
			<label for="vendor_address">Address</label><br/>
			<input type="text" name="vendor_address" value="" id="vendor_address"><br/>
			</div>

			<div class="span-4">
			<label for="vendor_number">Contact Number *</label><br/>
			<input type="text" name="vendor_number" value="" id="vendor_number"><br/>
			</div>
			
			<div class="span-4">
			<label for="vendor_email" >Email Address</label><br/>
			<input type="text" name="vendor_email" value="" id="vendor_email"><br/>
			</div>
			
			<div class="clear"></div>
			
			<input type="button" name="save_vendor" value=" Add " id="save_vendor">
			<input type="button" name="next" value=" Next " id="next">
		</div>
		
		<div id="stage-3" class="hide">
			<p class="quiet">Add new product. Hit tab to go from one field to the next. Hit Enter to save product or alt-shift-d to add details. </p>
			
			<form action="#" method="get" accept-charset="utf-8">
				<div class="span-2">
					<label for="code">Code</label><br />
					<input type="text" name="code" value="" id="code" class="span-2">
				</div>
				<div class="span-7">
					<label for="description">Description</label><br />
					<input type="text" name="description" value="" id="description" class="span-7">
				</div>
				<div class="span-3">
					<label for="cost_price">Cost Price</label><br/>
					<input type="text" name="cost_price" value="" id="cost_price" class="span-3">
				</div>
				<div class="span-3">
					<label for="sale_price">Sale Price</label><br/>
					<input type="text" name="sale_price" value="" id="sale_price" class="span-3">
				</div>
				<div class="span-6 last">
					<br />
					<input type="submit" name="save_item" value=" Save " id="save_item">
					<input type="button" name="add_details" value=" Save and add Details " id="add_details">
					<input type="button" name="reset" value=" Reset " id="reset">
				</div>
			</form>
		</div>
		
		<div id="details" class="hide">
			<p class="quiet">Add details for this product</p>
			<div class="span-7">
				<label for="detail_description">Detail</label><br/>
				<input type="text" name="detail_description" value="" id="detail_description" class="span-7">
			</div>
			<div class="span-3">
				<label for="detail_price">Price</label><br/>
				<input type="text" name="detail_price" value="" id="detail_price" class="span-3">
			</div>
		</div>
		
		<div class="clear"></div>
		
		<div class="prepend-15">
			<input type="button" name="done" value=" Save and Close " id="done">
		</div>
		
	</div>
	</div>
	
	<div id="interaction-page-toggle-button" class="push-19 span-3 append-2 append-bottom last">show pane</div>

	<div class="prepend-top prepend-14 span-9 append-1 last">
		<h1>Jewelry By Arunima</h1>
	</div>

	<div class="prepend-1 span-3 append-1">
		<h2>STOCK</h2>
	</div>

	<div class="span-17 append-1 last">
		<h3 id="vendor-name">&nbsp;</h3>
		<span class="small" id="vendor-number">&nbsp;</span>
	</div>

	<div class="prepend-top prepend-1 span-4">
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

	<div class="prepend-top span-18 append-1 last">

		<table>
			<thead>
				<th>Code</th>
				<th>Item</th>
				<th class="amount">Sale Price</th>
			</thead>

			<tbody>

			</tbody>

		</table>
	</div>
</div>

</body>
				
				
					