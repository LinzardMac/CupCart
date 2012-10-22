<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#details">Product Details</a></li>
    <li><a href="#images">Images</a></li>
    <li><a href="#attributes">Attributes</a></li>
    <li><a href="#discounts">Discounts</a></li>
</ul>
 
<div class="tab-content">
    <div class="tab-pane active" id="details">
    
	<form>
	    <h5>Global Details</h5>
	    
	    <div class="row-fluid">
		<div class="span4">
		    <label for="details_name">Name:</label>
		    <input type="text" name="details_name" value="" />
		    <label for="details_sku">SKU:</label>
		    <input type="text" name="details_sku" value="" />
		    <label for="details_warehouse">Warehouse Location:</label>
		    <select name="details_warehouse">
			<option>Charolette, NC</option>
		    </select>
		    <label for="details_manufacturer">Manufacturer:</label>
		    <select name="details_manufacturer">
			<option>American Apparel</option>
		    </select>
		</div>
		<div class="span4">
		    <label for="details_stockqty">Quantity In Stock:</label>
		    <input type="text" name="details_stockqty" value="" />
		    <label for="details_wholesaleprice">Wholesale Price:</label>
		    <div class="input-prepend">
			<span class="add-on">$</span><input type="text" name="details_wholesaleprice" value="" />
		    </div>
		    <label for="details_taxrule">Tax Rule:</label>
		    <select name="details_taxrule">
			<option>Connecticut - 6%</option>
		    </select>
		    <label for="details_retailprice">Retail Price:</label>
		    <div class="input-prepend">
			<span class="add-on">$</span><input type="text" name="details_retailprice" value="" />
		    </div>
		    <label for="details_unitprice">Unit Price:</label>
		    <div class="fluid-row">
			<div class="input-prepend span6">
			    <span class="add-on">$</span><input type="text" name="details_unitprice" value="" class="span8" />
			</div>
			<div class="input-prepend span6">
			    <span class="add-on">per</span><input type="text" name="details_unitpriceper" value="" class="span8" />
			</div>
		    </div>
		</div>
		<div class="span2">
		    Tags
		</div>
	    </div>
	    
	    <div class="row-fluid">
		<div class="span4">
		    <h5>Package Info</h5>
		    <div class="fluid-row">
			<div class="span3 input-append">
			    <input type="text" name="details_unitpriceper" value="" class="span6" />
			    <span class="add-on">IN</span>
			</div>
			<div class="span3 input-append">
			    <input type="text" name="details_unitpriceper" value="" class="span6" />
			    <span class="add-on">IN</span>
			</div>
			<div class="span3 input-append">
			    <input type="text" name="details_unitpriceper" value="" class="span6" />
			    <span class="add-on">IN</span>
			</div>
			<div class="span3 input-append">
			    <input type="text" name="details_unitpriceper" value="" class="span6" />
			    <span class="add-on">lbs</span>
			</div>
		    </div>
		</div>
		<div class="span8">
		    <h5>Options</h5>
		</div>
	    </div>
	    
	    <h5>Details</h5>
	    
	    <h5>Attachments</h5>
	</form>
    
    </div>
    <div class="tab-pane" id="images">...</div>
    <div class="tab-pane" id="attributes">...</div>
    <div class="tab-pane" id="discounts">...</div>
</div>