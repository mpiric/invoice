<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>



<div class="row" ng-controller="productCreateCtrl">

<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">{{heading}} Product</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>
	
<div style="margin:10px" id="validation_err">
	
</div>

<br>
	
	<form action="javascript:void(0)" name="create_product" novalidate="novalidate" ng-submit="createProduct(create_product)" method="post" enctype="multipart/form-data" >

	<div class="col-md-6">

		<fieldset>
			<legend>
				Product Details
			</legend>

			<!-- <ui-select multiple ng-model="multipleDemo.selectedBranchL" theme="bootstrap" ng-disabled="disabled">
				<ui-select-match placeholder="Select branch...">
					{{$item.name}}
				</ui-select-match>
				<ui-select-choices repeat="branch in branch_list | propsFilter: {name: $select.search, age: $select.search}">
					<div ng-bind-html="branch.name | highlight: $select.search"></div>
					<small> email: {{branch.address}} </small>
				</ui-select-choices>
			</ui-select> -->

			<div class="form-group" ng-show="false"><!-- ng-show="show_branch_dd" -->
				<label for="branch_id"  class="control-label">Branch: <span class="symbol required"></span></label>
				 <!-- <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" multiple>

    			</select> -->
    			
	    			<ui-select multiple ng-model="selectedBranch" name="selectedBranch" theme="bootstrap" ng-disabled="disabled">
						<ui-select-match placeholder="Select branch..." >
							{{$item.name}} 
						</ui-select-match>
						<ui-select-choices repeat="branch in branch_list | propsFilter: {name: $select.search, address: $select.search}">
							<div ng-bind-html="branch.name | highlight: $select.search"></div>
							<small> Address: {{branch.address}}</small>
						</ui-select-choices>
					</ui-select>
				
			</div>

			<div class="form-group">
				<label for="product_category_id"  class="control-label">Product Category: <span class="symbol required"></span></label>
				 <select class="form-control" name="product_category_id" ng-options="item as item.name for item in product_cat_list track by item.product_category_id" ng-model="product_category_id" required>
				 <option value="">Select Product Category</option>
    			</select>
    			<div class="has-error" ng-show="create_product.$submitted || create_product.product_category_id.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_product.product_category_id.$error.required">Product Category is required.</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="name"  class="control-label">Product name: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="name" id="name" placeholder="Product Name" name="name" ng-change="checkName(name)" required>

			    <div class="has-error" ng-show="create_product.$submitted || create_product.name.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_product.name.$error.required">Product Name is required.</span>
			    </div>
			    <div class="has-error" ng-show="create_product.$submitted || create_product.name.$touched">
		            <span class="error text-small block ng-scope" ng-show="checkN">Product Name is already in use.</span>
		        </div>
			</div>

			<div class="form-group">
			<label for="product_code"  class="control-label">Product Code: </label>
				<input type="text" class="form-control" ng-model="product_code" id="product_code" placeholder="Product Code" name="product_code" ng-change="checkCode(product_code)">
				<div class="has-error" ng-show="create_product.$submitted || create_product.product_code.$touched">
		            <span class="error text-small block ng-scope" ng-show="checkC">Product code is already in use.</span>
		        </div>
			</div>

			<div class="form-group">
			<label for="unit"  class="control-label">Product Unit: <span class="symbol required"></span></label>
				<!-- <input type="text" class="form-control" ng-model="unit" id="unit" placeholder="Product Unit" name="unit" required > -->
				<select class="form-control" name="unit" ng-options="item as item.value for item in unit_list track by item.key" ng-model="unit">
				 <!-- <option value="">Select Unit</option> -->
				  <option value="" ng-if="false"></option> 
    			</select>

			    <!-- <div class="has-error" ng-show="create_product.$submitted || create_product.unit.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_product.unit.$error.required">Product Unit  is required.</span>
			    </div> -->
			</div>

			<div class="form-group">
			<label for="description"  class="control-label">Product Description: <span class="symbol required"></span></label>
				<input type="description" class="form-control" ng-model="description" id="description" placeholder="Product Description" name="description" required >

			    <div class="has-error" ng-show="create_product.$submitted || create_product.description.$touched">
				  <span class="error text-small block ng-scope" ng-show="create_product.description.$error.required">Description is required.</span>
				  <span class="error text-small block ng-scope" ng-show="create_product.description.$error.email">Not a valid description.</span>
				</div>

			</div>

			<div class="form-group">
			<label for="price"  class="control-label">Product Price: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="price" ng-pattern="/^\d{0,9}(\.\d{1,9})?$/" id="price" placeholder="Product Price" name="price" required >

			    <div class="has-error" ng-show="create_product.$submitted || create_product.price.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_product.price.$error.required">Product Price is required.</span>
			      <span class="error text-small block ng-scope" ng-show="create_product.price.$error.pattern">Not a valid number!</span>
			    </div>
			</div>

		</fieldset>

	</div>	

	<div class="col-md-6">
		<fieldset>
			<legend>Product Image</legend>

			<div class="form-group">
				<label for="image"  class="control-label">Product Image: </label>
					<input type="file" class="form-control" ng-model="image" id="image" placeholder="Product Image" name="image" accept="image/*" image="image" file-model="image" ng-src="{{image_path}}" >
					<img ng-src="{{image_path}}" ng-model="image_path" width="225px" height="275px">			        
			</div>
		</fieldset>

		<button type="submit" class="btn btn-primary" ng-disabled="create_product.$invalid || checkN || checkC" style="float:right" >Submit</button>

	</div>



</form>
</div>

<!-- /// controller:  'SelectCtrl' -  localtion: assets/js/controllers/selectCtrl.js /// -->
				<!-- <form ng-controller="SelectCtrl">
					
					<div class="col-sm-6">
						<div class="panel panel-transparent">
							<div class="panel-body">								
								
								<div class="form-group">
									<label>
										Array of objects
									</label>
									<ui-select multiple ng-model="multipleDemo.selectedPeople" theme="bootstrap" ng-disabled="disabled">
										<ui-select-match placeholder="Select person...">
											{{$item.name}} &lt;{{$item.email}}&gt;
										</ui-select-match>
										<ui-select-choices repeat="person in people | propsFilter: {name: $select.search, age: $select.search}">
											<div ng-bind-html="person.name | highlight: $select.search"></div>
											<small> email: {{person.email}}
												age: <span ng-bind-html="''+person.age | highlight: $select.search"></span> </small>
										</ui-select-choices>
									</ui-select>
								</div>
							</div>
						</div>
					</div>
				</form> -->

